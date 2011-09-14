Using XML or YAML to describe Services
======================================

With the previous chapter, you learned how to describe services with PHP code
by using the `sfServiceContainerBuilder` class. Today, with the help of
service loaders and dumpers, you will learn how to use XML or YAML to describe
your services.

The Symfony Dependency Injection component provides helper classes that load
services using **"loader objects"**. By default, the component comes with two
of them: `sfServiceContainerLoaderFileXml` to load XML files, and
`sfServiceContainerLoaderFileYaml` to load YAML files.

But before diving into the XML and YAML notations, let's first have a look at
another part of the Symfony Dependency Injection component: the **"dumper
objects"**. A service dumper takes a container object and converts it to
another format. And of course, the component comes bundled with dumpers for
the XML and YAML formats.

To introduce the XML format, let's convert yesterday's container service
definitions to a `container.xml` file by using the
`sfServiceContainerDumperXml` dumper class.

Remember the code we used to define the `Zend_Mail` service?

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

To convert this container to an XML representation, use the following code:

    [php]
    $dumper = new sfServiceContainerDumperXml($sc);

    file_put_contents('/somewhere/container.xml', $dumper->dump());

A dumper class constructor takes a service container builder object as its
first argument and the `dump()` method introspects the container services and
converts them to another representation. If everything went fine, the
`container.xml` file should look like the following XML snippet:

    [xml]
    <?xml version="1.0" ?>

    <container xmlns="http://symfony-project.org/2.0/container">
      <parameters>
        <parameter key="mailer.username">foo</parameter>
        <parameter key="mailer.password">bar</parameter>
        <parameter key="mailer.class">Zend_Mail</parameter>
      </parameters>
      <services>
        <service id="mail.transport" class="Zend_Mail_Transport_Smtp" shared="false">
          <argument>smtp.gmail.com</argument>
          <argument type="collection">
            <argument key="auth">login</argument>
            <argument key="username">%mailer.username%</argument>
            <argument key="password">%mailer.password%</argument>
            <argument key="ssl">ssl</argument>
            <argument key="port">465</argument>
          </argument>
        </service>
        <service id="mailer" class="%mailer.class%">
          <call method="setDefaultTransport">
            <argument type="service" id="mail.transport" />
          </call>
        </service>
      </services>
    </container>

>**TIP**
>The XML format supports anonymous services. An anonymous service is a
>service that does not need a name and is defined directly in its use
>context.  It can be very convenient when you need a service that won't
>be used outside of a certain scope:
>
>     [xml]
>     <service id="mailer" class="%mailer.class%">
>       <call method="setDefaultTransport">
>         <argument type="service">
>           <service class="Zend_Mail_Transport_Smtp">
>             <argument>smtp.gmail.com</argument>
>             <argument type="collection">
>               <argument key="auth">login</argument>
>               <argument key="username">%mailer.username%</argument>
>               <argument key="password">%mailer.password%</argument>
>               <argument key="ssl">ssl</argument>
>               <argument key="port">465</argument>
>             </argument>
>           </service>
>         </argument>
>       </call>
>     </service>

Loading back the container is dead simple thanks to the XML service loader
class:

    [php]
    require_once '/PATH/TO/sfServiceContainerAutoloader.php';
    sfServiceContainerAutoloader::register();

    $sc = new sfServiceContainerBuilder();

    $loader = new sfServiceContainerLoaderFileXml($sc);
    $loader->load('/somewhere/container.xml');

As for dumpers, a loader takes a service container builder as its constructor
first argument, and the `load()` method reads the file and registers the
services into the container. The container is then useable as usual.

If you change the dumper code to use the `sfServiceContainerDumperYaml` class
instead, you will have a YAML representation of your services:

    [php]
    require_once '/PATH/TO/sfYaml.php';

    $dumper = new sfServiceContainerDumperYaml($sc);

    file_put_contents('/somewhere/container.yml', $dumper->dump());

>**NOTE**
>This will only work if you first load the sfYAML component
>(`http://svn.symfony-project.com/components/yaml/trunk/`) as it is
>required for the service container loader and dumper.

The previous container is represented as follows in YAML:

    [yml]
    parameters:
      mailer.username: foo
      mailer.password: bar
      mailer.class:    Zend_Mail

    services:
      mail.transport:
        class:     Zend_Mail_Transport_Smtp
        arguments: [smtp.gmail.com, { auth: login, username: %mailer.username%, password: %mailer.password%, ssl: ssl, port: 465 }]
        shared:    false
      mailer:
        class: %mailer.class%
        calls:
          - [setDefaultTransport, [@mail.transport]]

>**SIDEBAR**
>What's the best Format for your Service Definitions?
>
>Using the XML format gives you several advantages over the YAML one:
>
>  * When a XML file is loaded, it is automatically validated with the
>  built-in `services.xsd` file;
>
>  * The XML can be auto-completed in IDEs;
>
>  * The XML format is faster than the YAML one;
>
>  * The XML format has no external dependencies (the YAML format relies
>  on the sfYAML component).

