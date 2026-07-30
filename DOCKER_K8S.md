# GRM — Documentation Docker & Kubernetes

## Table des matières

1. [Aperçu du projet](#1-aperçu-du-projet)
2. [Architecture de l'image Docker](#2-architecture-de-limage-docker)
3. [Structure du projet](#3-structure-du-projet)
4. [Build de l'image](#4-build-de-limage)
5. [Docker Compose (développement)](#5-docker-compose-développement)
6. [Kubernetes (production)](#6-kubernetes-production)
7. [Configuration & Secrets](#7-configuration--secrets)
8. [Entrypoint & cycle de vie](#8-entrypoint--cycle-de-vie)
9. [Monitoring & probes](#9-monitoring--probes)
10. [Commandes utiles](#10-commandes-utiles)
11. [Dépannage](#11-dépannage)

---

## 1. Aperçu du projet

**GRM** (Gestion des Réclamations) est une application Laravel 13 pour gérer les réclamations clients.

| Composant     | Version |
|---------------|---------|
| PHP           | 8.3     |
| Laravel       | 13.8    |
| Node.js       | 22      |
| PostgreSQL    | 16      |
| Redis         | 7       |
| TailwindCSS   | 3.x     |
| Alpine.js     | 3.x     |
| Vite          | 8.x     |

**Stack de déploiement :**

```
┌─────────────────────────────────────────────────────────┐
│                    Kubernetes (K8s)                      │
│                                                         │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │  grm-app     │  │ grm-worker   │  │ grm-postgres │  │
│  │  (nginx +    │  │ (queue:work) │  │ (PostgreSQL  │  │
│  │   php-fpm)   │  │              │  │  16-alpine)  │  │
│  │  2 replicas  │  │  1 replica   │  │  1 replica   │  │
│  │  Port 80     │  │              │  │  Port 5432   │  │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘  │
│         │                 │                 │           │
│  ┌──────┴─────────────────┴─────────────────┴───────┐   │
│  │                  Réseau grm-net                   │   │
│  └───────────────────────────────────────────────────┘   │
│                                                         │
│  ┌──────────────┐  ┌──────────────┐                     │
│  │  grm-redis   │  │ grm-ingress  │                     │
│  │  (Redis 7)   │  │  (NGINX +    │                     │
│  │  Port 6379   │  │   TLS)       │                     │
│  └──────────────┘  └──────────────┘                     │
└─────────────────────────────────────────────────────────┘
```

---

## 2. Architecture de l'image Docker

L'image est construite en **3 étapes multi-stage** pour optimiser la taille finale (~714 MB) :

### Stage 1 — `frontend` (Node 22 Alpine)

```
node:22-alpine
    │
    ├── npm ci (install dépendances Node)
    ├── Copie resources/ (Blade, CSS, JS)
    └── npm run build → public/build/
```

**Rôle :** Compiler les assets Vite (CSS Tailwind, JS Alpine.js, Chart.js) en version production.

### Stage 2 — `php-deps` (PHP 8.3 FPM Alpine)

```
php:8.3-fpm-alpine
    │
    ├── Composer (copié depuis composer:2)
    ├── apk add (libzip, oniguruma, libpng, icu, postgresql-dev, etc.)
    ├── docker-php-ext-install (pdo_pgsql, pgsql, zip, bcmath, mbstring, intl, opcache, pcntl)
    ├── Compilation manuelle de phpredis 6.1.0 (via ADD + tar + phpize)
    ├── composer install --no-dev (dépendances PHP)
    ├── Copie du code source
    └── composer dump-autoload --optimize
```

**Rôle :** Installer les dépendances PHP et compiler les extensions nécessaires.

> **Note :** phpredis est compilé depuis GitHub car PECL est unreliable en Alpine.
> Le tarball est téléchargé via `ADD` (bypass DNS Docker via GitHub directement).

### Stage 3 — `production` (PHP 8.3 FPM Alpine)

```
php:8.3-fpm-alpine (image finale)
    │
    ├── Utilisateur non-root : www (UID 1000, GID 1000)
    ├── Runtime deps : nginx, supervisor, curl, + extensions PHP
    ├── php-fpm config (socket Unix, clear_env=no, PM dynamic)
    ├── OPcache config (JIT, 256MB memory, validate_timestamps=0)
    ├── Upload config (64M max, 512M memory_limit)
    ├── Copie depuis stage 2 : /app → /var/www/html
    ├── Copie depuis stage 1 : public/build/ → public/build/
    ├── Copie config : nginx.conf, supervisord.conf, entrypoint.sh
    ├── Permissions : chown www:www sur storage/ et bootstrap/cache/
    └── EXPOSE 80 + ENTRYPOINT
```

### Résultat

```
Image finale : ~714 MB
├── PHP 8.3.21 + 12 extensions
├── nginx (user www)
├── supervisord
├── Code Laravel optimisé
├── Assets Vite compilés
└── Config production (OPcache JIT, php-fpm tuning)
```

---

## 3. Structure du projet

```
GRM/
├── Dockerfile                      # Multi-stage build (3 stages)
├── .dockerignore                   # Fichiers exclus du build context
├── .env                            # Variables d'env (dev local)
├── .env.docker                     # Variables d'env (Docker)
├── docker-compose.yml              # App + Worker (réseau externe)
├── docker-compose.db.yml           # PostgreSQL + Redis
├── deploy.sh                       # Script deploy K8s
│
├── docker/
│   ├── entrypoint.sh               # Attente DB → cache → migrate → supervisord
│   ├── nginx.conf                  # Config nginx serveur
│   └── supervisord.conf            # php-fpm + nginx (2 process)
│
├── k8s/
│   ├── db/                         # Manifests base de données
│   │   ├── namespace.yaml          # Namespace grm
│   │   ├── configmap.yaml          # POSTGRES_DB, POSTGRES_USER
│   │   ├── secret.yaml             # POSTGRES_PASSWORD
│   │   ├── pvc-postgres.yaml       # PVC 5Gi
│   │   ├── deployment-postgres.yaml# PostgreSQL 16 (Recreate, healthcheck)
│   │   ├── deployment-redis.yaml   # Redis 7 (emptyDir)
│   │   ├── service-postgres.yaml   # ClusterIP:5432
│   │   └── service-redis.yaml      # ClusterIP:6379
│   │
│   └── app/                        # Manifests application
│       ├── namespace.yaml          # Namespace grm
│       ├── configmap.yaml          # Variables Laravel
│       ├── secret.yaml             # APP_KEY, DB_PASSWORD
│       ├── deployment-app.yaml     # 2 replicas, RollingUpdate
│       ├── deployment-worker.yaml  # queue:work, 1 replica
│       ├── service.yaml            # ClusterIP:80
│       └── ingress.yaml            # TLS + cert-manager
│
├── app/                            # Code Laravel
├── database/
│   ├── migrations/                 # 13 migrations
│   └── seeders/                    # 6 users, 8 clients, etc.
├── resources/                      # Blade, CSS, JS
├── routes/                         # web.php, api.php
└── public/                         # Point d'entrée HTTP
```

---

## 4. Build de l'image

### Build local

```bash
# Build simple
docker build -t grm-app:latest .

# Build avec tag
docker build -t grm-app:v1.0.0 .

# Build sans cache (rebuild complet)
docker build --no-cache -t grm-app:latest .

# Build pour architecture spécifique (K8s = linux/amd64)
docker build --platform linux/amd64 -t grm-app:latest .
```

### Test de l'image

```bash
# Lancer avec variables d'env (simule K8s)
docker run -d --name grm-test --network host \
  -e APP_KEY=base64:c5An6/FbK/jWRnNbHO2hVnFY4AbCxtXPS7ZbWP1IdnA= \
  -e APP_ENV=production \
  -e APP_DEBUG=false \
  -e APP_URL=http://localhost \
  -e DB_CONNECTION=pgsql \
  -e DB_HOST=127.0.0.1 \
  -e DB_PORT=5432 \
  -e DB_DATABASE=GRMdb \
  -e DB_USERNAME=devops \
  -e DB_PASSWORD=devops \
  -e SESSION_DRIVER=database \
  -e CACHE_STORE=database \
  -e QUEUE_CONNECTION=database \
  grm-app:latest

# Vérifier
curl -sL -o /dev/null -w "HTTP %{http_code}\n" http://localhost/
# → HTTP 200 → http://localhost/login

# Logs
docker logs grm-test

# Processus
docker exec grm-test ps aux
# → supervisord → php-fpm + nginx

# Nettoyage
docker stop grm-test && docker rm grm-test
```

### Push vers un registry

```bash
# Docker Hub
docker tag grm-app:latest utilisateur/grm-app:v1.0.0
docker push utilisateur/grm-app:v1.0.0

# Registry privé
docker tag grm-app:latest registry.example.com/grm-app:v1.0.0
docker push registry.example.com/grm-app:v1.0.0

# avec deploy.sh
REGISTRY=registry.example.com ./deploy.sh app v1.0.0
```

---

## 5. Docker Compose (développement)

### Architecture

```
docker-compose.db.yml              docker-compose.yml
┌────────────────────┐    grm-net   ┌────────────────────┐
│  postgres          │◄────────────►│  app               │
│  redis             │              │  worker            │
│  Volume: postgres  │              │  Volume: storage   │
│  Volume: redis     │              │  Volume: bootstrap │
└────────────────────┘              └────────────────────┘
```

### Démarrage

```bash
# 1. Démarrer la base de données (première fois)
docker compose -f docker-compose.db.yml up -d

# 2. Démarrer l'application
docker compose up -d --build

# Vérifier
docker compose ps
docker compose -f docker-compose.db.yml ps

# Logs
docker compose logs -f app
docker compose logs -f worker
```

### Arrêt

```bash
# Arrêter l'app (la DB continue)
docker compose down

# Tout arrêter y compris la DB
docker compose down
docker compose -f docker-compose.db.yml down

# ⚠️ Tout supprimer (données incluses)
docker compose -f docker-compose.db.yml down -v
```

### Volumes persistants

| Volume             | Contenu                            | Persistance |
|--------------------|------------------------------------|-------------|
| `grm_postgres-data`| Données PostgreSQL                 | ✅ Critique |
| `grm_redis-data`   | AOF Redis (appendonly)             | ✅ Important|
| `grm_app-storage`  | Uploads (pièces jointes), logs     | ✅ Critique |
| `grm_app-bootstrap`| Cache routes/config/views          | ✅ Recréé au démarrage |

### Fichiers d'environnement

| Fichier        | Usage                                    |
|----------------|------------------------------------------|
| `.env`         | Développement local (`php artisan serve`)|
| `.env.docker`  | Docker Compose (`env_file: .env.docker`) |

> **Important :** `.env.docker` est lu par Docker Compose. Les variables peuvent
> être surchargées dans `environment:` (priorité la plus haute).

---

## 6. Kubernetes (production)

### Prérequis

- Cluster K8s (minikube, EKS, GKE, AKS, etc.)
- `kubectl` configuré
- `cert-manager` installé (pour TLS)
- `nginx-ingress-controller` installé
- Registre d'images Docker (Docker Hub, ECR, GCR, Harbor, etc.)

### Architecture K8s

```
Namespace: grm
│
├── Database Layer
│   ├── grm-postgres (Deployment, 1 replica, Recreate strategy)
│   │   ├── grm-postgres-pvc (PVC 5Gi)
│   │   ├── grm-db-config (ConfigMap)
│   │   ├── grm-db-secret (Secret)
│   │   └── postgres-service (ClusterIP:5432)
│   │
│   └── grm-redis (Deployment, 1 replica)
│       ├── grm-redis-data (emptyDir)
│       └── redis-service (ClusterIP:6379)
│
├── App Layer
│   ├── grm-app (Deployment, 2 replicas, RollingUpdate)
│   │   ├── grm-config (ConfigMap)
│   │   ├── grm-secret (Secret)
│   │   ├── storage (emptyDir)
│   │   ├── bootstrap-cache (emptyDir)
│   │   └── grm-app service (ClusterIP:80)
│   │
│   ├── grm-worker (Deployment, 1 replica)
│   │   ├── Même ConfigMap/Secret
│   │   └── Mêmes volumes
│   │
│   └── grm-ingress (Ingress, TLS, cert-manager)
│       └── your-domain.com → grm-app:80
```

### Déploiement

```bash
# Déployer la base de données
./deploy.sh db

# Déployer l'application (build + push + apply)
./deploy.sh app v1.0.0

# Tout déployer
./deploy.sh all v1.0.0

# Vérifier
kubectl get pods -n grm
kubectl get svc -n grm
kubectl get ingress -n grm
```

### Stratégies de mise à jour

**App (grm-app) :**
- `RollingUpdate` avec `maxUnavailable: 0`, `maxSurge: 1`
- Zéro downtime : l'ancien pod reste actif jusqu'à ce que le nouveau soit prêt
- Probes : startup (10s), readiness (15s), liveness (30s)

**Worker (grm-worker) :**
- Pas de stratégie de mise à jour (1 replica)
- `terminationGracePeriodSeconds: 60` pour finir le job en cours
- `--max-time=3600` : redémarrage automatique toutes les heures

**PostgreSQL :**
- `Recreate` (pas de RollingUpdate pour une BDD)
- `terminationGracePeriodSeconds` pas spécifié (défaut 30s)

### Ressources

| Container    | CPU Request | CPU Limit | Memory Request | Memory Limit |
|-------------|-------------|-----------|----------------|--------------|
| grm-app     | 200m        | 1000m     | 256Mi          | 768Mi        |
| grm-worker  | 100m        | 500m      | 256Mi          | 512Mi        |
| grm-postgres| 100m        | 500m      | 256Mi          | 512Mi        |
| grm-redis   | 50m         | 200m      | 64Mi           | 128Mi        |

---

## 7. Configuration & Secrets

### Variables d'environnement (ConfigMap)

| Variable              | Valeur                  | Description                      |
|-----------------------|-------------------------|----------------------------------|
| `APP_NAME`            | GRM                     | Nom de l'application             |
| `APP_ENV`             | production              | Environnement                    |
| `APP_DEBUG`           | false                   | Mode debug désactivé             |
| `APP_URL`             | https://your-domain.com | URL publique                     |
| `DB_CONNECTION`       | pgsql                   | Driver PostgreSQL                |
| `DB_HOST`             | postgres-service        | Service K8s PostgreSQL           |
| `DB_PORT`             | 5432                    | Port PostgreSQL                  |
| `DB_DATABASE`         | GRMdb                   | Nom de la base                   |
| `DB_USERNAME`         | devops                  | Utilisateur PostgreSQL           |
| `REDIS_HOST`          | redis-service           | Service K8s Redis                |
| `SESSION_DRIVER`      | database                | Sessions en BDD                  |
| `QUEUE_CONNECTION`    | database                | Files en BDD                     |
| `CACHE_STORE`         | database                | Cache en BDD                     |
| `MAIL_MAILER`         | log                     | Mail en mode log                 |

### Secrets (Secret)

| Variable        | Valeur                                      |
|-----------------|---------------------------------------------|
| `APP_KEY`       | base64:c5An6/FbK/jWRnNbHO2hVnFY4Ab...     |
| `DB_PASSWORD`   | devops                                      |

> ⚠️ **Sécurité en production :**
> - Utiliser **Sealed Secrets**, **External Secrets Operator**, ou **Vault**
> - Ne jamais commiter de secrets en clair
> - Changer `APP_KEY` et `DB_PASSWORD` pour chaque environnement

### Fichiers de config

| Fichier               | Rôle                                              |
|-----------------------|---------------------------------------------------|
| `docker/nginx.conf`   | Serveur nginx (port 80, fastcgi, cache static)    |
| `docker/supervisord.conf` | Gestion process php-fpm + nginx               |
| `docker/entrypoint.sh`| Bootstrap Laravel (cache, migrate, supervisord)   |

---

## 8. Entrypoint & cycle de vie

L'entrypoint (`docker/entrypoint.sh`) gère le démarrage de chaque conteneur :

### App (nginx + php-fpm)

```
1. Attendre PostgreSQL (TCP check via PHP fsockopen)
2. Attendre Redis (TCP check via PHP fsockopen)
3. config:cache → route:cache → view:cache
4. migrate --force
5. Démarrer supervisord (php-fpm + nginx)
```

### Worker (queue:work)

```
1. Attendre PostgreSQL
2. Attendre Redis
3. config:cache → route:cache → view:cache
4. migrate --force
5. Exécuter : php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

### Entrypoint avec arguments

```sh
if [ $# -gt 0 ]; then
    exec "$@"    # Exécute la commande passée (worker)
fi

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf  # App
```

> Le worker passe sa commande via `command:` dans docker-compose/K8s.
> L'entrypoint détecte les arguments et exécute la commande au lieu de supervisord.

---

## 9. Monitoring & probes

### Probes HTTP (App)

| Probe     | Chemin | Intervalle | Delay  | Timeout | Threshold |
|-----------|--------|------------|--------|---------|-----------|
| Startup   | `/up`  | 5s         | 10s    | -       | 12        |
| Readiness | `/up`  | 10s        | 15s    | 5s      | 3         |
| Liveness  | `/up`  | 30s        | 30s    | 5s      | 3         |

**Route `/up`** : Route Laravel de health check (retourne 200 si l'app est prête).

### Probes exec (PostgreSQL)

| Probe     | Commande                                    | Intervalle |
|-----------|---------------------------------------------|------------|
| Readiness | `pg_isready -U devops -d GRMdb`            | 10s        |
| Liveness  | `pg_isready -U devops -d GRMdb`            | 30s        |

### Probes exec (Redis)

| Probe     | Commande            | Intervalle |
|-----------|---------------------|------------|
| Readiness | `redis-cli ping`    | 10s        |
| Liveness  | `redis-cli ping`    | 30s        |

### Logs

```bash
# Docker Compose
docker compose logs -f app
docker compose logs -f worker

# Kubernetes
kubectl logs -f deployment/grm-app -n grm
kubectl logs -f deployment/grm-worker -n grm

# Vérifier les logs NGINX dans le pod
kubectl exec -it <pod-app> -n grm -- cat /var/log/nginx/access.log
```

---

## 10. Commandes utiles

### Docker

```bash
# Build
docker build -t grm-app:latest .

# Run
docker run -d --name grm -p 8080:80 grm-app:latest

# Inspect
docker inspect grm-app:latest
docker exec grm ps aux

# Taille
docker images grm-app --format "{{.Repository}}:{{.Tag}} → {{.Size}}"

# Nettoyage
docker system prune -af
```

### Docker Compose

```bash
# Démarrer
docker compose -f docker-compose.db.yml up -d
docker compose up -d --build

# Logs
docker compose logs -f

# Status
docker compose ps

# Restart
docker compose restart app

# Arrêter
docker compose down
docker compose -f docker-compose.db.yml down

# Supprimer volumes
docker compose -f docker-compose.db.yml down -v
```

### Kubernetes

```bash
# Deploy
./deploy.sh db
./deploy.sh app v1.0.0
./deploy.sh all v1.0.0

# Status
kubectl get pods -n grm
kubectl get svc -n grm
kubectl get ingress -n grm
kubectl get pvc -n grm

# Logs
kubectl logs -f deployment/grm-app -n grm
kubectl logs -f deployment/grm-worker -n grm

# Shell
kubectl exec -it <pod> -n grm -- sh

# Rollout
kubectl rollout restart deployment/grm-app -n grm
kubectl rollout status deployment/grm-app -n grm

# Scale
kubectl scale deployment/grm-app --replicas=3 -n grm

# Delete
kubectl delete -f k8s/app/ -n grm
kubectl delete -f k8s/db/ -n grm
```

---

## 11. Dépannage

### Problèmes courants

| Symptôme | Cause probable | Solution |
|----------|---------------|----------|
| `HTTP 502` | php-fpm pas démarré | Vérifier logs supervisord |
| `Waiting for PostgreSQL...` | BDD pas accessible | Vérifier service `postgres-service` |
| `clear_env = no` manquant | Variables d'env vides | Vérifier `www.conf` php-fpm |
| `permission denied` socket | nginx pas user www | Vérifier `user www www;` dans nginx.conf |
| `Worker` affiche supervisord | `exec "$@"` manquant | Vérifier entrypoint.sh |
| Migration échoue | UUID mismatch | Vérifier `reclamation_id` dans les migrations |
| `ext-redis` non trouvé | Extension non compilée | Vérifier ADD redis.so dans Dockerfile |

### Vérifications rapides

```bash
# App
curl -sL -o /dev/null -w "%{http_code}" http://localhost:8080/
docker exec grm-app php /var/www/html/artisan about

# Worker
docker exec grm-worker ps aux
docker exec grm-worker php /var/www/html/artisan queue:work --once

# PostgreSQL
docker exec grm-postgres pg_isready -U devops -d GRMdb

# Redis
docker exec grm-redis redis-cli ping
```

### Debug en production (K8s)

```bash
# Logs du pod
kubectl logs <pod-name> -n grm --previous

# Shell dans le pod
kubectl exec -it <pod-name> -n grm -- sh

# Décrire le pod
kubectl describe pod <pod-name> -n grm

# Événements
kubectl get events -n grm --sort-by=.metadata.creationTimestamp
```
