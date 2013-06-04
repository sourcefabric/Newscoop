UPGRADE
=======

### Warning! Backup your site before performing the upgrade!
It is always safe to Backup your files before performing an upgrade! Please make a Backup of your files and database first.

### Upgrading from Newscoop 3.5.x:

Upgrading from version older than 3.5.0 is not supported anymore. If you had an older version you need to install Newscoop 3.5.0 and upgrade to this version.

### Upgrading from Newscoop 4.1.x to Newscoop 4.2
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
chmod -R 777 files/
chmod -R 777 videos/
```

*If this produces a `chmod: changing permissions of [snip]` error, try it as `root`*

The following files have to be removed.

```
rm -rf application/configs/application.ini-dist
rm -rf library/Newscoop/Services/EventDispatcherService.php
rm -rf library/Newscoop/DoctrineEventDispatcherProxy.php
rm -rf library/Newscoop/Entity/Repository/User/SubscriberRepository.php
rm -rf library/Resource/Doctrine.php
rm -rf application/modules/admin/controllers/TestController.php
rm -rf application/controllers/ArticleofthedayController.php
rm -rf library/Newscoop/Entity/User/Subscriber.php
rm -rf library/Newscoop/Entity/Entity.php
rm -rf .disable_upgrade
rm -rf README.txt
rm -rf .travis.yml
rm -rf UPGRADE.txt
rm -rf library/Newscoop/Entity/Proxy
rm -rf library/fabpot-dependency-injection-07ff9ba
rm -rf library/fabpot-event-dispatcher-782a5ef
rm -rf library/smarty3
rm -rf docs/
```

#### Clearing the `Cache`

Now we should not forget to clear the `cache`

```
rm -rf cache/*
```

#### Actual unpacking of files
Now we have to unpack the upgrade over your old one. Place the `Newscoop 4.2` `ZIP` or `TAR.GZ` one directory higher then your `Newscoop directory`. This will usually be `/var/www/`. Once this file is there you can do the following.

First we change to the directory above your `Newscoop directory`.

```
cd ..
```

If you have the `ZIP`

```
unzip -oq newscoop-4.2.zip
```

If you have the `TAR.GZ`

```
tar -xzf newscoop-4.2.tar.gz
```

##### Setting permissions
Now in order to have everything work correctly we need to set some permissions. This can be done easily now:


```
cd newscoop/
sh bin/post-install.sh
```

#### Run the `upgrade.php` script
Now we point our browser to our `Newscoop Web Site` and type `/upgrade.php` behind the URL. This will automatically run the required Scripts and Database Upgrades required for 4.2.

Once this process is complete, congratulations! You're now on Newscoop 4.2!