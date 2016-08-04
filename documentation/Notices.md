#Newscoop Notices
## Family announcements, notices, classified ads

See https://wiki.sourcefabric.org/display/CS/Family+announcements

### Available endpoints/controllers
Admin_NoticeController
 - list/manage 
 - add/edit Notice
 - list Categories
 - edit Categories
 - configure notices

NoticeRestController (API being used by frontend)
 - list (queries with tags)
 - get  (by id)

### Services 
 - I decided not to wrap EntityRepository methods into a service class
 - Just use the already existing Service pattern of doctrine (Repositories)
 
### Mapped Entities/Data Models
Using gedmo extensions for doctrine
https://github.com/l3pp4rd/DoctrineExtensions

NoticeCategory (tree, sluggable, timestampable)
 - used as a blueprint for available categories and category groups
 - Notices are related to NoticeCategories through a join table (many2many)

Notice
 - has many tags
 - can be queried with tags
     - /notice-rest
     - /notice-rest/?query=Geburtsanzeigen/Basel


##ToDos:
 - Controllers and Repositories are to be cleaned up (coding style, encapsulation + proper comments and doc blocks)

Newscoop localization needs work, we should bring wording etc. to a final state and then localize.
 - spec and features seem to be a slightly moving target - lets see how much clutter there is to come