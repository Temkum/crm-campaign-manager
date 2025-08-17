#!/bin/bash

# =============================================================================
# Alternative Docker Build Test Script
# =============================================================================
# This script tests the build with Dockerfile.test to bypass authentication issues

set -e

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

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

# Configuration
IMAGE_NAME="crm-manager-test-alt"
TAG="latest"
DOCKERFILE="Dockerfile.test"

# Check if alternative Dockerfile exists
if [ ! -f "$DOCKERFILE" ]; then
    log_error "Alternative Dockerfile $DOCKERFILE not found!"
    exit 1
fi

# Clean up any existing test image
cleanup() {
    log_info "Cleaning up test image..."
    docker rmi "$IMAGE_NAME:$TAG" 2>/dev/null || true
}

# Test build with alternative Dockerfile
test_build() {
    log_info "Testing Docker build with $DOCKERFILE..."
    
    # Build the image using the alternative Dockerfile
    if docker build -f "$DOCKERFILE" -t "$IMAGE_NAME:$TAG" .; then
        log_info "Build successful! ✅"
        return 0
    else
        log_error "Build failed! ❌"
        return 1
    fi
}

# Test container startup
test_container() {
    log_info "Testing container startup..."
    
    # Start container in background
    CONTAINER_ID=$(docker run -d --name test-container-alt "$IMAGE_NAME:$TAG")
    
    if [ $? -eq 0 ]; then
        log_info "Container started successfully"
        
        # Wait a bit for services to start
        sleep 15
        
        # Check if container is running
        if docker ps | grep -q test-container-alt; then
            log_info "Container is running ✅"
            
            # Check health endpoint
            if docker exec test-container-alt curl -f http://localhost/health >/dev/null 2>&1; then
                log_info "Health check passed ✅"
                SUCCESS=true
            else
                log_warn "Health check failed"
                SUCCESS=false
            fi
        else
            log_error "Container failed to start ❌"
            SUCCESS=false
        fi
        
        # Clean up test container
        docker stop test-container-alt 2>/dev/null || true
        docker rm test-container-alt 2>/dev/null || true
        
        return $([ "$SUCCESS" = true ] && echo 0 || echo 1)
    else
        log_error "Failed to start container ❌"
        return 1
    fi
}

# Main execution
main() {
    log_info "Starting alternative Docker build test..."
    log_info "Using Dockerfile: $DOCKERFILE"
    
    # Set up cleanup on exit
    trap cleanup EXIT
    
    # Test build
    if test_build; then
        # Test container
        if test_container; then
            log_info "All tests passed! 🎉"
            log_info "The build fixes are working correctly!"
            exit 0
        else
            log_error "Container test failed! ❌"
            exit 1
        fi
    else
        log_error "Build test failed! ❌"
        exit 1
    fi
}

# Run main function
main "$@"