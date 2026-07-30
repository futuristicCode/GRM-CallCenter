#!/bin/sh
set -eu

# =============================================================================
#  deploy.sh — Build & Deploy GRM sur Kubernetes
# =============================================================================
# Usage:
#   ./deploy.sh db              # Déployer PostgreSQL + Redis
#   ./deploy.sh app             # Builder + déployer l'app (web + worker)
#   ./deploy.sh app v1.2.3      # Builder avec un tag spécifique
#   ./deploy.sh all             # Tout déployer
#
# Variables d'environnement :
#   REGISTRY        Registre Docker (défaut: ghcr.io/votre-org)
#   IMAGE_TAG       Tag de l'image (défaut: git commit court + date)
#   KUBE_NAMESPACE  Namespace K8s (défaut: grm)
#   IMAGE_PULL_SECRET Nom du secret pour pull sur registry privée
# =============================================================================

# ─── Configuration ───────────────────────────────────────────────────────────
REGISTRY="${REGISTRY:-dssi04}"
KUBE_NAMESPACE="${KUBE_NAMESPACE:-grm}"
IMAGE_NAME="grm-app"

# Generer un tag unique : commit court + date
GIT_SHA="$(git rev-parse --short HEAD 2>/dev/null || echo 'unknown')"
BUILD_DATE="$(date +%Y%m%d-%H%M%S)"
IMAGE_TAG="${IMAGE_TAG:-${BUILD_DATE}-${GIT_SHA}}"

FULL_IMAGE="${REGISTRY}/${IMAGE_NAME}:${IMAGE_TAG}"
FULL_IMAGE_LATEST="${REGISTRY}/${IMAGE_NAME}:latest"

# ─── Fonctions ───────────────────────────────────────────────────────────────

info()  { printf "\033[1;34m[INFO]\033[0m %s\n" "$*"; }
ok()    { printf "\033[1;32m[OK]\033[0m   %s\n" "$*"; }
err()   { printf "\033[1;31m[ERR]\033[0m  %s\n" "$*"; exit 1; }

wait_rollout() {
  local deploy="$1" ns="$2"
  info "Waiting for rollout of ${deploy}..."
  kubectl rollout status "deployment/${deploy}" -n "${ns}" --timeout=180s && \
    ok "${deploy} rolled out successfully" || \
    err "${deploy} rollout failed — check 'kubectl logs deployment/${deploy} -n ${ns}'"
}

build_image() {
  info "Building image: ${FULL_IMAGE}"
  docker build \
    --platform linux/amd64 \
    --build-arg APP_ENV=production \
    --build-arg BUILD_DATE="${BUILD_DATE}" \
    -t "${FULL_IMAGE}" \
    -t "${FULL_IMAGE_LATEST}" \
    .

  info "Pushing image..."
  docker push "${FULL_IMAGE}"
  docker push "${FULL_IMAGE_LATEST}"
  ok "Image pushed: ${FULL_IMAGE}"
}

deploy_app_manifests() {
  info "Applying Kubernetes manifests..."

  kubectl apply -f k8s/app/namespace.yaml
  kubectl apply -f k8s/app/configmap.yaml
  kubectl apply -f k8s/app/secret.yaml
  kubectl apply -f k8s/app/pvc-app.yaml
  kubectl apply -f k8s/app/deployment-app.yaml
  kubectl apply -f k8s/app/deployment-worker.yaml
  kubectl apply -f k8s/app/deployment-scheduler.yaml
  kubectl apply -f k8s/app/service.yaml
  kubectl apply -f k8s/app/ingress.yaml
}

update_image_on_deployments() {
  local ns="$1" image="$2"

  info "Updating image on deployments..."

  kubectl set image deployment/grm-app \
    "app=${image}" -n "${ns}" --record

  kubectl set image deployment/grm-worker \
    "worker=${image}" -n "${ns}" --record

  kubectl set image deployment/grm-scheduler \
    "scheduler=${image}" -n "${ns}" --record

  ok "Image set to ${image} on all deployments"
}

restart_deployments() {
  local ns="$1"

  info "Restarting deployments to pick up new image..."
  kubectl rollout restart deployment/grm-app -n "${ns}"
  kubectl rollout restart deployment/grm-worker -n "${ns}"
  kubectl rollout restart deployment/grm-scheduler -n "${ns}"
}

wait_all_rollouts() {
  local ns="$1"

  wait_rollout "grm-app" "${ns}"
  wait_rollout "grm-worker" "${ns}"
  wait_rollout "grm-scheduler" "${ns}"
}

