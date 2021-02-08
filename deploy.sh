#!/bin/sh

# define path for git and php
git="$DEPLOY_GIT_PATH"
php="$DEPLOY_PHP_PATH"
composer="$DEPLOY_COMPOSER_PATH"

export COMPOSER_HOME="$composer"

# activate maintenance mode
$php artisan down
# update source code
$git pull

# update PHP dependencies
$composer install --no-interaction --no-dev --prefer-dist
# --no-interaction Do not ask any interactive question
# --no-dev  Disables installation of require-dev packages.
# --prefer-dist  Forces installation from package dist even for dev versions.

# update database
$php artisan migrate --force
# --force  Required to run when in production.

# update languages
$php artisan db:seed --class=LanguageSeeder

# clean cache
$php artisan clear-compiled 
$php artisan cache:clear
$php artisan config:clear
$php artisan route:clear
$php artisan view:clear
# clear-compiled  Remove the compiled class file
# cache:clear  Flush the application cache
# config:clear  Remove the configuration cache file
# route:clear  Remove the route cache file
# view:clear  Clear all compiled view files

# restart queue
$php artisan queue:restart

# stop maintenance mode
$php artisan up