You can of course mix and match the loaders and the dumpers to convert any
format to any other one:

    [php]
    // Convert an XML container service definitions file to a YAML one
    $sc = new sfServiceContainerBuilder();

    $loader = new sfServiceContainerLoaderFileXml($sc);
    $loader->load('/somewhere/container.xml');

    $dumper = new sfServiceContainerDumperYaml($sc);
    file_put_contents('/somewhere/container.yml', $dumper->dump());

>**TIP**
>To keep this chapter short, we won't list all possibilities of the
>YAML or XML format. But you can easily learn them by converting an
>existing container and looking at the output. You can also have a look at
>the appendices, where the formats are described in great detail.

Using YAML or XML files for configuring your services allows you to create
your services with a GUI (yet to be done...). But it also opens up a lot more
interesting possibilities.

One of the most important ones is the ability to import other 
"resources". A resource can be any other configuration file:

    [xml]
    <container xmlns="http://symfony-project.org/2.0/container">
      <imports>
        <import resource="default.xml" />
      </imports>
      <parameters>
        <!-- These parameters override the ones defined in default.xml -->
      </parameters>
      <services>
        <!-- These service definitions override the ones defined in default.xml -->
      </services>
    </container>

The `imports` section lists resources that need to be included before the
other sections are evaluated. By default, it looks for files with a path
relative to the current file, but you can also pass an array of paths to look
in as the second argument of the loader:

    [php]
    $loader = new sfServiceContainerLoaderFileXml($sc, array('/another/path'));
    $loader->load('/somewhere/container.xml');

You can even embed a YAML definition file in an XML one by defining the
`class` that is able to load the resource:

    [xml]
    <container xmlns="http://symfony-project.org/2.0/container">
      <imports>
        <import resource="default.yml" class="sfServiceContainerLoaderFileYaml" />
      </imports>
    </container>

And of course, the same goes for the YAML format:

    [yml]
    imports:
      - { resource: default.xml, class: sfServiceContainerLoaderFileXml }

The `import` facility gives you a flexible way to organize your service
definition files. It is also a great way to share and reuse definition files.
Let's talk about the web session example we introduced in the first chapter.
When you use web sessions in a test environment, the session storage object
probably needs to be mocked; on the contrary, if you have several
load-balanced web servers, the production environment needs to store its
sessions in a database like MySQL. One way to have a different configuration
based on the environment is to create several different configuration files
and import them as needed:

    [xml]
    <!-- in /framework/config/default/session.xml -->
    <container xmlns="http://symfony-project.org/2.0/container">
      <parameters>
        <parameter key="session.class">sfSessionStorage</parameter>
      </parameters>

      <!-- service definitions go here -->
    </container>

    <!-- in /project/config/session_test.xml -->
    <container xmlns="http://symfony-project.org/2.0/container">
      <imports>
        <import resource="session.xml" />
      </imports>

      <parameters>
        <parameter key="session.class">sfSessionTestStorage</parameter>
      </parameters>
    </container>

    <!-- in /project/config/session_prod.xml -->
    <container xmlns="http://symfony-project.org/2.0/container">
      <imports>
        <import resource="session.xml" />
      </imports>

      <parameters>
        <parameter key="session.class">sfMySQLSessionStorage</parameter>
      </parameters>
    </container>

Using the right configuration is trivial:

    [php]
    $loader = new sfServiceContainerLoaderFileXml($sc, array(
      '/framework/config/default/',
      '/project/config/',
    ));
    $loader->load('/somewhere/session_'.$environment.'.xml');

I can hear people crying about using XML to define the configuration, as XML
is probably not the most readable configuration format on earth. Coming from a
Symfony background, you could have written all the files in the YAML format.
But you can also decouple the service definitions from their configuration. As
you can import files from other ones, you can define services in a
`services.xml` file, and store the related configuration in a `parameters.xml`
one. You can also define parameters in a YAML file (`parameters.yml`).
Finally, there is a built-in INI loader that is able to read
parameters from a standard INI file:

    [xml]
    <!-- in /project/config/session_test.xml -->
    <container xmlns="http://symfony-project.org/2.0/container">
      <imports>
        <import resource="config.ini" class="sfServiceContainerLoaderFileIni" />
      </imports>
    </container>

    <!-- /project/config/config.ini -->
    [parameters]
    session.class = sfSessionTestStorage

>**NOTE**
>It is not possible to define services in an INI file; only parameters
>can be defined and parsed.

These examples barely scratch the surface of the container loaders and
dumpers features, but hopefully this chapter has been a good overview of the
power of the XML and YAML formats over the PHP one. And for those who are
sceptical about the performance of a container that needs to load several files
to be configured, we think you will be blown away by the next chapter.