# ─── Commandes ───────────────────────────────────────────────────────────────

case "${1:-all}" in

  db)
    echo "═══════════════════════════════════════════════════════════════════"
    echo "  Database — PostgreSQL + Redis"
    echo "═══════════════════════════════════════════════════════════════════"
    kubectl apply -f k8s/db/namespace.yaml
    kubectl apply -f k8s/db/configmap.yaml
    kubectl apply -f k8s/db/secret.yaml
    kubectl apply -f k8s/db/pvc-postgres.yaml
    kubectl apply -f k8s/db/deployment-postgres.yaml
    kubectl apply -f k8s/db/deployment-redis.yaml
    kubectl apply -f k8s/db/service-postgres.yaml
    kubectl apply -f k8s/db/service-redis.yaml

    ok "Database manifests applied. Waiting for pods..."
    kubectl wait --for=condition=ready pod \
      -l "app.kubernetes.io/component in (postgres,redis)" \
      -n grm --timeout=180s 2>/dev/null || true

    kubectl get pods -n grm -l "app.kubernetes.io/component in (postgres,redis)"
    ;;

  build)
    echo "═══════════════════════════════════════════════════════════════════"
    echo "  Build — ${FULL_IMAGE}"
    echo "═══════════════════════════════════════════════════════════════════"
    build_image
    ;;

  app)
    echo "═══════════════════════════════════════════════════════════════════"
    echo "  Deploy App — ${FULL_IMAGE}"
    echo "═══════════════════════════════════════════════════════════════════"

    build_image
    deploy_app_manifests

    # Injecter le tag d'image dans les deployments
    update_image_on_deployments "${KUBE_NAMESPACE}" "${FULL_IMAGE}"

    restart_deployments "${KUBE_NAMESPACE}"
    wait_all_rollouts "${KUBE_NAMESPACE}"

    echo ""
    info "App deployed:"
    kubectl get pods -n "${KUBE_NAMESPACE}" \
      -l "app.kubernetes.io/name=grm"
    ;;

  push)
    echo "═══════════════════════════════════════════════════════════════════"
    echo "  Push existing local image"
    echo "═══════════════════════════════════════════════════════════════════"
    docker push "${FULL_IMAGE}"
    docker push "${FULL_IMAGE_LATEST}"
    ;;

  rollback)
    echo "═══════════════════════════════════════════════════════════════════"
    echo "  Rollback — restore previous deployment"
    echo "═══════════════════════════════════════════════════════════════════"
    kubectl rollout undo deployment/grm-app -n "${KUBE_NAMESPACE}"
    kubectl rollout undo deployment/grm-worker -n "${KUBE_NAMESPACE}"
    kubectl rollout undo deployment/grm-scheduler -n "${KUBE_NAMESPACE}"
    wait_all_rollouts "${KUBE_NAMESPACE}"
    ;;

  status)
    echo "═══════════════════════════════════════════════════════════════════"
    echo "  Status — Namespace: ${KUBE_NAMESPACE}"
    echo "═══════════════════════════════════════════════════════════════════"
    kubectl get all -n "${KUBE_NAMESPACE}"
    echo ""
    kubectl get ingress -n "${KUBE_NAMESPACE}"
    ;;

  all)
    echo "═══════════════════════════════════════════════════════════════════"
    echo "  Full Deploy — Database + App"
    echo "═══════════════════════════════════════════════════════════════════"

    if ! kubectl get namespace "${KUBE_NAMESPACE}" 2>/dev/null; then
      $0 db
    else
      kubectl get pods -n "${KUBE_NAMESPACE}" -l "app.kubernetes.io/component in (postgres,redis)" 2>/dev/null \
        && info "Database already deployed, skipping." \
        || $0 db
    fi

    echo ""
    $0 app "${2:-}"
    ;;

  *)
    echo "Usage: ${0##*/} [db|build|app|push|rollback|status|all]"
    echo ""
    echo "  db               Deploy PostgreSQL + Redis"
    echo "  build            Build and push image only"
    echo "  app              Build + push + deploy application"
    echo "  push             Push existing local image to registry"
    echo "  rollback         Rollback to previous deployment"
    echo "  status           Show all K8s resources"
    echo "  all [tag]        Deploy everything (default)"
    echo ""
    echo "Examples:"
    echo "  REGISTRY=ghcr.io/my-org ./deploy.sh all"
    echo "  IMAGE_TAG=v1.2.3 ./deploy.sh build"
    echo "  IMAGE_PULL_SECRET=my-registry-key ./deploy.sh app"
    exit 1
    ;;
esac
