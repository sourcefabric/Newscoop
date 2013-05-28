UPGRADE
=======


Upgrading from version older than 3.5.0 is not supported anymore. If you
had an older version you need to install Newscoop 3.5.0 and upgrade to this
version.

#### Warning! Backup your site before performing the upgrade!

### Upgrading to Newscoop 4.2

Before upgrading (replacing files) you must remove all files/directories form /vendor directory. If you forget about this, then you can remove all vendor directory and run `php composer.phar install` command.

Also all those files and directories must be writable:

```
'/application/configs/application.ini-dist',
'/library/Newscoop/Services/EventDispatcherService.php',
'/application/modules/admin/controllers/TestController.php',
'/library/Newscoop/Entity/User/Subscriber.php',
'/library/Newscoop/Entity/Entity.php',
'/.disable_upgrade',
'/README.txt',
'/.travis.yml',
'/UPGRADE.txt'
'/library/Newscoop/Entity/Proxy',
'/library/fabpot-dependency-injection-07ff9ba',
'/library/fabpot-event-dispatcher-782a5ef',
'/library/smarty3',
'/docs',
'/files',
'/videos',
```

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

