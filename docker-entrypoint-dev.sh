#!/bin/bash
set -e

#
# If we're starting web-server we need to do following:
#   1) Modify docker-php-ext-xdebug.ini file to contain correct remote host value
#   2) Ensure that /app/var directory exists and clear possible existing one before that
#   3) Install all dependencies
#   4) Generate JWT encryption keys + allow apache to read this file
#   5) Create database if it not exists yet
#   6) Run possible migrations, so that database is always up to date
#   7) Ensure that _all_ files have "correct" permissions
#
# Note that all the chmod stuff is for users who are using docker-compose within Linux environment. More info in link
# below:
#   https://jtreminio.com/blog/running-docker-containers-as-current-host-user/
#

# Step 1
HOST=`/sbin/ip route|awk '/default/ { print $3 }'`
sed -i "s/xdebug\.remote_host \=.*/xdebug\.remote_host\=$HOST/g" /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Step 2
rm -rf /app/var
mkdir -p /app/var

# Step 3
composer install

# Step 4
make generate-jwt-keys
chmod 644 /app/config/jwt/private.pem

# Step 5
php /app/bin/console doctrine:database:create --if-not-exists --no-interaction

# Step 6
php /app/bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

# Step 7
chmod -R o+s+w /app

exec "$@"
