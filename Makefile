# =============================================================================
# Cookies Manager CRM - Docker Environment Management
# =============================================================================

.PHONY: help build up down restart logs shell clean
.DEFAULT_GOAL := help

# Environment variables
ENV ?= local
COMPOSE_FILE := docker-compose.$(ENV).yml

# Colors for output
GREEN := \033[0;32m
YELLOW := \033[1;33m
RED := \033[0;31m
NC := \033[0m # No Color

help: ## Show this help message
	@echo "$(GREEN)Cookies Manager CRM - Docker Environment Management$(NC)"
	@echo ""
	@echo "Usage: make [target] [ENV=environment]"
	@echo ""
	@echo "Environments:"
	@echo "  ENV=local     Local development (default)"
	@echo "  ENV=staging   Staging environment"
	@echo "  ENV=production Production environment"
	@echo ""
	@echo "Targets:"
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  $(GREEN)%-15s$(NC) %s\n", $$1, $$2}' $(MAKEFILE_LIST)

# =============================================================================
# Environment Management
# =============================================================================

local: ## Set environment to local
	@$(MAKE) ENV=local

staging: ## Set environment to staging
	@$(MAKE) ENV=staging

production: ## Set environment to production
	@$(MAKE) ENV=production

# =============================================================================
# Docker Operations
# =============================================================================

build: ## Build Docker images for current environment
	@echo "$(YELLOW)Building Docker images for $(ENV) environment...$(NC)"
	docker-compose -f $(COMPOSE_FILE) build

up: ## Start services for current environment
	@echo "$(YELLOW)Starting $(ENV) environment...$(NC)"
	docker-compose -f $(COMPOSE_FILE) up -d

down: ## Stop services for current environment
	@echo "$(YELLOW)Stopping $(ENV) environment...$(NC)"
	docker-compose -f $(COMPOSE_FILE) down

restart: ## Restart services for current environment
	@echo "$(YELLOW)Restarting $(ENV) environment...$(NC)"
	docker-compose -f $(COMPOSE_FILE) restart

logs: ## Show logs for current environment
	docker-compose -f $(COMPOSE_FILE) logs -f

shell: ## Open shell in app container for current environment
	docker-compose -f $(COMPOSE_FILE) exec app sh

# =============================================================================
# Database Operations
# =============================================================================

db-shell: ## Open PostgreSQL shell for current environment
	docker-compose -f $(COMPOSE_FILE) exec pgsql psql -U laravel -d laravel

db-migrate: ## Run database migrations for current environment
	docker-compose -f $(COMPOSE_FILE) exec app php artisan migrate

db-seed: ## Seed database for current environment
	docker-compose -f $(COMPOSE_FILE) exec app php artisan db:seed

db-fresh: ## Fresh database migration and seeding for current environment
	docker-compose -f $(COMPOSE_FILE) exec app php artisan migrate:fresh --seed

# =============================================================================
# Laravel Operations
# =============================================================================

artisan: ## Run artisan command (usage: make artisan cmd="list")
	docker-compose -f $(COMPOSE_FILE) exec app php artisan $(cmd)

cache-clear: ## Clear all Laravel caches for current environment
	docker-compose -f $(COMPOSE_FILE) exec app php artisan cache:clear
	docker-compose -f $(COMPOSE_FILE) exec app php artisan config:clear
	docker-compose -f $(COMPOSE_FILE) exec app php artisan route:clear
	docker-compose -f $(COMPOSE_FILE) exec app php artisan view:clear

cache-optimize: ## Optimize Laravel caches for current environment
	docker-compose -f $(COMPOSE_FILE) exec app php artisan config:cache
	docker-compose -f $(COMPOSE_FILE) exec app php artisan route:cache
	docker-compose -f $(COMPOSE_FILE) exec app php artisan view:cache

# =============================================================================
# Development Operations
# =============================================================================

install-deps: ## Install PHP and Node dependencies
	docker-compose -f $(COMPOSE_FILE) exec app composer install
	docker-compose -f $(COMPOSE_FILE) exec app pnpm install

build-assets: ## Build frontend assets for current environment
	docker-compose -f $(COMPOSE_FILE) exec app pnpm build

watch-assets: ## Watch and build frontend assets (local only)
	@if [ "$(ENV)" = "local" ]; then \
		docker-compose -f $(COMPOSE_FILE) exec app pnpm dev; \
	else \
		echo "$(RED)Asset watching is only available in local environment$(NC)"; \
	fi

# =============================================================================
# Utility Operations
# =============================================================================

status: ## Show status of services for current environment
	@echo "$(YELLOW)Status for $(ENV) environment:$(NC)"
	docker-compose -f $(COMPOSE_FILE) ps

clean: ## Clean up Docker resources (use with caution)
	@echo "$(RED)Cleaning up Docker resources...$(NC)"
	docker system prune -f
	docker volume prune -f

clean-all: ## Clean up all Docker resources including volumes (use with extreme caution)
	@echo "$(RED)Cleaning up ALL Docker resources including volumes...$(NC)"
	@read -p "Are you sure? This will delete ALL Docker data! [y/N] " -n 1 -r; \
	if [[ $$REPLY =~ ^[Yy]$$ ]]; then \
		docker system prune -a -f --volumes; \
	else \
		echo "Cleanup cancelled."; \
	fi

# =============================================================================
# Production Operations
# =============================================================================

deploy: ## Deploy to production (requires proper setup)
	@if [ "$(ENV)" = "production" ]; then \
		echo "$(YELLOW)Deploying to production...$(NC)"; \
		docker-compose -f $(COMPOSE_FILE) up -d --build; \
	else \
		echo "$(RED)Deploy command is only available for production environment$(NC)"; \
		echo "Use: make production deploy"; \
	fi

backup: ## Create database backup for current environment
	@echo "$(YELLOW)Creating database backup for $(ENV) environment...$(NC)"
	docker-compose -f $(COMPOSE_FILE) exec pgsql pg_dump -U laravel laravel > backup_$(ENV)_$(shell date +%Y%m%d_%H%M%S).sql

# =============================================================================
# Health Checks
# =============================================================================

health: ## Check health of services for current environment
	@echo "$(YELLOW)Health check for $(ENV) environment:$(NC)"
	@if [ "$(ENV)" = "local" ]; then \
		curl -f http://localhost:8000/health || echo "$(RED)Health check failed$(NC)"; \
	elif [ "$(ENV)" = "staging" ]; then \
		curl -f http://localhost:8001/health || echo "$(RED)Health check failed$(NC)"; \
	elif [ "$(ENV)" = "production" ]; then \
		curl -f http://localhost/health || echo "$(RED)Health check failed$(NC)"; \
	fi

# =============================================================================
# Quick Start Commands
# =============================================================================

dev: ## Quick start for local development
	@$(MAKE) local up
	@$(MAKE) local install-deps
	@$(MAKE) local build-assets
	@echo "$(GREEN)Local development environment ready!$(NC)"
	@echo "App: http://localhost:8000"
	@echo "Database: localhost:5432"
	@echo "Redis: localhost:6379"

staging-setup: ## Quick start for staging environment
	@$(MAKE) staging up
	@$(MAKE) staging cache-optimize
	@echo "$(GREEN)Staging environment ready!$(NC)"
	@echo "App: http://localhost:8001"

prod-setup: ## Quick start for production environment
	@$(MAKE) production up
	@$(MAKE) production cache-optimize
	@echo "$(GREEN)Production environment ready!$(NC)"
	@echo "App: http://localhost" 