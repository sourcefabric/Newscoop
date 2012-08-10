WHY independent application?

Damn hits me as I need to re-invent the wheel and with Zend we can't use wheel (FOSRestBundle, JMSSerializerBundle).

Idea for gimme will stay the same but with transformation/serialization by JMSSerializerBundle, routings by FOSRestBundle and symfony2 routing. 

We don't want problems with gimme once we will refactor Newscoop code.
We don't want all Zend Botstraped resources - without this layer gimme will be faster.
All resources we can get from dependency injection.
We can use builtied in symfony framework bundle events like:
	* kernel.response - allows other systems to modify or replace the Response object after its creation.
	* kernel.exception - A listener on this event can create and set a Response object (very usefull for us!), create and set a new Exception object.

We can use Symfony Profiler - API shoud be fast! It can be used in functional test - http://symfony.com/doc/current/cookbook/testing/profiling.html - it's realy cool, and allow to controle api speed.

WE CAN USE ALL NEWSCOOP CLASS AND SERVICES (EVEN SMARTY3), AND ALL 1639 BUNDLES FOR SYMFONY2 (http://knpbundles.com/).

	* FOSRestBundle - https://github.com/FriendsOfSymfony/FOSRestBundle
	* KnpPaginatorBundle - https://github.com/KnpLabs/KnpPaginatorBundle
	* JMSSerializerBundle - https://github.com/schmittjoh/JMSSerializerBundle
