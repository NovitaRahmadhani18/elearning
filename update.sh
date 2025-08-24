#!/bin/sh

git pull origin main
if [ $? -ne 0 ]; then
    echo "Error: Failed to pull the latest changes from the repository."
    exit 1
fi

docker compose up -d --build
if [ $? -ne 0 ]; then
    echo "Error: Failed to build and start the Docker containers."
    exit 1
fi

echo "Docker containers started successfully."

# Check if the containers are running
# docker compose exec app php artisan migrate:fresh --force --seed

docker compose exec app php artisan optimize

if [ $? -ne 0 ]; then
    echo "Error: Failed to run migrations and optimize the application."
    exit 1
fi
