#!/bin/sh
composer install
vendor/bin/doctrine-migrations migrations:migrate -n
php-fpm
