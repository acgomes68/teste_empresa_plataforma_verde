#!/bin/bash

echo Upload Application container 
docker-compose up -d

echo Copy the configuration example file
docker exec -it webserver cp .env.example .env

echo Install dependencies
docker exec -it webserver composer install

echo Change permissions to cache folders
docker exec -it webserver chmod -R o+rw bootstrap/ storage/

echo Change permissions to database file
docker exec -it webserver chmod -R o+rw database/
docker exec -it webserver mv database/database.sqlite.example database/database.sqlite

echo Make migrations
docker exec -it webserver php artisan migrate:refresh

echo Make seeds
docker exec -it webserver php artisan db:seed

echo Show information of new containers
docker ps -a 

echo Show information of active containers
docker ps

