#!/bin/bash

# =============================================================================
# Production Deployment Script for Cookies Manager CRM
# =============================================================================

set -e

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Configuration
ENVIRONMENT=${1:-production}
# Map environment name to compose file prefix
case "${ENVIRONMENT}" in
  production)
    COMPOSE_FILE="docker-compose.prod.yml";;
  staging)
    COMPOSE_FILE="docker-compose.staging.yml";;
  local)
    COMPOSE_FILE="docker-compose.local.yml";;
  *)
    COMPOSE_FILE="docker-compose.${ENVIRONMENT}.yml";;
esac

BACKUP_DIR="./backups"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Functions
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

check_environment() {
    if [ ! -f "$COMPOSE_FILE" ]; then
        log_error "Docker compose file $COMPOSE_FILE not found!"
        exit 1
    fi
    
    log_info "Deploying to $ENVIRONMENT environment"
}

check_dependencies() {
    log_info "Checking dependencies..."
    
    if ! command -v docker &> /dev/null; then
        log_error "Docker is not installed!"
        exit 1
    fi
    
    if ! command -v docker-compose &> /dev/null; then
        log_error "Docker Compose is not installed!"
        exit 1
    fi
    
    log_info "Dependencies check passed"
}

create_backup() {
    if [ "$ENVIRONMENT" = "production" ]; then
        log_info "Creating database backup..."
        mkdir -p "$BACKUP_DIR"
        
        # Create database backup
        docker-compose -f "$COMPOSE_FILE" exec -T pgsql pg_dump -U laravel laravel > "$BACKUP_DIR/db_backup_${ENVIRONMENT}_${TIMESTAMP}.sql" 2>/dev/null || {
            log_warn "Database backup failed, continuing with deployment..."
        }
        
        log_info "Backup created: $BACKUP_DIR/db_backup_${ENVIRONMENT}_${TIMESTAMP}.sql"
    fi
}

# Log in to Docker Hub if credentials are provided
if [ -n "$DOCKERHUB_USERNAME" ] && [ -n "$DOCKERHUB_PASSWORD" ]; then
    log_info "Logging in to Docker Hub"
    echo "$DOCKERHUB_PASSWORD" | docker login -u "$DOCKERHUB_USERNAME" --password-stdin
fi

deploy_application() {
    log_info "Starting deployment..."
    
    # Pull latest images if using remote images
    log_info "Pulling latest images..."
    docker-compose -f "$COMPOSE_FILE" pull || log_warn "Some images could not be pulled"
    
    # Build and start services
    log_info "Building and starting services..."
    docker-compose -f "$COMPOSE_FILE" up -d --build
    
    # Wait for services to be healthy
    log_info "Waiting for services to be healthy..."
    sleep 30
    
    # Check health status
    if docker-compose -f "$COMPOSE_FILE" ps | grep -q "unhealthy"; then
        log_error "Some services are unhealthy!"
        docker-compose -f "$COMPOSE_FILE" ps
        exit 1
    fi
    
    log_info "All services are healthy"
}

run_migrations() {
    log_info "Running database migrations..."
    
    # Wait for database to be ready
    log_info "Waiting for database to be ready..."
    sleep 10
    
    # Run migrations
    if docker-compose -f "$COMPOSE_FILE" exec -T app php artisan migrate --force; then
        log_info "Migrations completed successfully"
    else
        log_error "Migrations failed!"
        exit 1
    fi
}

optimize_application() {
    log_info "Optimizing application..."
    
    # Clear caches
    docker-compose -f "$COMPOSE_FILE" exec -T app php artisan cache:clear || log_warn "Cache clear failed"
    docker-compose -f "$COMPOSE_FILE" exec -T app php artisan config:clear || log_warn "Config clear failed"
    docker-compose -f "$COMPOSE_FILE" exec -T app php artisan route:clear || log_warn "Route clear failed"
    docker-compose -f "$COMPOSE_FILE" exec -T app php artisan view:clear || log_warn "View clear failed"
    
    # Optimize caches
    docker-compose -f "$COMPOSE_FILE" exec -T app php artisan config:cache || log_warn "Config cache failed"
    docker-compose -f "$COMPOSE_FILE" exec -T app php artisan route:cache || log_warn "Route cache failed"
    docker-compose -f "$COMPOSE_FILE" exec -T app php artisan view:cache || log_warn "View cache failed"
    
    log_info "Application optimization completed"
}

health_check() {
    log_info "Performing health check..."

    case "$ENVIRONMENT" in
      production)
        # No host port mapping; check from inside the app container
        for i in {1..30}; do
            if docker-compose -f "$COMPOSE_FILE" exec -T app curl -sf http://localhost/health >/dev/null 2>&1; then
                log_info "Application is healthy and responding (in-container)"
                return 0
            fi
            sleep 2
        done
        ;;
      staging)
        PORT=8001
        ;;
      local)
        PORT=8000
        ;;
      *)
        # Try to discover host-mapped port
        PORT=$(docker-compose -f "$COMPOSE_FILE" port app 80 | awk -F: '{print $2}')
        ;;
    esac

    if [ -n "$PORT" ]; then
        log_info "Checking http://localhost:$PORT/health"
        for i in {1..30}; do
            if curl -sf "http://localhost:$PORT/health" >/dev/null 2>&1; then
                log_info "Application is healthy and responding (host)"
                return 0
            fi
            sleep 2
        done
    fi

    log_error "Application health check failed!"
    exit 1
}

cleanup() {
    log_info "Cleaning up old images..."
    docker image prune -f || true
}

# Main deployment flow
main() {
    log_info "Starting deployment to $ENVIRONMENT environment"
    
    check_environment
    check_dependencies
    create_backup
    deploy_application
    run_migrations
    optimize_application
    health_check
    cleanup
    
    log_info "Deployment to $ENVIRONMENT completed successfully!"
    log_info "Application is now running and healthy"
}

# Handle script interruption
trap 'log_error "Deployment interrupted!"; exit 1' INT TERM

# Run main function
main "$@"