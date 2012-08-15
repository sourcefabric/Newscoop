#Newscoop REST API

Codename: Gimme

Full Name: Newscoop REST API

Endpoint: Example.com/api/

Documentation: https://wiki.sourcefabric.org/display/CS/Newscoop+REST+API+Reference

## API Resources
You can check all defined routes by console:

```bash
php gimme/app/console router:debug
```
You can override method by providing "method" as a query parameter with option for example as a value.

```php
/api/articles?method=option
```

## BDD Testing

Features directory: features/
Behat bootsraped files directory: features/bootstrap/

Run test:

```bash
php vendor/bin/behat
```

