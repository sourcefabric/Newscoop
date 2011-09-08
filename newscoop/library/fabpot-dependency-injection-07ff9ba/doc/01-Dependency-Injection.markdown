What is Dependency Injection?
=============================

As Dependency Injection is not yet a widespread concept in the PHP world, this
chapter introduces Dependency Injection by only using plain PHP.

Through some concrete examples, we will demonstrate the problems that
Dependency Injection tries to solve and the benefits it gives to the
developer.

>**TIP**
>If you are already familiar with Dependency Injection, you can safely
>skip this chapter and start reading the next one.

Dependency Injection is probably one of the most dead simple design patterns,
and odds are you have already used Dependency Injection. However, it is
also one of the most difficult to explain well. This is probably partly
due to the nonsense examples used in most introductions to Dependency
Injection. In this chapter, we have tried to come up with examples that fit
the PHP world better. As PHP is a language mainly used for web development,
we are going to use simple Web examples.

The Problem
-----------

To overcome the statelessness of the HTTP protocol, web applications need a
way to store user information between web requests. This is of course quite
simple to achieve by using a cookie, or even better, by using the built-in PHP
session mechanism:

    [php]
    $_SESSION['language'] = 'fr';

The above code stores the user language in the `language` session variable.
So, for all subsequent requests of the same user, the `language` will be
available in the global `$_SESSION` array:

    [php]
    $user_language = $_SESSION['language'];

As Dependency Injection only makes sense in an Object-Oriented world, let's
pretend we have a `SessionStorage` class that wraps the PHP session mechanism:

    [php]
    class SessionStorage
    {
      function __construct($cookieName = 'PHP_SESS_ID')
      {
        session_name($cookieName);
        session_start();
      }

      function set($key, $value)
      {
        $_SESSION[$key] = $value;
      }

      function get($key)
      {
        return $_SESSION[$key];
      }

      // ...
    }

... and a `User` class that provides a nice high-level interface for the user:

    [php]
    class User
    {
      protected $storage;

      function __construct()
      {
        $this->storage = new SessionStorage();
      }

      function setLanguage($language)
      {
        $this->storage->set('language', $language);
      }

      function getLanguage()
      {
        return $this->storage->get('language');
      }

      // ...
    }

Those classes are simple enough and using the `User` class is also rather
easy:

    [php]
    $user = new User();
    $user->setLanguage('fr');
    $language = $user->getLanguage();

All is good and well... until you want more flexibility. What if you want to
change the session cookie name for instance? Here are some random
possibilities:

  * Hardcode the name in the `User` class in the `SessionStorage`
    constructor:

        [php]
        class User
        {
          function __construct()
          {
            $this->storage = new SessionStorage('SESSION_ID');
          }

          // ...
        }

  * Define a constant outside of the `User` class:

        [php]
        class User
        {
          function __construct()
          {
            $this->storage = new SessionStorage(STORAGE_SESSION_NAME);
          }

          // ...
        }

        define('STORAGE_SESSION_NAME', 'SESSION_ID');

  * Add the session name as a `User` constructor argument:

        [php]
        class User
        {
          function __construct($sessionName)
          {
            $this->storage = new SessionStorage($sessionName);
          }

          // ...
        }

        $user = new User('SESSION_ID');

  * Add an array of options for the storage class:

        [php]
        class User
        {
          function __construct($storageOptions)
          {
            $this->storage = new SessionStorage($storageOptions['session_name']);
          }

          // ...
        }

        $user = new User(array('session_name' => 'SESSION_ID'));

All these alternatives are quite bad. Hardcoding the session name in the
`User` class does not really solve the problem as you cannot easily change
your mind later on without changing the `User` class again. Using a constant
is also a bad idea as the `User` class now depends on a constant to be set.
Passing the session name as an argument or as an array of options is probably
the best solution, but it still smells bad. It clutters the `User` constructor
arguments with things that are not relevant to the object itself.

But there is yet another problem that cannot be solved easily: How can I
change the `SessionStorage` class? For instance, to replace it with a mock
object to ease testing. Or perhaps because you want to store the sessions in a
database table or in memory. That's impossible with the current
implementation, except if you change the `User` class.

The Solution
------------

Enter Dependency Injection. *Instead of creating the `SessionStorage` object
inside the `User` class, let's inject the `SessionStorage` object in the
`User` object by passing it as a constructor argument*:

    [php]
    class User
    {
      function __construct($storage)
      {
        $this->storage = $storage;
      }

      // ...
    }

That's Dependency Injection. Nothing more! Using the `User` class is now a bit
more involving as you first need to create the `SessionStorage` object:

    [php]
    $storage = new SessionStorage('SESSION_ID');
    $user = new User($storage);

Now, configuring the session storage object is dead simple, and replacing the
session storage class is also very easy. And everything is possible without
changing the `User` class thanks to the better separation of concerns.

The [Pico Container website](http://www.picocontainer.org/injection.html)
describes Dependency Injection like this:

  "Dependency Injection is where components are given their dependencies
  through their constructors, methods, or directly into fields."

>**TIP**
>As with any other design pattern, Dependency Injection
>also has some anti-patterns.  The
>[Pico Container website](http://www.picocontainer.org/)
>describes some of them.

Dependency Injection is not restricted to constructor injection:

  * Constructor Injection:

        [php]
        class User
        {
          function __construct($storage)
          {
            $this->storage = $storage;
          }

          // ...
        }

  * Setter Injection:

        [php]
        class User
        {
          function setSessionStorage($storage)
          {
            $this->storage = $storage;
          }

          // ...
        }

  * Property Injection:

        [php]
        class User
        {
          public $sessionStorage;
        }

        $user->sessionStorage = $storage;

As a rule of thumb, constructor injection is best for required dependencies,
like in our example, and setter injection is best for optional dependencies,
like for a cache object for instance.

Nowadays, most modern PHP frameworks heavily use Dependency Injection to
provide a set of decoupled but cohesive components:

    [php]
    // symfony: A constructor injection example
    $dispatcher = new sfEventDispatcher();
    $storage = new sfMySQLSessionStorage(array('database' => 'session', 'db_table' => 'session'));
    $user = new sfUser($dispatcher, $storage, array('default_culture' => 'en'));

    // Zend Framework: A setter injection example
    $transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', array(
      'auth'     => 'login',
      'username' => 'foo',
      'password' => 'bar',
      'ssl'      => 'ssl',
      'port'     => 465,
    ));

    $mailer = new Zend_Mail();
    $mailer->setDefaultTransport($transport);
