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
* create new pages (with controllers) for frontend

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

#### New pages (with controllers) for frontend

Bundle will can introduce new controllers for frontend (custom pages) with support for smarty theme templates.



[1]: http://symfony.com/doc/2.0/cookbook/bundles/best_practices.html

TODO:

* Merge gimme with newscoop directories, move everything one level up (newscoop/ will be root). (2 days)
* Replace zend routing with symfony/routing, provide support for all generating url's methods. (2 days)
* Install and configure symfony/kernel and symfony/httprequest for newscoop (1 day)
* Newscoop Plugins:
** create new option in menu (0.5 day)
** provide configuration page (0.5 day)
** provide new templates (smarty) blocks (1 day)
** be localized (our localizer) (0.5 day)
** be installed/uninstalled (events) (2 days - implement all events)
** works with our ACL (0.5 day)
** provide new ui elements (1 day)
** create new pages (with controllers) for frontend (1 day)

Summary: 12 working days for plugins system. + 5 days for paywal plugin.

## Newscoop avaiable events:

##### image.delivered
##### document.delivered
##### comment.recommended
##### image.approved
##### document.approved
##### playlist.delete
##### playlist.save
##### user.register

##### article_type.add - after article type creation
    article_type ArticleType - article type object

##### article_type.delete - before article type removing
    article_type ArticleType - article type object

##### article_type.hide - after article type hidding
    article_type ArticleType - article type object

##### article_type.rename - after article type hidding
    article_type ArticleType - article type object
    old_name string - old article type name

##### article_type.translate - after article type translation
    article_type ArticleType - article type object

##### article_type.comments_management - after article type comments activation/deactivation
    article_type ArticleType - article type object
    new_status boolean - new article type comments activation status

##### article.add - after article creation
    article Article - article object

##### article.delete - after article deletion
    article Article - article object

##### article.update - after article editing
    article Article - article object

##### article.translate - after article translation
    article Article - article object

##### article.duplicate - after article duplication
    article Article - article object
    orginal_article_number int - article number

##### article.move - after article moving
    article Article - article object

##### article.publish - after article publishing
    article Article - article object

##### article.submit - after article submiting
    article Article - article object
    

