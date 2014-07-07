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

You should see Apache2 Welcome page as shown below.

<img src="https://www.dropbox.com/s/ak94w43zwjryb11/Zrzut%20ekranu%202014-07-07%2016.19.29.png" width="690" height="590">

If you can see this page then everything works as expected.

### Step 2 - Install MySQL

...

### Step 3 - Install PHP
...

Install PHP Intl extension

    sudo apt-get install php5-intl


### Install Git

    apt-get install git

### Install Subversion

Subversion is required to install Smarty bundle. Without subversion you will get an error: `Package could not be downloaded, sh: 1: svn: not found.` while installing Newscoop vendors. You won't be able to finish Newscoop installation properly.

    apt-get install subversion


