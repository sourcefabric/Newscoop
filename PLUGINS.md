## Newscoop Plugins

Plugin system will be on top of Symfony Bundles ([best practices][1]).

Whole Newscoop will be under Symfony/Routing and Symfony/Kernel components. We will create simple controlers loades for zend controllers (nothing should change there). Thanks to that we will can use Bundles in Newscoop.

Every Newscoop Plugin Bundle should have composer.json file with type: newscoop-plugin

What plugins should can:

* create new option in menu
* provide configuration page
* provide new templates (smarty) blocks
* be localized (our localizer)
* be installed/uninstalled (events)
* works with our ACL
* provide new ui elements

### How it will works?

Everything will be based on events (Events Dispatcher). 

For example:

* On install action we will emmit newscoop.plugins.install event and Plugin developer will can register service for that.
* On article creation we will emmit newscoop.article.create event with article data and developer will can register service for that.

etc...

Plugins will lives under /admin/plugin/ route, for ex. /admin/plugin/debates.

#### New ui elements.

We will place in existing php files result from services registered on specified event.

Example:

* For article edit screen will be newscoop.ui.article.edit event and rendered results will be applied.

#### Widgets

Every PluginBundle will can provide widgets for dashboard (TO DO).



[1]: http://symfony.com/doc/2.0/cookbook/bundles/best_practices.html