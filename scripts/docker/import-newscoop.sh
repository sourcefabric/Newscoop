#!/bin/sh

SHARE_DIR="/usr/share/newscoop"
DATA_DIR="$SHARE_DIR/data"
WWW_DIR="/var/www/newscoop"
PLUGINS=false
CUSTOM_DB=false

if [ ! "$(ls -A $DATA_DIR)" ]; then
    echo "nothing to import"
    exit 0
fi

# import database
if [ -s $DATA_DIR/database.sql ] ; then
    DUMP="$DATA_DIR/database.sql"
    CUSTOM_DB=true
else
    DUMP="$WWW_DIR/install/Resources/sql/campsite_core.sql"
fi

# Create database and insert custom or default data
mysql -h mysql -u root -proot -e 'CREATE DATABASE newscoop;'
mysql -h mysql -u root -proot newscoop < $DUMP;

# Update root password to: SoFab
mysql -h mysql -u root -proot newscoop -e 'UPDATE liveuser_users SET Password = "sha1$nDB8qhSKXSKD$7cd8fb537cc3f60708dbcc8c8925f3a0600fa444" WHERE id = 1 and UName = "admin"';

# copy system themes to user share
if [[ "$(ls -A $DATA_DIR/themes)" ]] ; then
    mv $WWW_DIR/themes/* $DATA_DIR/themes/
    # Remove theme directory so we can create symlink
    rm -rf $WWW_DIR/themes/
    # make theme symlink
    ln -sf $DATA_DIR/themes $WWW_DIR/themes
    chown -hR www-data:www-data $DATA_DIR/themes
fi

# copy plugins
if [[ "$(ls -A $DATA_DIR/plugins)" ]] ; then
    cp -rf $DATA_DIR/plugins/* $WWW_DIR/plugins/
    PLUGINS=true
fi

# copy custom parameters
if [[ -s $DATA_DIR/custom_parameters.yml ]] ; then
    cp $DATA_DIR/custom_parameters.yml $WWW_DIR/application/configs/parameters/custom_parameters.yml
fi

# Files are required for installed newscoop
cp $DATA_DIR/conf/database_conf.php.dist $WWW_DIR/conf/database_conf.php
cp $DATA_DIR/conf/configuration.php.dist $WWW_DIR/conf/configuration.php

# Set up htaccess
cp $WWW_DIR/htaccess.dist $WWW_DIR/.htaccess

# Move to directory else composer will complain
cd $WWW_DIR
php $WWW_DIR/composer.phar dump-autoload --optimize

if [[ $PLUGINS && $CUSTOM_DB ]] ; then
    php $WWW_DIR/application/console plugins:upgrade
    php $WWW_DIR/composer.phar update
    php $WWW_DIR/composer.phar dump-autoload --optimize
    php $WWW_DIR/application/console assets:install --symlink public
fi

# Clear the cache
rm -rf $WWW_DIR/cache/*

# Run the upgrade script
php $WWW_DIR/upgrade.php

# Remove files which indicate not yet installed
rm $WWW_DIR/conf/upgrading.php
rm $WWW_DIR/conf/installation.php

