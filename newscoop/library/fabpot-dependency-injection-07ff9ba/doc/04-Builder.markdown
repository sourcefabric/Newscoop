Using a Builder to create Services
==================================

In the previous chapter, you learned how to use the `sfServiceContainer` class
to provide a more appealing interface to your service containers. In this
chapter, we will go one step further and learn how to leverage the
`sfServiceContainerBuilder` class to describe services and their configuration
in pure PHP code.

The `sfServiceContainerBuilder` class extends the basic `sfServiceContainer`
class and allows the developer to describe services with a simple PHP
interface.

>**SIDEBAR**
>The Service Container Interface
>
>All service container classes share the same interface, defined in
>`sfServiceContainerInterface`:
>
>     [php]
>     interface sfServiceContainerInterface
>     {
>       public function setParameters(array $parameters);
>       public function addParameters(array $parameters);
>       public function getParameters();
>       public function getParameter($name);
>       public function setParameter($name, $value);
>       public function hasParameter($name);
>       public function setService($id, $service);
>       public function getService($id);
>       public function hasService($name);
>     }

The descriptions of the services are done by registering service definitions.
Each service definition describes a service: from the class to use to the
arguments to pass to the constructor, and a bunch of other configuration
properties (see the `sfServiceDefinition` sidebar below).

The `Zend_Mail` example can easily be rewritten by removing all the hardcoded
code and building it dynamically with the builder class instead:

    [php]
    require_once '/PATH/TO/sfServiceContainerAutoloader.php';
    sfServiceContainerAutoloader::register();

    $sc = new sfServiceContainerBuilder();

    $sc->
      register('mail.transport', 'Zend_Mail_Transport_Smtp')->
      addArgument('smtp.gmail.com')->
      addArgument(array(
        'auth'     => 'login',
        'username' => '%mailer.username%',
        'password' => '%mailer.password%',
        'ssl'      => 'ssl',
        'port'     => 465,
      ))->
      setShared(false)
    ;

    $sc->
      register('mailer', '%mailer.class%')->
      addMethodCall('setDefaultTransport', array(new sfServiceReference('mail.transport')))
    ;

The creation of a service is done by calling the `register()` method, which
takes the service name and the class name, and returns a `sfServiceDefinition`
instance.

>**TIP**
>A service definition is internally represented by an object of
>class `sfServiceDefinition`. It is also possible to create one by
>hand and register it directly by using the service container
>`setServiceDefinition()` method.

The definition object implements a fluid interface and provides
methods that configure the service. In the above example, we have used the
following:

  * `addArgument()`: Adds an argument to pass to the service constructor.

  * `setShared()`: Whether the service must be unique for a container or not
    (`true` by default).

  * `addMethodCall()`: A method to call after the service has been created.
    The second argument is an array of arguments to pass to the method.

Referencing a service is now done with a `sfServiceReference` instance. This
special object is dynamically replaced with the actual service when the
referencing service is created.

During the registration phase, no service is actually created, it is just
about the description of the services. The services are only created when you
actually want to work with them. It means you can register the services in any
order without taking care of the dependencies between them. It also means you
can override an existing service definition by re-registering a service with
the same name. That's yet another simple way to override a service for testing
purposes.

>**SIDEBAR**
>The `sfServiceDefinition` Class
>
>A service has several properties that change the way it is created and
>configured:
>
> * `setConstructor()`: Sets the static method to use when the service
> is created, instead of the standard `new` construct (useful for
> factories).
>
> * `setClass()`: Sets the service class.
>
> * `setArguments()`: Sets the arguments to pass to the constructor (the
> order is of course significant).
>
> * `addArgument()`: Adds an argument for the constructor.
>
> * `setMethodCalls()`: Sets the service methods to call after
> service creation. These methods are called in the same order as the
> registration.
>
> * `addMethodCall()`: Adds a service method call to call after service
> creation. You can add a call to the same method several times if
> needed.
>
> * `setFile()`: Sets a file to include before creating a service
> (useful if the service class if not autoloaded).
>
> * `setShared()`: Whether the service must be unique for a container or
> not (`true` by default).
>
> * `setConfigurator()`: Sets a PHP callable to call after the service
> has been configured.

As the `sfServiceContainerBuilder` class implements the standard
`sfServiceContainerInterface` interface, using the service container does not
need to be changed:

    [php]
    $sc->addParameters(array(
      'mailer.username' => 'foo',
      'mailer.password' => 'bar',
      'mailer.class'    => 'Zend_Mail',
    ));

    $mailer = $sc->mailer;

The `sfServiceContainerBuilder` is able to describe any object instantiation
and configuration. We have demonstrated it with the `Zend_Mail` class, and
here is another example using the `sfUser` class from Symfony:

    [php]
    $sc = new sfServiceContainerBuilder(array(
      'storage.class'        => 'sfMySQLSessionStorage',
      'storage.options'      => array('database' => 'session', 'db_table' => 'session'),
      'user.class'           => 'sfUser',
      'user.default_culture' => 'en',
    ));

    $sc->register('dispatcher', 'sfEventDispatcher');

    $sc->
      register('storage', '%storage.class%')->
      addArgument('%storage.options%')
    ;

    $sc->
      register('user', '%user.class%')->
      addArgument(new sfServiceReference('dispatcher'))->
      addArgument(new sfServiceReference('storage'))->
      addArgument(array('default_culture' => '%user.default_culture%'))->
    ;

    $user = $sc->user;

>**NOTE**
>In the Symfony example, even though the storage object takes an
>array of options as an argument, we passed a string placeholder
>(`addArgument('%storage.options%')`). The container is smart enough
>to actually pass an array, which is the value of the placeholder.

Using PHP code to describe the services is quite simple and powerful. It gives
you a tool to create your container without duplicating too much code and to
abstract object instantiation and configuration.
