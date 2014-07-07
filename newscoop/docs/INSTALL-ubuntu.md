# Newscoop Ubuntu 14.04 Install Guide

This guide will help you install the latest Newscoop version without any knowledge about Apache, MySQL, PHP or any other technologies.

### Install LAMP stack

A "LAMP" stack is a group of open source software that is typically installed together to enable a server to host dynamic websites and web apps. This term is actually an acronym which represents the Linux operating system, with the Apache web server. The site data is stored in a MySQL database, and dynamic content is processed by PHP.

### Step 1 - Install Apache

    sudo apt-get update
    sudo apt-get install apache2

Since we are using a sudo command, these operations get executed with root privileges. Later on it will ask you to provide regular user's password to confirm apt-get operations.

Afterwards, your web server is installed.

To make sure if everything went fine you can go to:

	http://localhost/

You should see Apache2 Default page as shown below.

<img src="https://dl.dropboxusercontent.com/u/35759363/Newscoop%20images/Zrzut%20ekranu%202014-07-07%2016.19.29.png" width="690" height="590">

If you can see this page then everything works as expected.

### Step 2 - Install MySQL

...

### Step 3 - Install PHP
...

Install PHP extra extensions

    sudo apt-get install php5-intl php5-curl

You will need to restart the server afterwards:

	sudo service apache2 restart


### Install Git

    apt-get install git

### Install Subversion

Subversion is required to install Smarty bundle. Without subversion you will get an error: `Package could not be downloaded, sh: 1: svn: not found.` while installing Newscoop vendors. You won't be able to finish Newscoop installation properly.

    apt-get install subversion


### Download/Clone Latest Newscoop Repository Files Using Git

You can get the development sources from our public repository at Github by doing only this:

	cd /var/www/
	git clone https://github.com/sourcefabric/Newscoop.git

This will take some time depending on your internet connection speed. When cloning will successfully end, you will see a new folder called `Newscoop`(see image below)

<img src="https://dl.dropboxusercontent.com/u/35759363/Newscoop%20images/Zrzut%20ekranu%202014-07-07%2017.47.28.png" width="455" height="120">

### Set up VirtualHost in Apache2 configuration

The term Virtual Host refers to the practice of running more than one web site (such as company1.example.com and company2.example.com) on a single machine. Virtual hosts can be "IP-based", meaning that you have a different IP address for every web site, or "name-based", meaning that you have multiple names running on each IP address. The fact that they are running on the same physical server is not apparent to the end user. - [Apache2 docs][vhosts]

Create the file `/etc/apache2/sites-available/newscoop`

	sudo nano /etc/apache2/sites-available/newscoop

and copy in the information below.

	<VirtualHost *:80>
	   DocumentRoot /var/www/Newscoop/newscoop
	   ServerName localhost #www.example.com
	   ServerAdmin foo@bar.org

	   ErrorLog ${APACHE_LOG_DIR}/error.log
	   CustomLog ${APACHE_LOG_DIR}/access.log

	  <Directory /var/www/Newscoop/newscoop>
	      DirectoryIndex index.php
	      AllowOverride all
	      Order allow,deny
	      Allow from all
	  </Directory>
	</VirtualHost>

and then from the command line:

	sudo a2dissite 000-default
	sudo a2ensite newscoop
	sudo a2enmod rewrite php5
	sudo service apache2 restart

[vhosts]: http://httpd.apache.org/docs/2.2/vhosts/
