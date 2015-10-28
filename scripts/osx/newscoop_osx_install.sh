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
DEFAULT_NS_DIR=/Users/$(whoami)/Sites/Newscoop
echo "Enter the Newscoop repository checkout dir, followed by [ENTER]: ($DEFAULT_NS_DIR)"
read NSDIR 

if [[ -z "$VAR" ]] ; then
    NSDIR=${DEFAULT_NS_DIR//\//\\/}
else
    NSDIR=${NSDIR//\//\\/}
fi
echo "Using $NSDIR as web root for vhost"

echo "Backing up existing /etc/apache2/httpd.conf"
sudo cp /etc/apache2/httpd.conf /etc/apache2/httpd.conf.bak
sudo cp $SCRIPTPATH/httpd.conf /etc/apache2/httpd.conf

echo $SCRIPTPATH

echo "Backing up existing /etc/apache2/extra/httpd-vhosts.conf"
sudo cp /etc/apache2/extra/httpd-vhosts.conf /etc/apache2/extra/httpd-vhosts.conf.bak
(sed '/# START newscoop.dev.conf/,/# END newscoop.dev.conf/d' /etc/apache2/extra/httpd-vhosts.conf; cat newscoop.dev.conf ) > $SCRIPTPATH/httpd-vhosts.conf.tpl
sudo sh -c "sed 's/\$NEWSCOOPDIR/$NSDIR/g' $SCRIPTPATH/httpd-vhosts.conf.tpl > /etc/apache2/extra/httpd-vhosts.conf"
rm -rf $SCRIPTPATH/httpd-vhosts.conf.tpl
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
