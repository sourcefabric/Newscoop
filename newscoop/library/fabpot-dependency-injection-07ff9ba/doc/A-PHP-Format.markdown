Appendix A - The PHP Format
===========================

This appendix describes the PHP format used to describe services.

### Format

To describe services with PHP, you can use the
`sfServiceContainerBuilder` class:

    [php]
    $container = new sfServiceContainerBuilder();

### Placeholders

Most keys and values can use placeholders. A placeholder is a string enclosed
in `%` signs, which is replaced dynamically at runtime by the corresponding
parameter value.

Services
--------

Each service is described by a `sfServiceDefinition` object. Adding
a service to a container can be done by using the
`setServiceDefinition()` method. It takes the service name and a
`sfServiceDefinition` instance:

    [php]
    $definition = new sfServiceDefinition('FooClass');
    $container->setServiceDefinition('foo', $definition);

`sfServiceContainerBuilder` also supports a fluid interface to
dynamically register services:

    [php]
    $container->register('foo', 'FooClass');

The `register()` method returns a `sfServiceDefinition` object,
based on the class name you passed as its second argument. So the
two examples are strictly equivalent.

From now on, we will use the fluid interface for our examples.

### Shared Services

By default, a service is shared. Whenever you get a specific service
from the container, it will return the same instance of it:

    [php]
    $container->getService('foo') === $container->getService('foo')

If you want to get a new instance each time you get the service, you
need to declare it as being non-shared:

    [php]
    $container
      ->register('foo', 'FooClass')
      ->setShared(false)
    ;

Now, each time you get the `foo` service, you will have a new
instance of it:

    [php]
    $container->getService('foo') !== $container->getService('foo')

### Constructor

When the container creates a service, it uses the `new` operator of
PHP. If the class constructor is protected because new instances of
it should be created by a static method instead (your class is a
factory or a singleton for instance), describe it by using the
`setConstructor()` method:

    [php]
    $container
      ->register('foo', 'FooClass')
      ->setConstructor('getInstance')
    ;

### File

By default, the service container relies on autoloading for service
creation. But if the class is not autoloaded, you need to provide a
file that will be required by the container just before it creates
an instance of the class via the `setFile()` method:

    [php]
    $container
      ->register('foo', 'FooClass')
      ->setFile('/path/to/FooClass.php')
    ;

You can also use placeholders in the path:

    [php]
    $container
      ->register('foo', 'FooClass')
      ->setFile('/path/to/%foo.class_file%.php')
    ;

### Arguments

If the class constructor of the service need to be passed some
arguments, you can define them with the `addArgument()` method:

    [php]
    $container
      ->register('foo', 'FooClass')
      ->addArgument('foo')
      ->addArgument(array(1, 2))
    ;

>**NOTE**
>The order in which you register the arguments must be the same as the
>one defined by the constructor.

Each argument can use placeholders.

If a service need another one to be injected in the constructor,
pass an instance of the `sfServiceReference` class:

    [php]
    $container
      ->register('foo', 'FooClass')
      ->addArgument(new sfServiceReference('bar'))
    ;

>**NOTE**
>You can also use the `setArguments()` method to define all arguments in
>one call. It takes an array of arguments to pass to the constructor as
>its unique argument.

### Configurator

After the service is instantiated, the container can call a
configurator. A configurator is a callable that will be able to
further configure the service. The callable is passed the service
instance as an argument.

The configurator can be a function:

    [php]
    $container
      ->register('foo', 'FooClass')
      ->setConfigurator('configure')
    ;

or a method called on an existing service:

    [php]
    $container
      ->register('foo', 'FooClass')
      ->setConfigurator(array(new sfServiceReference('baz'), 'configure'))
    ;

or a static method:

    [php]
    $container
      ->register('foo', 'FooClass')
      ->setConfigurator(array('BazClass', 'configureStatic'))
    ;

### Method Calls

The `sfServiceDefinition` class also supports method injection via
the `addMethodCall()` method:

    [php]
    $container
      ->register('foo', 'FooClass')
      ->addMethodCall('configure', array('foo'))
      ->addMethodCall(array('BazClass', 'configureStatic'))
      ->addMethodCall(array('BazClass', 'configureStatic'))
    ;

The first argument is the method to call on the service, and the
second one is an array of arguments to use when calling it.

### Aliases

You can define an alias for an existing service by using the `setAlias()`
method:

    [php]
    $container->setAlias('alias_for_foo', 'foo');

The `alias_for_foo` service is now an alias of the `foo` service.

