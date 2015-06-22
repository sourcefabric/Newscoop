#!/bin/sh


if [ "$1" = 'newscoop' ]; then
    # install composer and install PHP dependencies
    if [ ! -f /var/www/newscoop/composer.phar ]; then
        cd /var/www/newscoop && curl -s https://getcomposer.org/installer | php
    fi
    cd /var/www/newscoop && /usr/bin/php composer.phar install --no-dev
    chown -R www-data:www-data /var/www/newscoop

    # check if we should install
    if [ -f /var/www/newscoop/conf/installation.php ]; then
      /usr/share/newscoop/import-newscoop.sh
    fi

    # catch signlas
    trap "echo 'caught signal'" HUP INT QUIT KILL TERM

    #/usr/sbin/apachectl -D FOREGROUND
    /usr/sbin/apachectl start

    tail -f /var/www/newscoop/log/prod.log

    echo "[hit enter key to exit] or run docker stop <container>'"
    read

    echo "stopping apache"
    /usr/sbin/apachectl stop

    echo "exited $0"
fi

exec "$@"
