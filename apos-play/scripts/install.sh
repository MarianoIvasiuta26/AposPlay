#!/bin/bash

rm -rf node_modules
rm -rf vendor
rm -rf storage/app/requests
rm -rf storage/app/reports
rm -rf storage/app/livewire-tmp

docker run --rm -u "$(id -u):$(id -g)" -v $(pwd):/var/www/html -w /var/www/html composer:latest composer install --ignore-platform-reqs --no-scripts

cp .env.example .env
vendor/bin/sail down -v
vendor/bin/sail up -d
echo "Esperando docker MySQL..."
sleep 15
vendor/bin/sail artisan key:generate
vendor/bin/sail artisan migrate:fresh --seed

vendor/bin/sail npm install
vendor/bin/sail npm run dev
