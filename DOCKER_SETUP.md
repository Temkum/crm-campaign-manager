# 🐳 Docker Setup Guide - Cookies Manager CRM

This guide covers the Docker setup for local development, staging, and production environments.

## 🚀 Quick Start

### Local Development
```bash
# Start local environment
make dev

# Or manually:
make local up
make local install-deps
make local build-assets
```

### Staging Environment
```bash
# Start staging environment
make staging-setup

# Or manually:
make staging up
make staging cache-optimize
```

### Production Environment
```bash
# Start production environment
make prod-setup

# Or manually:
make production up
make production cache-optimize
```

## 📁 Environment Files

### Environment Templates
- `env.template` - Base environment configuration
- `env.local.example` - Local development settings
- `.env` - Your actual environment file (create from template)

### Setup Environment
```bash
# Copy template to create your environment file
cp env.template .env

# For local development
cp env.local.example .env.local

# Edit the file with your specific values
nano .env
```

## 🐙 Docker Compose Files

| Environment | File | Port | Purpose |
|-------------|------|------|---------|
| **Local** | `docker-compose.local.yml` | 8000 | Development with hot reload |
| **Staging** | `docker-compose.staging.yml` | 8001 | Pre-production testing |
| **Production** | `docker-compose.prod.yml` | 80/443 | Live production |

## 🛠️ Available Make Commands

### Environment Management
```bash
make local          # Set environment to local
make staging        # Set environment to staging  
make production     # Set environment to production
```

### Docker Operations
```bash
make build          # Build Docker images
make up             # Start services
make down           # Stop services
make restart        # Restart services
make logs           # Show logs
make shell          # Open shell in app container
```

### Database Operations
```bash
make db-shell       # Open PostgreSQL shell
make db-migrate     # Run migrations
make db-seed        # Seed database
make db-fresh       # Fresh migration + seeding
```

### Laravel Operations
```bash
make artisan cmd="list"  # Run artisan command
make cache-clear         # Clear all caches
make cache-optimize      # Optimize caches
```

### Development Operations
```bash
make install-deps        # Install dependencies
make build-assets        # Build frontend assets
make watch-assets        # Watch assets (local only)
```

### Utility Operations
```bash
make status              # Show service status
make health              # Health check
make clean               # Clean Docker resources
make backup              # Database backup
```

## 🔧 Environment-Specific Configurations

### Local Development
- **Debug**: Enabled
- **Cache**: File-based (faster for development)
- **Queue**: Synchronous
- **Assets**: Hot reload with Vite
- **Port**: 8000

### Staging
- **Debug**: Disabled
- **Cache**: Redis
- **Queue**: Redis with 2 workers
- **Assets**: Built and optimized
- **Port**: 8001

### Production
- **Debug**: Disabled
- **Cache**: Redis with OPcache
- **Queue**: Redis with 4 workers + Horizon
- **Assets**: Built and optimized
- **Port**: 80/443 (with SSL)

## 🚀 Deployment

### Automated Deployment
```bash
# Deploy to production
./scripts/deploy.sh production

# Deploy to staging
./scripts/deploy.sh staging
```

### Manual Deployment
```bash
# Production
make production deploy

# Staging
make staging up
make staging cache-optimize
```

## 🔍 Health Checks

### Application Health
- **Endpoint**: `/health`
- **Checks**: Database, Redis, Storage
- **Response**: JSON with service status

### Service Health
```bash
# Check all services
make health

# Check specific environment
make production health
```

## 📊 Monitoring

### Service Status
```bash
# Show running services
make status

# Show logs
make logs
```

### Resource Usage
```bash
# Docker stats
docker stats

# Container logs
make logs
```

## 🛡️ Security Features

### Production Security
- **PHP**: OPcache enabled, error display disabled
- **Nginx**: Hidden files blocked, sensitive paths protected
- **Sessions**: Secure cookies, Redis storage
- **SSL**: HTTPS redirect, modern cipher suites

### Environment Isolation
- **Networks**: Separate networks for frontend/backend
- **Volumes**: Isolated data storage per environment
- **Secrets**: Docker secrets for sensitive data

## 🔧 Troubleshooting

### Common Issues

#### Port Already in Use
```bash
# Check what's using the port
sudo lsof -i :8000

# Kill the process or change port in docker-compose file
```

#### Database Connection Issues
```bash
# Check database status
make db-shell

# Restart database service
make restart
```

#### Asset Build Issues
```bash
# Clear node modules and rebuild
make local shell
rm -rf node_modules
pnpm install
pnpm build
```

#### Cache Issues
```bash
# Clear all caches
make cache-clear

# Rebuild caches
make cache-optimize
```

### Debug Mode
```bash
# Enable debug in local environment
APP_DEBUG=true make local up

# Check logs
make logs
```

## 📚 Additional Resources

### Documentation
- [Laravel Documentation](https://laravel.com/docs)
- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Reference](https://docs.docker.com/compose/)

### Configuration Files
- `docker/prod/` - Production configurations
- `docker/staging/` - Staging configurations
- `config/environments.php` - Environment-specific settings

### Scripts
- `scripts/deploy.sh` - Production deployment script
- `Makefile` - Environment management commands

## 🤝 Contributing

When adding new services or configurations:

1. **Update all environments** - Local, staging, and production
2. **Add health checks** - Ensure service monitoring
3. **Update documentation** - Keep this guide current
4. **Test thoroughly** - Verify across all environments

## 📞 Support

For issues or questions:
1. Check the troubleshooting section
2. Review Docker and Laravel logs
3. Check service health status
4. Consult the deployment guide

---

**Happy Coding! 🚀** 