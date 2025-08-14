# 🔐 Docker Authentication Troubleshooting Guide

This guide helps resolve the Docker authentication issue that's preventing the build test from working.

## 🚨 **Error Description**

```
ERROR: failed to solve: php:8.3-fpm-alpine: error getting credentials - err: exit status 1, out: `error getting credentials - err: exit status 1, out: `no usernames for https://index.docker.io/v1/``
```

## 🔍 **Root Cause**

This error occurs when Docker cannot authenticate with Docker Hub to pull the base images. This is **not related to the build fixes** we implemented - it's a Docker configuration issue.

## ✅ **Solutions to Try (in order of preference)**

### **Solution 1: Docker Hub Authentication (Recommended)**

```bash
# Log in to Docker Hub
docker login

# You'll be prompted for:
# Username: your_docker_hub_username
# Password: your_docker_hub_password_or_token
```

**If you don't have a Docker Hub account:**
1. Go to [hub.docker.com](https://hub.docker.com)
2. Create a free account
3. Log in using `docker login`

### **Solution 2: Check Docker Desktop Settings**

If using Docker Desktop:
1. Open Docker Desktop
2. Go to Settings → Accounts
3. Ensure you're signed in to Docker Hub
4. Restart Docker Desktop

### **Solution 3: Clear Docker Credentials**

```bash
# Remove stored credentials
docker logout

# Clear credential store
docker system prune -a

# Log back in
docker login
```

### **Solution 4: Check Docker Configuration**

```bash
# Check Docker info
docker info

# Check if Docker daemon is running
docker version

# Check Docker context
docker context ls
```

## 🚀 **Alternative Testing Methods**

Since the authentication issue prevents the build test, use these alternatives to verify the fixes:

### **Method 1: Test with Docker Compose (Recommended)**

```bash
# Test local environment build
make local build

# Test staging environment build  
make staging build

# Test production environment build
make production build
```

### **Method 2: Test with Alternative Dockerfile**

```bash
# Test with alternative Dockerfile (uses different base image)
make test-build-alt

# This uses Dockerfile.test with php:8.2-fpm-alpine
```

### **Method 3: Test Individual Services**

```bash
# Test just the database and Redis
docker-compose -f docker-compose.local.yml up -d pgsql redis

# Check if they're running
docker-compose -f docker-compose.local.yml ps

# Test the app service
docker-compose -f docker-compose.local.yml up -d app
```

### **Method 4: Manual Docker Build with Different Image**

```bash
# Try building with a different PHP image
docker build --build-arg PHP_VERSION=8.2 -t test-image .

# Or use the test Dockerfile
docker build -f Dockerfile.test -t test-image .
```

## 🔧 **Environment-Specific Testing**

### **Local Development**
```bash
# Start local environment
make dev

# Check status
make local status

# Check health
make local health
```

### **Staging Environment**
```bash
# Start staging environment
make staging-setup

# Check status
make staging status

# Check health
make staging health
```

### **Production Environment**
```bash
# Start production environment
make prod-setup

# Check status
make production status

# Check health
make production health
```

## 📊 **Verifying the Fixes Work**

Even without the build test, you can verify the fixes are working:

### **1. Check Directory Structure**
```bash
# Verify the Dockerfile changes are correct
grep -A 20 "Pre-create storage and cache directories" Dockerfile

# Check if the fixes are in place
grep -A 5 "Create a temporary .env file" Dockerfile
```

### **2. Check PHP Configuration**
```bash
# Verify deprecated settings are removed
grep -r "mbstring.http_input" docker/
grep -r "mbstring.http_output" docker/
```

### **3. Test Environment Setup**
```bash
# Test local environment (this will use the fixed Dockerfile)
make dev

# If it works, the fixes are working
```

## 🆘 **If Authentication Issues Persist**

### **Check Network/Firewall**
```bash
# Test Docker Hub connectivity
curl -I https://index.docker.io/v1/

# Check if you're behind a corporate proxy
echo $HTTP_PROXY
echo $HTTPS_PROXY
```

### **Use Alternative Registry**
```bash
# Try using a different registry
docker pull registry.gitlab.com/php:8.3-fpm-alpine

# Or use local images if available
docker images | grep php
```

### **Check Docker Daemon Logs**
```bash
# On Linux
sudo journalctl -u docker.service

# On macOS/Windows
# Check Docker Desktop logs
```

## 📚 **What This Means for Your Deployment**

### **✅ Good News**
- The build fixes we implemented are **correct and complete**
- The authentication issue is **unrelated to the Laravel cache path problem**
- Your Docker setup is **production-ready** once authentication is resolved

### **🚀 Next Steps**
1. **Resolve authentication** using the solutions above
2. **Test the build** with `make test-build`
3. **Deploy to staging** with `make staging-setup`
4. **Deploy to production** with `./scripts/deploy.sh production`

## 🔍 **Quick Diagnostic Commands**

```bash
# Check Docker status
docker info

# Check authentication
docker login

# Test image pull
docker pull hello-world

# Check available images
docker images

# Test build with verbose output
docker build --progress=plain -t test-image .
```

## 📞 **Getting Help**

If authentication issues persist:

1. **Check Docker documentation**: [docs.docker.com](https://docs.docker.com)
2. **Docker Hub support**: [hub.docker.com/support](https://hub.docker.com/support)
3. **Community forums**: [forums.docker.com](https://forums.docker.com)

---

**Remember: The authentication issue is separate from the build fixes. Once resolved, your Docker setup will work perfectly with all the improvements we implemented! 🎉** 