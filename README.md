<a href="http://www.sourcefabric.org/en/newscoop/">![Logo](newscoop/admin-style/images/newscoop_logo_big.png)
===
[![Build Status](https://travis-ci.org/sourcefabric/Newscoop.svg?branch=master)](https://travis-ci.org/sourcefabric/Newscoop)

[Newscoop][1] is the open content management system for professional journalists.

Features for the modern newsroom include multiple author management, issue-and-section based publishing, geolocation and multilingual content management. The enterprise-standard journalist’s dashboard and a templating engine supporting anything from HTML5 to mobile complete this fast production and publishing system. Read more about Newscoop history on his [wikipedia page][5].

Quick links to our resources are:

* Manuals for Newscoop [http://manuals.sourcefabric.org][6]
* Forums and mailing lists: [http://forum.sourcefabric.org][7]
* Bug Tracking: [http://dev.sourcefabric.org/browse/CS][8]
* Public source code hosting: [http://github.com/sourcefabric/Newscoop][9]
* Download link: [https://github.com/sourcefabric/Newscoop/releases/latest][10]
* Developer's wiki: [https://wiki.sourcefabric.org/display/CS/][11]
* Developer's blog: [https://dev-blog.sourcefabric.org][12]
* Plugins Development’s documentation: [http://docs.sourcefabric.org/projects/newscoop-plugins/en/latest/][15]
* RESTful API documentation: [http://docs.sourcefabric.org/projects/newscoop-restful-api/en/master/][16]
* Developer's Cookbooks: [http://docs.sourcefabric.org/projects/newscoop-cookbook/en/latest/][17]

## Installation

**Using console command**

	php application/console newscoop:install --fix --database_name newscoop --database_user root --database_password password

Default Admin Panel account is `admin` with password `password`.

For more details run:

	php application/console newscoop:install --help

**Using docker**

Make sure docker and docker compose are installed (boot2docker or docker machine on OSX as well). Add the line ```127.0.0.1 newscoop.docker``` to your ```/etc/hosts``` file. OSX users should use the ip of their virtualbox (e.g. ```boot2docker ip```).

For running Newscoop in production mode:

```bash
docker-compose build
docker-compose up
```

For running Newscoop in development mode:

```bash
docker-compose -f docker-compose-dev.yml build
docker-compose -f docker-compose-dev.yml up
```

## Requirements

- PHP version must be at least 5.4 on Debian and CentOS to run Newscoop properly. See [PHP bug][14].

- MySQL can't work in "strict mode". Newscoop will currently break in many places when "strict mode" is enabled.

- If you use `E_DEPRECATED` error_reporting level in PHP 5.6, you can get the behaviour described in this [PHP bug][18] report.
  `always_populate_raw_post_data` must be set to `-1` in your `php.ini` file.

**Compatibility**

| Browsers | Tablets |  Smartphones |
| -------- | ------- | ----------- |
| Safari 7.1+| not supported |  not supported |
| Google Chrome 31+ |  not supported | not supported |
| Internet Explorer 9+ | not supported | not supported |
| Firefox 35+ |  not supported |  not supported |
| Opera 27+ | not supported |  not supported |

## REST API documentation

REST API documentation will be available under the link: `http://www.domain.com/documentation/rest-api/` after Newscoop installation is done.

## How to contribute

Only 4 steps:

* Fork sourcefabric/Newscoop repository - [how to fork][2].
* Clone your fork
* Create new local feature branch - [how to create branch][3]
* Create pull request with your feature/bugfix - [how to create pull request][4]

## License

Newscoop is licensed under the GPL3 license.

[1]: http://www.sourcefabric.org/en/newscoop/
[2]: https://help.github.com/articles/fork-a-repo
[3]: http://learn.github.com/p/branching.html
[4]: https://help.github.com/articles/creating-a-pull-request
[5]: http://en.wikipedia.org/wiki/Newscoop
[6]: http://manuals.sourcefabric.org
[7]: http://forum.sourcefabric.org
[8]: http://dev.sourcefabric.org/browse/CS
[9]: http://github.com/sourcefabric/Newscoop
[10]: https://github.com/sourcefabric/Newscoop/releases/latest
[11]: https://wiki.sourcefabric.org/display/CS/
[12]: https://dev-blog.sourcefabric.org/en/blogs/?filter=1
[13]: https://github.com/sourcefabric/Newscoop/blob/master/newscoop/docs/INSTALL-ubuntu.md
[14]: https://bugs.php.net/bug.php?id=54709
[15]: http://docs.sourcefabric.org/projects/newscoop-plugins/en/latest/
[16]: http://docs.sourcefabric.org/projects/newscoop-restful-api/en/master/
[17]: http://docs.sourcefabric.org/projects/newscoop-cookbook/en/latest/
[18]: https://bugs.php.net/bug.php?id=66763
