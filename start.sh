#!/bin/bash

echo "Detecting service type..."
echo "SERVICE_TYPE = $SERVICE_TYPE"

if [ "$SERVICE_TYPE" = "worker" ]; then
    echo "Starting WORKER service..."
    exec bash worker-start.sh
else
    echo "Starting WEB service..."
    exec bash web-start.sh
fi
