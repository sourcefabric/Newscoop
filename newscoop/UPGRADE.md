UPGRADE
=======


Upgrading from version older than 3.5.0 is not supported anymore. If you
had an older version you need to install Newscoop 3.5.0 and upgrade to this
version.

#### Warning! Backup your site before performing the upgrade!

### Upgrading to Newscoop 4.2
#### Find your Newscoop dir

First we change to your `Newscoop directory`, this depends per server but usually is `/var/www/newscoop/`
```
cd `NewscoopDirectory`
```

#### Vendors

Before upgrading (which will replace some files) we must remove all files and directories from the `vendor` directory:

```
rm -rf vendor/*
```

If you forget about this before upgrading don't worry. You can remove it after upgrading the files and then run: 

```
# ONLY IF YOU FORGOT THE PREVIOUS STEP
php composer.phar install
```

#### Writable directories

The following files have to be read/writable by the `upgrade.php` script in order to upgrade Newscoop automatically.

```
chmod 777 application/configs/application.ini-dist
chmod 777 library/Newscoop/Services/EventDispatcherService.php
chmod 777 library/Newscoop/DoctrineEventDispatcherProxy.php
chmod 777 library/Newscoop/Entity/Repository/User/SubscriberRepository.php
chmod 777 library/Resource/Doctrine.php
chmod 777 application/modules/admin/controllers/TestController.php
chmod 777 application/controllers/ArticleofthedayController.php
chmod 777 library/Newscoop/Entity/User/Subscriber.php
chmod 777 library/Newscoop/Entity/Entity.php
chmod 777 .disable_upgrade
chmod 777 README.txt
chmod 777 .travis.yml
chmod 777 UPGRADE.txt
chmod -R 777 library/Newscoop/Entity/Proxy
chmod -R 777 library/fabpot-dependency-injection-07ff9ba
chmod -R 777 library/fabpot-event-dispatcher-782a5ef
chmod -R 777 library/smarty3
chmod -R 777 docs/
chmod -R 777 files/
chmod -R 777 videos/
```
This can however still be troublesome so it can sometimes be recommended to remove them manually.

#### Actual unpacking of files
Now we have to unpack the upgrade over your old one. Place the `Newscoop 4.2` `ZIP` or `TAR.GZ` one directory higher then your `Newscoop directory`. This will usually be `/var/www/`. Once this file is there you can do the following:

If you have the `ZIP`

```
unzip newscoop-4.2.zip
```
This will then proceed to ask you if you want to replace certain files, you can answer with `A` for All.

If you have the `TAR.GZ`

```
tar -xzf newscoop-4.2.tar.gz
```

#### Run the `upgrade.php` script
Now we point our browser to our `Newscoop Web Site` and type /upgrade.php behind the URL. This will automatically run the required Scripts and Database Upgrades required for 4.2.

Once this process is complete, congratulations! You're now on Newscoop 4.2!

### Upgrading from Newscoop 3.5.x:

Drop the new sources over the existing install, open a browser and make a request for the publication home page: ```http://[site_name]```

The upgrade will be performed automatically when you make the first
request to the publication site or to the Newscoop admin interface.

After upgrading, you need to MANUALLY update ```.htaccess``` file in your 
Newscoop directory. Make the following changes:

* Template directory rewrite:

    Remove this line:

    ```RewriteCond %{REQUEST_URI} !/+templates```

    Add this line:

    ```RewriteCond %{REQUEST_URI} !/+themes```

* Javascript directory rewrite:

    Remove this line:

    (Depending on your Newscoop installation, it is possible that you do not
    have this line)

    ```RewriteCond %{REQUEST_URI} !(/+plugins/[^/]*)?/+javascript```

    Add this line:

    ```RewriteCond %{REQUEST_URI} !(/+plugins/[^/]*)?/+js```

The reason why this is not performed during automatic upgrade is because
almost every Newscoop installation is on a custom configuration and has a
modified .htaccess file. Therefore, it's not a good idea to automatically
modify the file.

##### Compatibility issues

In version 3.0 the template language was modified extensively. The
backup restore script will provide an automated conversion of the old
template files to the new format. For more details on language changes
please read the manual.

The template language is fully backward compatible with the older 3.x
versions.

In version 4.0 the old templates system was replaced by themes, when upgrading
this may generate problems when rendering the front-end pages as it was
impossible to cover all cases. If you get into this situation you should
modify your templates manually to adjust certain paths. You can always ask
in our forums for support, we will be glad to help you out!
