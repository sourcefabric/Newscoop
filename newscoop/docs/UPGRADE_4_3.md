## Upgrading Newscoop version 4.2.3/4.2.4/4.3.x to 4.3.x

**Important!** Remember to backup all your data before performing upgrade.

1. Remove `newscoop/vendor` directory and its content from your current Newscoop instance (`sudo rm -rf newscoop/vendor`).
2. Copy Newscoop 4.3.x files over the 4.2.3/4.2.4/4.3.x files (e.g. `sudo cp -r /home/user/Newscoop43/newscoop/ /var/www/newscoop/`).
3. Run `upgrade.php` script. (Go to `http://www.example.com/upgrade.php`)
4. Check if there are any instructions to follow in the output of upgrade script. If so, then follow the steps.
5. When it is done, clear the cache folder: `sudo rm -rf cache/*`.
6. Run `php composer.phar dump-autoload --optimize` command in `../newscoop/` directory - this will autoload new classes.
7. Run `php application/console assets:install public/` command in `../newscoop/` directory - it will install assets.
8. Run `php scripts/fixer.php` script in `../newscoop/` directory - it will fix files permissions. (optional, run it when you don't know how to manage files permissions)
9. Clear the cache folder for the last time: `sudo rm -rf cache/*`.
10. You are done!

Above steps are required to upgrade Newscoop 4.2.3/4.2.4/4.3.x to 4.3.x.

We also recommend to update all the legacy plugins: `debate`, `poll`, `soundcloud`, `recaptcha`, because they will not be compatible with Newscoop 4.3 anymore.

How to do this?: (only when upgrading from 4.2.3/4.2.4 to 4.3.x)

* Make a backup of `newscoop/plugins/` directory.
* Remove the whole `newscoop/plugins/` content(Linux command: `sudo rm -rf newscoop/plugins/*`).
* Download the fixed package of legacy plugins from [here][3].
* Extract archive and copy it to `newscoop/plugins/` directory.
* Go to newscoop root folder (`../newscoop/`) and execute `php composer.phar dump-autoload —-optimize`
* Clear the cache folder: `sudo rm -rf cache/*`

## Restoring backup package from Newscoop version 4.2.3/4.2.4 on 4.3

For a backup/restore use the ["Backup/Restore utility"][4] which can be found in the Newscoop Admin Panel.

If your current 4.2.3/4.2.4 instance contained old legacy plugins (`debate`, `poll`, `soundcloud`, `recaptcha`) and you created a backup, you will have to update these plugins after the restore process in version 4.3 of Newscoop.

This is required because more adjustments have been done to make the legacy plugins compatible with Newscoop 4.3.

How to install old legacy plugins?:

* Download the fixed package of legacy plugins from [here][3].
* Extract archive and copy it to `newscoop/plugins/` directory.
* Go to newscoop root folder (`../newscoop/`) and execute `php composer.phar dump-autoload —-optimize`
* Clear the cache folder: `sudo rm -rf cache/*`

## Break Changes for old plugins:

#### CampInstallationBaseHelper is removed.
If your plugin use CampInstallationBaseHelper::ImportDB() function to import sql files - replace it with:
```
$databaseConnection = \Zend_Registry::get('container')->get('database_connection');
$installerDatabaseService = new \Newscoop\Installer\Services\DatabaseService(\Zend_Registry::get('container')->get('logger'));
$installerDatabaseService->importDB('path to sql', $databaseConnection);
```
If you use standard Sourcefabric plugins like debate, pool etc. then please use new (fixed) versions from our sourceforge/github.


## Newscoop core api changes

#### System preferences changes:
System preferences are now implemented as an service.

Usage:
```
//in non Symfony2 classes
$preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');

//in Symfony2 classes
$preferencesService = $this->container->get('system_preferences_service');

//get options
$preferencesService->get('SiteOnline');
//or
$preferencesService->SiteOnline;

//set options
$preferencesService->set('SiteOnline', 'Y');
//or
$preferencesService->SiteOnline = 'Y';
```


## New translation system:
Removed Localizer feature. Replaced with new Symfony2 translation concept.
Added event listener to load old plugins translations from directory ```../newscoop/newscoop/plugins/```
Translations files are located in ```../newscoop/newscoop/src/Newscoop/NewscoopBundle/Resources/translations``` directory


**Usage in templates:**

old translation tags usage:
```
{{ #search# }}
```

new translation tags usage:
```
{{ 'search'|translate:'translation_domain' }}
```
* ```search``` - given text to translate
* ```translation_domain``` - name of translation file, for ex. if our translation file name is ```custodian.en.yml``` then the translation domain is custodian and translation strings will be loaded from that file, if translation domain isn't set (```{{ 'search'|translate }}``` then default translation domain is used: ```theme_translation```.

Translation domains allow you to split translations to many files, ex. ```users.en.yml```, ```front.en.yml``` etc.

How to use it with global variables?
```
{{ assign var="welcome" value="{{ 'welcome'|translate }}" }}
and call it: {{ $welcome }}
```

New translation directory inside themes: ```theme_name/translations```

**Usage for developers:**

in Twig files:
```
{{ 'newscoop.recover.password'|trans({'%variableName%': variableName}, 'home') }}
```
where:
* ```home``` - translation domain (e.g. home.en.yml file)
* ```variableName``` - parameter to be displayed in translated string

in Controllers:
```
//Symfony2 controllers translator container
$translator = $this->get('translator');
 
//non Symfony2 controllers
$translator = \Zend_Registry::get('container')->getService('translator');
 
//translating string
$translator->trans('newscoop.preferences.error.cache',
    array('%cache%' => $cache_engine), 'system_pref'
));
```
where:
* ```array('%cache%' => $cache_engine)```  - parameter to be displayed in translated string
* ```system_pref``` - translation domain (e.g. system_pref.en.yml file)

Yaml file structure example:
```
home.en.yml file
newscoop:
  recover:
    password: "Password recovery for %variableName%"
```

More information about Symfony2 translations can be found [here][2]

## Publication override by request parameter
Added possibility to override publication name via request parameters:
Implementation:
```
if ($request->query->has('__publication_alias_name') || $request->request->has('__publication_alias_name')) {
    $publication = $request->get('__publication_alias_name');
}
```
Usage:

Send ```__publication_alias_name``` parameter with publication name in request.

## A new way to manage background jobs:

In Newscoop 4.3 we have introduced a new way to handle cron jobs management. We have 7 Newscoop internal cron jobs listed below, to help organize content.

Cron jobs:

* ```Autopublish``` - Autopublish pending issues and articles
* ```Indexer``` - Runs Newscoop Indexer - articles indexing
* ```Subscriptions``` notifications - Send Newscoop subscriptions notifications
* ```Events notifications``` - Send Newscoop events notifications
* ```Statistics clean``` - Remove old statistics from Newscoop database
* ```Send statistics``` - Send Newscoop stats to Sourcefabric
* ```Users garbage``` - Remove obsolete pending users data

Previously these all cron jobs were installed in cron tab during the Newscoop installation, every job were inserted into cron tab which means we had to manage all 7 jobs via cron tab config file manually.

In 4.3 we did it in a bit diffrent way, much more flexible and better. From now on we care only about one master cron job which is added to cron tab during the Newscoop installation process and all other jobs are being added to this master job which runs them all.

This cron job can be invoked manually like that:
```
php application/console scheduler:run
```
When Newscoop 4.3 will be installed sucessfully, this job will run every minute, firing all other 7 cron jobs which will run at their configured schedule time.
All cron jobs can be now managed by Newscoop backend in System Preferences where you will be able to disable/enable specified job:

To register your custom cron job you simply have to use newscoop.scheduler service:
```
$schedulerService = $this->get("newscoop.scheduler");
$schedulerService->registerJob("My custom job", array(
       'command' => $appDirectory.' custom:job',
       'schedule' => '* * * * *',
));
```

Second parameter of ```registerJob``` method is array of parameters:

Full list of parameters:
* ```string``` ```command```  - The job to run (either a shell command or anonymous PHP function)
* ```string``` ```schedule``` - Crontab schedule format (`man -s 5 crontab`)
* ```boolean``` ```enabled``` - Run this job at scheduled times
* ```boolean``` ```debug``` - Send `scheduler` internal messages to 'debug.log'
* ```string``` ```dateFormat``` - Format for dates on scheduler log messages
* ```string``` ```output``` - Redirect `stdout` and `stderr` to this file
* ```string``` ```runOnHost``` - Run jobs only on this hostname
* ```string``` ```environment``` - Development environment for this job
* ```string``` ```runAs``` - Run as this user, if crontab user has `sudo` privileges

## Other changes

* New design for System preferences tab
* Backend menu:
 * Changed menu to fit Bootstrap 3
* Password Recovery:
 *  Implemented new admin password recovery
* Topic management:
 * topic search starts from 3 characters
 * implemented as an service
 * add new topic on "enter" hit in Article Edit Screen
 * More can be found here: [Topic Management][1]
* Widget called `Google gadget` has been removed

[1]: https://wiki.sourcefabric.org/display/WOBS/Topic+Management
[2]: http://symfony.com/doc/current/book/translation.html
[3]: https://github.com/newscoop/newscoop-legacy-plugins
[4]: http://sourcefabric.booktype.pro/newscoop-43-for-journalists-and-editors/backup-and-upgrade/
