The Symfony Service Container
=============================

Until now, we have talked about general concepts. The first two introductory
chapters were important to better understand the implementation we will talk
about in this chapter and in the following ones. It is now time to dive into
the Symfony Service Container component implementation.

The Symfony Dependency Injection Container is managed by a class named
`sfServiceContainer`. It is a very lightweight class that implements the basic
features we talked about in the previous article.

In Symfony speak, *a service is any object managed by the container*. In the
`Zend_Mail` example from the last chapter, we had two of them: the `mailer`
service and the `mail_transport` service:

    [php]
    class Container
    {
      static protected $shared = array();

      protected $parameters = array();

      public function __construct(array $parameters = array())
      {
        $this->parameters = $parameters;
      }

      public function getMailTransport()
      {
        return new Zend_Mail_Transport_Smtp('smtp.gmail.com', array(
          'auth'     => 'login',
          'username' => $this->parameters['mailer.username'],
          'password' => $this->parameters['mailer.password'],
          'ssl'      => 'ssl',
          'port'     => 465,
        ));
      }

      public function getMailer()
      {
        if (isset(self::$shared['mailer']))
        {
          return self::$shared['mailer'];
        }

        $class = $this->parameters['mailer.class'];

        $mailer = new $class();
        $mailer->setDefaultTransport($this->getMailTransport());

        return self::$shared['mailer'] = $mailer;
      }
    }

If we make the `Container` class extend the `sfServiceContainer` Symfony
class, we can simplify the code a bit:

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

That's not much, but it will give us a more powerful and clean interface. Here
are the main changes we made:

 * The method names have been suffixed with `Service`. By convention, a
   service is to be defined by a method prefixed by `get` and suffixed by
   `Service`. Each service has a unique identifier, which is the underscored
   version of the method name without the prefix and suffix. By defining a
   `getMailTransportService()` method, we have defined a service named
   `mail_transport`.

 * The methods are now protected. We will see in a minute how to retrieve
   services from the container.

 * The container can be used as an array to get parameter values
   (`$this['mailer.class']`).

>**TIP**
>A service identifier must be unique and must be made of letters,
>numbers, underscores, and dots.  Dots are useful to define
>"namespaces" within your container (for instance `mail.mailer` and
>`mail.transport`).

Let's see how to use the new container class:

    [php]
    require_once '/PATH/TO/sfServiceContainerAutoloader.php';
    sfServiceContainerAutoloader::register();

    $sc = new Container(array(
      'mailer.username' => 'foo',
      'mailer.password' => 'bar',
      'mailer.class'    => 'Zend_Mail',
    ));

    $mailer = $sc->mailer;

Now, because the `Container` class extends the `sfServiceContainer` class, we
can enjoy a cleaner interface:

  * Services are accessible via a uniform interface:

        [php]
        if ($sc->hasService('mailer'))
        {
          $mailer = $sc->getService('mailer');
        }

        $sc->setService('mailer', $mailer);

  * As a shortcut, services are also accessible by using the class property
    notation:

        [php]
        if (isset($sc->mailer))
        {
          $mailer = $sc->mailer;
        }

        $sc->mailer = $mailer;

  * Parameters are accessible via a uniform interface:

        [php]
        if (!$sc->hasParameter('mailer_class'))
        {
          $sc->setParameter('mailer_class', 'Zend_Mail');
        }

        echo $sc->getParameter('mailer_class');

        // Override all parameters of the container
        $sc->setParameters($parameters);

        // Adds parameters
        $sc->addParameters($parameters);

  * As a shortcut, parameters are also accessible by using the container like
    an array:

        [php]
        if (!isset($sc['mailer.class']))
        {
          $sc['mailer.class'] = 'Zend_Mail';
        }

        $mailerClass = $sc['mailer.class'];

  * You can also iterate over all services of a container:

        [php]
        foreach ($sc as $id => $service)
        {
          echo sprintf("Service %s is an instance of %s.\n", $id, get_class($service));
        }

Using the `sfServiceContainer` class is very useful when you have a very small
number of services to manage; even if you still need to do a lot of groundwork
yourself, and duplicate a lot of code. But, if the number of services to be
managed starts to grow beyond a few of them, we need a better way to describe
the services.

That's why, most of the time, you don't use the `sfServiceContainer` class
directly. It was nonetheless important to take some time to describe it as it
is the cornerstone of the Symfony dependency injection container
implementation.
