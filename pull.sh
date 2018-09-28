#!/bin/bash

git pull &&
composer install &&
php run cache:clear &&
php run cache:warmup &&
php run doctrine:migrations:migrate &&
php run cache:clear &&
php run cache:warmup