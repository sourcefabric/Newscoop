#!/bin/bash
########################################################################
# Utility script to install newscoop and all its dependencies on OSX
# using brew, curl, and php composer
#
SCRIPTPATH="`dirname \"$0\"`"
SCRIPTPATH="`( cd \"$SCRIPTPATH\" && pwd )`" 

###############################################################
# START dependency installs
#   For initial install only
###############################################################

#
# Check if Homebrew is installed
#
which -s brew
if [[ $? != 0 ]] ; then
    # Install Homebrew
    /usr/bin/ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"
fi

#
# Check for PHP with intl
#
php -i | grep "Internationalization support => enabled"
if [[ $? != 0 ]] ; then
    echo "Installing php55-intl";
    brew install php55-intl
fi

#
# Check for mysql
#
which -s mysql
if [[ $? != 0 ]] ; then
    brew install mysql
fi

#
# Check for composer
#
which -s composer 
if [[ $? != 0 ]] ; then
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
fi

###############################################################
# END dependency installs
###############################################################

# apache config
sudo cp /etc/apache2/httpd.conf /etc/apache2/httpd.conf.bak
sudo cp httpd.conf /etc/apache2/httpd.conf
sudo sh -c "(sed '/# START newscoop.dev.conf/,/# END newscoop.dev.conf/d' /etc/apache2/extra/httpd-vhosts.conf; cat newscoop.dev.conf ) > /etc/apache2/extra/httpd-vhosts.conf"
sudo apachectl restart


# reset newscoop config
cd $SCRIPTPATH/../../newscoop
sudo rm -rf conf/configuration.php conf/database_conf.php cache/*
mysql -e 'drop database newscoop;' -uroot

# set permissions
chmod 775 plugins install cache images public conf log

# composer install, and newscoop install
composer self-update
composer install --prefer-dist
sudo ./application/console newscoop:install --fix --database_name newscoop --database_user root --no-client
sudo php upgrade.php
sudo php $SCRIPTPATH/../../newscoop/scripts/fixer.php
