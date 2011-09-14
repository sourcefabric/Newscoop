The Need for Speed
==================

During the first five chapters of this book, we have progressively introduced
the main concepts behind this simple and useful design pattern. We have also
talked about the implementation of a lightweight PHP container that will be
one of the core components of Symfony 2.

But with the introduction of the XML and YAML configuration files, you might
have become a bit sceptical about the performance of the container itself. Even
if services are lazy loading, reading a bunch of XML or YAML files on each
request and creating objects by using introspection is probably not very
efficient in PHP. And because the container is almost always the cornerstone
of any application using it, its speed does matter a lot.

On one hand, using XML or YAML to describe services and their
configuration is very powerful and flexible:

    [php]
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
            <argument key="port">true</argument>
          </argument>
        </service>
        <service id="mailer" class="%mailer.class%">
          <call method="setDefaultTransport">
            <argument type="service" id="mail.transport" />
          </call>
        </service>
      </services>
    </container>

But, on the other hand, defining the service container as a plain PHP class
gives you the full speed, as seen during the second chapter of this book:

    [php]
    class Container extends sfServiceContainer
    {
      static protected $shared = array();

      protected function getMailTransportService()
      {
        return new Zend_Mail_Transport_Smtp('smtp.gmail.com', array(
          'auth'     => 'login',
          'username' => $this['mailer.username'],
          'password' => $this['mailer.password'],
          'ssl'      => 'ssl',
          'port'     => 465,
        ));
      }

      protected function getMailerService()
      {
        if (isset(self::$shared['mailer']))
        {
          return self::$shared['mailer'];
        }

        $class = $this['mailer.class'];

        $mailer = new $class();
        $mailer->setDefaultTransport($this->getMailTransportService());

        return self::$shared['mailer'] = $mailer;
      }
    }

The above code does the bare minimum to provide flexibility thanks to the
configuration variables, and still be very fast.

How can you have the best of both worlds? That's quite simple. The Symfony
Dependency Injection component provides yet another built-in dumper: a **PHP
dumper**. This dumper can convert any service container to plain PHP code.
That's right, it is able to generate the code you could have written by hand
in the first place.

Let's again use our `Zend_Mail` example and for brevity's sake, let's use the
XML definition file created in the previous chapter:

    [php]
    $sc = new sfServiceContainerBuilder();

    $loader = new sfServiceContainerLoaderFileXml($sc);
    $loader->load('/somewhere/container.xml');

    $dumper = new sfServiceContainerDumperPhp($sc);

    $code = $dumper->dump(array('class' => 'Container'));

    file_put_contents('/somewhere/container.php', $code);

As for any other dumper, the `sfServiceContainerDumperPhp` class takes a
container as its constructor first argument. The `dump()` method takes an
array of options, and one of them is the name of the class to generate.

Here is the generated code:

    [php]
    class Container extends sfServiceContainer
    {
      protected $shared = array();

      public function __construct()
      {
        parent::__construct($this->getDefaultParameters());
      }

      protected function getMailTransportService()
      {
        $instance = new Zend_Mail_Transport_Smtp('smtp.gmail.com', array(
          'auth' => 'login',
          'username' => $this->getParameter('mailer.username'),
          'password' => $this->getParameter('mailer.password'),
          'ssl' => 'ssl',
          'port' => 465
        ));

        return $instance;
      }

      protected function getMailerService()
      {
        if (isset($this->shared['mailer'])) return $this->shared['mailer'];

        $class = $this->getParameter('mailer.class');
        $instance = new $class();
        $instance->setDefaultTransport($this->getMailTransportService());

        return $this->shared['mailer'] = $instance;
      }

      protected function getDefaultParameters()
      {
        return array (
          'mailer.username' => 'foo',
          'mailer.password' => 'bar',
          'mailer.class' => 'Zend_Mail',
        );
      }
    }

If you have a closer look at the code generated by the dumper, you will notice
that the code is very similar to the one we wrote by hand.

>**NOTE**
>The generated code does not use the shortcut notation to access
>parameters and services in order to be as fast as possible.

By using the `sfServiceContainerDumperPhp` dumper, you can have the best of
both worlds: the flexibility of the XML or YAML format to describe and
configure your services, and the speed of an optimized and auto-generated PHP
file.

Of course, as projects have almost always different settings for different
environments, you can of course generate different container classes, based on
the environment or on a debugging setting. Here is a small snippet of PHP code
that illustrates how to build the container dynamically for the very first
request, and use a cached one for all other requests when not in debugging
mode:

    [php]
    $name = 'Project'.md5($appDir.$isDebug.$environment).'ServiceContainer';
    $file = sys_get_temp_dir().'/'.$name.'.php';

    if (!$isDebug && file_exists($file))
    {
      require_once $file;
      $sc = new $name();
    }
    else
    {
      // build the service container dynamically
      $sc = new sfServiceContainerBuilder();
      $loader = new sfServiceContainerLoaderFileXml($sc);
      $loader->load('/somewhere/container.xml');

      if (!$isDebug)
      {
        $dumper = new sfServiceContainerDumperPhp($sc);

        file_put_contents($file, $dumper->dump(array('class' => $name));
      }
    }

That wraps up our tour of the Symfony 2 Dependency Injection Container.

Before closing this book, let's see yet another great feature of the dumpers.
Dumpers can do a lot of different things, and to demonstrate the decoupling of
the implementation of the component, we have implemented a Graphviz dumper.
What for? To help you visualize your services and their dependencies.

First, let's see how to use it on our example container:

    [php]
    $dumper = new sfServiceContainerDumperGraphviz($sc);
    file_put_contents('/somewhere/container.dot', $dumper->dump());

The Graphviz dumper generates a `dot` representation of your container:

    digraph sc {
      ratio="compress"
      node [fontsize="11" fontname="Myriad" shape="record"];
      edge [fontsize="9" fontname="Myriad" color="grey" arrowhead="open" arrowsize="0.5"];

      node_service_container [label="service_container\nsfServiceContainerBuilder\n", shape=record, fillcolor="#9999ff", style="filled"];
      node_mail_transport [label="mail.transport\nZend_Mail_Transport_Smtp\n", shape=record, fillcolor="#eeeeee", style="dotted"];
      node_mailer [label="mailer\nZend_Mail\n", shape=record, fillcolor="#eeeeee", style="filled"];
      node_mailer -> node_mail_transport [label="setDefaultTransport()" style="dashed"];
    }

This representation can be converted to an image by using the
[`dot` program](http://graphviz.org/):

    $ dot -Tpng /somewhere/container.dot > /somewhere/container.png

![Zend_Mail container PNG representation](http://fabien.potencier.org/media/articles/di/zend-mail.png)

For this simple example, the visualization has no real added value, but as
soon as you start having more than a few services, it can be quite useful...
and beautiful.

>**NOTE**
>The Graphviz dumper `dump()` method takes a lot of different options
>to tweak the output of the graph. Have a look at the source code to
>discover the default values for each of them:
>
>  * `graph`: The default options for the whole graph
>  * `node`: The default options for nodes
>  * `edge`: The default options for edges
>  * `node.instance`: The default options for services that are defined
>  directly by object instances
>  * `node.definition`: The default options for services that are
>  defined via service definition instances
>  * `node.missing`: The default options for missing services

Here is the graph for an hypothetical CMS using the new Symfony 2 Templating
Framework:

![Symfony 2 Templating Framework](http://fabien.potencier.org/media/articles/di/template.png)
