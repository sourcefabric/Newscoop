#!/bin/bash

if [ "$1" = 'newscoop' ]; then
    # install composer and install PHP dependencies
    if [ ! -f /var/www/newscoop/composer.phar ]; then
        cd /var/www/newscoop && curl -sS https://getcomposer.org/installer | php
    fi
    if [ "$APPLICATION_ENVIRONMENT" = "dev" ]; then
        # Disable production, enable dev
        a2dissite newscoop
        a2ensite newscoop-dev
        cd /var/www/newscoop && /usr/bin/php composer.phar install
    else
        cd /var/www/newscoop && /usr/bin/php composer.phar install --no-dev
    fi

    # check if we should install
    if [ -f /var/www/newscoop/conf/installation.php ]; then
        /usr/share/newscoop/import-newscoop.sh
    fi

    # catch signlas
    trap "echo 'caught signal'" HUP INT QUIT KILL TERM

    cron start

    #/usr/sbin/apachectl -D FOREGROUND
    /usr/sbin/apachectl start

    if [ "$APPLICATION_ENVIRONMENT" = "dev" ]; then
        tail -F /var/www/newscoop/log/dev.log
    else
        tail -F /var/www/newscoop/log/prod.log
    fi

    echo "[hit enter key to exit] or run docker stop <container>'"
    read

    echo "stopping apache"
    /usr/sbin/apachectl stop

    echo "stopping cron"
    stop cron

    echo "exited $0"
fi

exec "$@"
