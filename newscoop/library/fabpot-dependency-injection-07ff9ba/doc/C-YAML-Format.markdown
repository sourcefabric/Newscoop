Appendix C - The YAML Format
============================

This appendix describes the YAML format used to describe parameters and
services.

### Format

The YAML files cannot be validated like the XML ones. So, you need to be
careful when writing them.

The YAML file can define three main entries:

  * `parameters`: Defines the container parameters
  * `services`:   Defines the container services
  * `imports`:    Defines the files to import before parsing the file

### Placeholders

Most keys and values can use placeholders. A placeholder is a string enclosed
in `%` signs, which is replaced dynamically at runtime by the corresponding
parameter value.

### Precedence Rules

When loading a YAML resource, service definitions override the current
defined ones.

But for parameters, they are overridden by the current ones. It allows the
parameters passed to the container constructor to have precedence over the
loaded ones.

    [php]
    $container = new sfServiceContainerBuilder(array('foo' => 'bar'));
    $loader = new sfServiceContainerLoaderFileYaml($container);
    $loader->load('services.yml');

In the above example, even if the loaded resource defines a `foo` parameter,
the value will still be 'bar' as defined in the builder constructor.

Parameters
----------

The parameters are defined by a YAML array. All the YAML rules apply:

    [yml]
    parameters:
      foo: bar
      values:
        - true
        - false
        - 0
        - 1000.3

Parameter values can contain placeholders:

    [yml]
    parameters:
      foo: bar
      bar: %foo%
      baz: The placeholders can be %foo% embedded in a string

The previous YAML snippet is equivalent to the following PHP code:

    [php]
    array('foo' => true, 'bar' => true, 'baz' => 'The placeholders can be true embedded in a string')

You can escape a `%` by doubling it:

    [yml]
    parameters:
      foo: The string has no placeholder... %%foo

A parameter can also be a reference to a service:

    [xml]
    foo: @bar

Services
--------

Services are defined by creating a hash where the key represents the unique
identifier of the service:

    [yml]
    services:
      foo: { class: FooClass }

The `class` entry is the minimum required to define a service.

### Attributes

A `service` entry supports the following attributes:

  * `class`: The class name of the service (mandatory, can be a placeholder).

  * `shared`: Whether the service should be shared or not.

  * `constructor`: The constructor static method to call to create an instance
    of the service.

  * `file`: An absolute file name to require before instantiating the
    service (can use placeholders).

  * `arguments`: Arguments to pass to the constructor (the order is
    significant, values can use placeholders).

    Arguments are defined using the YAML array notation. It also supports
    references to services by using the `@` sign:

        [yml]
        services:
          foo: { class: FooClass, arguments: [foo, @bar] }
          bar: { class: BarClass }

  * `configurator`: A callable to call to configure the service after
    instantiation (the callable is passed the service instance as an
    argument).

    The configurator can be a function:

        [yml]
        foo: { class: FooClass, configurator: configure }

    or a method called on an existing service:

        [yml]
        foo: { class: FooClass, configurator: [@baz, configure] }

    or a static method:

        [yml]
        foo: { class: FooClass, configurator: [BazClass, configure] }

  * `calls`: An array of methods to call on the service instance after
    instantiation. Each method call is an array where the first element is the
    method name, and the second one an array of arguments to pass to it.

Here is an example that uses most possibilities:

    [yml]
      foo:
        class: FooClass
        constructor: getInstance
        shared: false
        file: %path%/foo.php
        arguments: [foo, @foo, [true, false]]
        configurator: [@baz, configure]
        calls:
          - [ setBar, [ foo, @foo, [true, false] ] ]

### Aliases

You can define an alias for an existing service by simply assigning a service
reference to the alias:

    [php]
    alias_for_foo: @foo

The `alias_for_foo` service is now an alias of the `foo` service.

Imports
-------

Before the YAML file is parsed, the component first reads the import resources
defined under the `imports` entry:

    [yml]
    imports:
      - { resource: services.yml }

If you import many resources, they are interpreted in the same order as they
are defined. As one resource can override previous defined parameters and
services, the order is significant.

If the resource is a relative path, the resource is first looked for in the
same directory as the current YAML file. If it is not found, the paths passed
to the loader constructor second argument will be looked for one after the
other.

By default, the same loader as the current one will be used, but you can also
use any other loader class by defining a `class` attribute:

    [yml]
    imports:
      - { resource: services.yml }
      - { resource: "../ini/parameters.ini", class: sfServiceContainerLoaderFileIni }

The same paths as the original loaders will be passed to the new one.

As parameters are seen as simple key/value pairs by the component, when a
value is overridden, the value is replaced with the new value.

For instance, if you have the following two YAML files:

    [yml]
    file1.yml
    parameters:
      complex: [true, false]

    file2.yml
    parameters:
      complex: foo

When loading `file1.yml` and `file2.yml` in this order, the value of complex
will be "foo".
