Introduction
============

This book is about *Symfony Dependency Injection*, a PHP library part of the
Symfony Components project. Its official website is at
http://components.symfony-project.org/dependency_injection/.

>**SIDEBAR**
>About the Symfony Components
>
>[Symfony Components](http://components.symfony-project.org/) are
>standalone PHP classes that can be easily used in any
>PHP project. Most of the time, they have been developed as part of the
>[Symfony framework](http://www.symfony-project.org/), and decoupled from the
>main framework later on. You don't need to use the Symfony MVC framework to use
>the components.

What is it?
-----------

Symfony Dependency Injection is a PHP library that provides a lightweight and
robust Dependency Injection Container for classes that implement the
Dependency Injection pattern.

Dependency Injection is a best practice to follow if you want to easily reuse
your classes and provides an elegant way to facilitate unit testing.

A Dependency Injection Container manages object instantiation and
configuration for you.

If you are interested in learning more about Dependency Injection, you can read the
[Martin Fowler introduction](http://www.martinfowler.com/articles/injection.html)
or the excellent
[Jeff More presentation](http://www.procata.com/talks/phptek-may2007-dependency.pdf).
You can also have a look at a
[presentation](http://fabien.potencier.org/talk/19/decouple-your-code-for-reusability-ipc-2008)
I gave on Dependency Injection, where I talk in more detail
on the exact same example as the one used in this book.

### Fast

One of the goals of Symfony Dependency Injection is to be as fast as possible.
With the possibility to dump any container to a plain PHP file, you can use
the library without any overhead whatsoever, certified.

### Open-Source

Released under the MIT license, you are free to do whatever you want, even in
a commercial environment. You are also encouraged to contribute.

### Built on the shoulders of giants

Symfony Dependency Injection has its roots in the great Spring framework. But
instead of being a straight port of a Java container, the library has been
rethought and redesigned to take into account the PHP platform specificities.

### Easy to use

There is only one archive to download, and you are ready to go. No
configuration, and no installation. Drop the files in a directory and start
using it today in your projects.

### Fun to use

Thanks to the great flexibility of the library, Symfony Dependency Injection
comes bundled with a Graphviz dumper, allowing you to visualize the graph of
your objects and ease the debugging of your container.

### Used by popular Projects

Symfony Dependency Injection is one of the fundamental libraries behind the
Symfony 2 framework. It is also used by many other big "enterprise-like"
projects.

### Documented

Symfony Dependency Injection is fully documented, with a dedicated online
book, and of course a full API documentation.

### Unit tested

The library is fully unit-tested. With 100% code coverage, the library is
stable and ready to be used in large projects.

### Flexible

Symfony Dependency Injection is flexible enough for all your needs, even the
most complex ones. Thanks to an open architecture, you can implement your own
dumpers and loaders (by default, it comes with full support for XML, YAML,
PHP, INI, and Graphviz).

Installation
------------

Symfony Dependency Injection can be installed by downloading the source code
as a [tar](http://github.com/fabpot/dependency-injection/tarball/master) archive or a
[zip](http://github.com/fabpot/dependency-injection/zipball/master) one.

To stay up-to-date, you can also use the official Subversion
[repository](http://svn.symfony-project.com/components/dependency_injection/).

If you are a Git user, there is an official
[mirror](http://github.com/fabpot/dependency-injection), which is updated every 10 minutes.

If you prefer to install the component globally on your machine, you can use
the symfony [PEAR](http://pear.symfony-project.com/) channel server.

Support
-------

Support questions and enhancements can be discussed on the
[mailing-list](http://groups.google.com/group/symfony-components).

If you find a bug, you can create a ticket at the symfony
[trac](http://trac.symfony-project.org/newticket) under the *dependency_
_injection* component.

License
-------

The Symfony Dependency Container component is licensed under the *MIT
license*:

>Copyright (c) 2008-2009 Fabien Potencier
>
>Permission is hereby granted, free of charge, to any person obtaining a copy
>of this software and associated documentation files (the "Software"), to deal
>in the Software without restriction, including without limitation the rights
>to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
>copies of the Software, and to permit persons to whom the Software is furnished
>to do so, subject to the following conditions:
>
>The above copyright notice and this permission notice shall be included in all
>copies or substantial portions of the Software.
>
>THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
>IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
>FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
>AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
>LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
>OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
>THE SOFTWARE.
