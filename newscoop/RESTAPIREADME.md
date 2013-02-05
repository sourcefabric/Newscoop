#Newscoop REST API

Codename: Gimme  
Full Name: Newscoop REST API  
Endpoint: example.com/api/  
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

## Available SDK's

* [PHP SDK][1]
* [JavaScript SDK][2]

## BDD Testing

Features directory: features/  
Behat bootsraped files directory: features/bootstrap/

Run test:

```bash
php vendor/bin/behat
```

[1]: https://github.com/sourcefabric/newscoop-api-php-sdk
[2]: https://github.com/sourcefabric/newscoop-api-js-sdk