# Newscoop OSX Install Guide [Advanced]

This guide will help you install the latest Newscoop version.  You can choose to use the automated install script, which allows for install of all Newscoop dependencies without prior knowledge of Apache, MySQL, PHP.   Or follow the manual steps to install and configure all dependencies one by one.

## Automated Install

The provided newscoop_osx_install.sh script will install Homebrew (package manager), PHP, MySQL, Composer, and configure the existing apache2 service on OSX for Newscoop and all of its dependencies.  After succesful execution of the script your Newscoop instance will be available locally at http://newscoop.dev/admin.

### Step 1 - Run newscoop_osx_install.sh

    ./scripts/osx/newscoop_osx_install.sh

***


## Manual Install

The following steps will walk you through manually installing each of the dependencies required by Newscoop.  Use this method of install if you have already installed some dependencies already, or you wish to customize the install / configuration of any the dependencies. 

### Step 1 - Install Homebrew

    /usr/bin/ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)" 

### Step 2 - Install PHP with intl support
...

    brew install php55-intl

### Step 3 - Install MySQL
...

    brew install mysql

The default MySQL "root" password will be empty.

### Step 4 - Install Composer

    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer

### Step 5 - Configure Apache2

Copy the provided apache config file to the appropriate locations.

    sudo cp /etc/apache2/httpd.conf /etc/apache2/httpd.conf.bak
    sudo cp ./scripts/osx/httpd.conf /etc/apache2/httpd.conf


### Step 6 - Set up Virtual Host in Apache2 configuration

The term Virtual Host refers to the practice of running more than one web site (such as company1.example.com and company2.example.com) on a single machine. Virtual hosts can be "IP-based", meaning that you have a different IP address for every web site, or "name-based", meaning that you have multiple names running on each IP address. The fact that they are running on the same physical server is not apparent to the end user. - [Apache2 docs][vhosts]

Backup the file `/etc/apache2/extra/httpd-vhosts.conf`

    sudo cp /etc/apache2/extra/httpd-vhosts.conf /etc/apache2/extra/httpd-vhosts.conf.bak

Open the file `/etc/apache2/extra/httpd-vhosts.conf`
and copy in the information below.

    <VirtualHost *:80>
        DocumentRoot "$NEWSCOOPDIR/newscoop"
        SetEnv APPLICATION_ENV "development"
        ServerName newscoop.dev
        ServerAlias www.newscoop.dev
        DirectoryIndex index.php index.html
        <Directory $NEWSCOOPDIR/newscoop/ >
            AllowOverride All
            Options Indexes MultiViews FollowSymLinks
            Require all granted
        </Directory>
        ErrorLog "/private/var/log/apache2/newscoop-error.log"
        CustomLog "/private/var/log/apache2/newcsoop-access.log" common
    </VirtualHost>

Where $NEWSCOOPDIR is the fully qualified path of the newscoop repository checkout.

and then from the command line:

    cd newscoop
    chmod 775 plugins install cache images public conf log
    composer install --prefer-dist
    sudo apachectl restart 


Go to http://newscoop.dev and you will see Newscoop Installer


Follow instructions in Installer to complete the installation.

[vhosts]: http://httpd.apache.org/docs/2.2/vhosts/

