## Break Changes for old plugins:

#### CampInstallationBaseHelper is removed.
If your plugin use CampInstallationBaseHelper::ImportDB() function to import sql files - replace it with:
```
$databaseConnection = \Zend_Registry::get('container')->get('database_connection');
$installerDatabaseService = new \Newscoop\Installer\Services\DatabaseService(\Zend_Registry::get('container')->get('logger'));
$installerDatabaseService->importDB('path to sql', $databaseConnection);
```
If you use standard Sourcefabric plugins like debate, pool etc. then please use new (fixed) versions from our sourceforge/github.


## Newscoop core api chnages

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
* Community Feeds (Community Ticker):
 * Community Ticker has been removed from Newscoop core. Available as a plugin: Community ticker

[1]: https://wiki.sourcefabric.org/display/WOBS/Topic+Management
[2]: http://symfony.com/doc/current/book/translation.html