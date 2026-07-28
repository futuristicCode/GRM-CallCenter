#!/bin/sh
set -e

case "${1:-all}" in
  db)
    echo "=== Deploying Database (PostgreSQL + Redis) ==="
    kubectl apply -f k8s/db/namespace.yaml
    kubectl apply -f k8s/db/configmap.yaml
    kubectl apply -f k8s/db/secret.yaml
    kubectl apply -f k8s/db/pvc-postgres.yaml
    kubectl apply -f k8s/db/deployment-postgres.yaml
    kubectl apply -f k8s/db/deployment-redis.yaml
    kubectl apply -f k8s/db/service-postgres.yaml
    kubectl apply -f k8s/db/service-redis.yaml
    echo ""
    kubectl get pods -n grm -l "app.kubernetes.io/component in (postgres,redis)"
    ;;

  app)
    echo "=== Building image ==="
    REGISTRY="${REGISTRY:-docker.io}"
    IMAGE="${REGISTRY}/grm-app"
    TAG="${2:-latest}"
    docker build --platform linux/amd64 -t "${IMAGE}:${TAG}" .
    docker push "${IMAGE}:${TAG}"

    echo ""
    echo "=== Deploying App (web + worker) ==="
    kubectl apply -f k8s/app/namespace.yaml
    kubectl apply -f k8s/app/configmap.yaml
    kubectl apply -f k8s/app/secret.yaml
    kubectl apply -f k8s/app/deployment-app.yaml
    kubectl apply -f k8s/app/deployment-worker.yaml
    kubectl apply -f k8s/app/service.yaml
    kubectl apply -f k8s/app/ingress.yaml
    echo ""
    echo "=== Rolling restart ==="
    kubectl rollout restart deployment/grm-app -n grm
    kubectl rollout restart deployment/grm-worker -n grm
    echo ""
    kubectl get pods -n grm -l "app.kubernetes.io/component in (web,worker)"
    ;;

  all)
    $0 db
    echo ""
    echo "---"
    echo ""
    $0 app "${2:-latest}"
    ;;

  *)
    echo "Usage: ./deploy.sh [db|app|all] [tag]"
    echo ""
    echo "  db        Deploy PostgreSQL + Redis only"
    echo "  app [tag] Deploy Laravel app + worker (builds & pushes image)"
    echo "  all [tag] Deploy everything (default)"
    exit 1
    ;;
esac
