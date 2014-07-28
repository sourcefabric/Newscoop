# Newscoop Ubuntu 14.04 Install Guide [Advanced]

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

	sudo apt-get install mysql-server libapache2-mod-auth-mysql php5-mysql


You will be asked to enter MySQL "root" password:

<img src="https://dl.dropboxusercontent.com/u/35759363/Newscoop%20images/Zrzut%20ekranu%202014-07-08%2010.18.04.png" width="571" height="444">

Type strong one and hit `ENTER` button. It will ask you to confirm it:

<img src="https://dl.dropboxusercontent.com/u/35759363/Newscoop%20images/Zrzut%20ekranu%202014-07-08%2010.19.50.png" width="554" height="439">

Type the same password you typed in the step above and hit `ENTER` button. MySQL server is installed now.

First, we need to tell MySQL to create its database directory structure where it will store its information. You can do this by typing:

	sudo mysql_install_db

Afterwards, we want to run a simple security script that will remove some dangerous defaults and lock down access to our database system a little bit. Start the interactive script by running: (this step is optional but for better security it is recommended to run this script and follow instructions in it)

	sudo mysql_secure_installation

### Step 3 - Install PHP
...

	sudo apt-get install php5 libapache2-mod-php5 php5-mcrypt php5-intl php5-curl php5-gd curl

You will need to restart the Apache2 server afterwards so the configuration can refresh:

	sudo service apache2 restart

By default `www` folder will be owned by `root` we have to change this so `www-data` user can own it which is much safer and don't complicate things much.

	sudo chown -R www-data:www-data /var/www

We will also modify permissions and make sure that access is permitted to the general web directory.

	sudo chmod -R 755 /var/www

### Install Git

    sudo apt-get install git

### Install Subversion

Subversion is required to install Smarty bundle. Without subversion you will get an error: `Package could not be downloaded, sh: 1: svn: not found.` while installing Newscoop vendors. You won't be able to finish Newscoop installation properly.

    sudo apt-get install subversion


### Download/Clone Latest Newscoop Repository Files Using Git

You can get the development sources from our public repository at Github by doing only this:

	cd /var/www/
	git clone https://github.com/sourcefabric/Newscoop.git

This will take some time depending on your internet connection speed. When cloning will successfully end, you will see a new folder called `Newscoop`(see image below)

<img src="https://dl.dropboxusercontent.com/u/35759363/Newscoop%20images/Zrzut%20ekranu%202014-07-07%2017.47.28.png" width="455" height="120">

### Set up Virtual Host in Apache2 configuration

The term Virtual Host refers to the practice of running more than one web site (such as company1.example.com and company2.example.com) on a single machine. Virtual hosts can be "IP-based", meaning that you have a different IP address for every web site, or "name-based", meaning that you have multiple names running on each IP address. The fact that they are running on the same physical server is not apparent to the end user. - [Apache2 docs][vhosts]

Create the file `/etc/apache2/sites-available/newscoop.conf`

	sudo nano /etc/apache2/sites-available/newscoop.conf

and copy in the information below.

	<VirtualHost *:80>
			DocumentRoot /var/www/Newscoop/newscoop
			ServerName localhost
			ServerAdmin foo@bar.org
			<Directory /var/www/Newscoop/newscoop>
                	AllowOverride All
        	</Directory>
	</VirtualHost>

and then from the command line:

	sudo a2dissite 000-default
	sudo a2ensite newscoop
	sudo a2enmod rewrite
	sudo service apache2 restart


Go to http://localhost and you will see Newscoop Installer

<img src="https://dl.dropboxusercontent.com/u/35759363/Newscoop%20images/Zrzut%20ekranu%202014-07-08%2013.37.16.png" width="570" height="141">

Follow instructions in Installer to complete the installation.

[vhosts]: http://httpd.apache.org/docs/2.2/vhosts/
