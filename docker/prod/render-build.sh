#!/bin/sh
set -ex
docker build --target production -t $RENDER_SERVICE_NAME .