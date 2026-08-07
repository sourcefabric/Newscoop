# Composer Dependency Updates for PHP 8.3/8.4 Compatibility

## Overview
This document details all changes made to `composer.json` to upgrade dependencies to versions compatible with PHP 8.3 and PHP 8.4.

## Changes Made

### PHP Version Requirement
**Before:** `"php": "^8.3"`
**After:** `"php": "^8.3"` (unchanged, already correct)

### Core Framework Updates

#### Symfony Framework
**Before:** `"symfony/symfony": "^6.4"`
**After:** `"symfony/symfony": "^7.2"`
**Reason:** Symfony 7.x provides full PHP 8.3/8.4 compatibility and latest security patches.

#### Doctrine ORM
**Before:** `"doctrine/orm": "^2.17"`
**After:** `"doctrine/orm": "^3.3"`
**Reason:** Doctrine ORM 3.x is required for PHP 8.3+ compatibility with improved type safety.

#### Doctrine Common
**Before:** `"doctrine/common": "^2.13"`
**After:** `"doctrine/common": "^3.4"`
**Reason:** Match Doctrine ORM 3.x requirements.

#### Doctrine Bundle
**Before:** `"doctrine/doctrine-bundle": "^2.10"`
**After:** `"doctrine/doctrine-bundle": "^2.13"`
**Reason:** Latest version for Symfony 7 compatibility.

### Twig Template Engine

#### Twig Core (NEW)
**Added:** `"twig/twig": "^3.18"`
**Reason:** Explicitly require Twig 3.x for Symfony 7 compatibility.

#### Twig Extensions
**Before:** `"twig/extensions": "^1.5"`
**After:** `"twig/extensions": "^1.5"` (unchanged)
**Note:** This package is deprecated but still functional.

### Symfony Bundles

#### Monolog Bundle
**Before:** `"symfony/monolog-bundle": "^3.8"`
**After:** `"symfony/monolog-bundle": "^3.10"`
**Reason:** Latest version with PHP 8.3 fixes.

#### Sensio Distribution Bundle
**Removed:** `"sensio/distribution-bundle": "^5.0"`
**Reason:** Deprecated and incompatible with Symfony 7. Use Symfony Flex instead.

#### Sensio Generator Bundle
**Removed:** `"sensio/generator-bundle": "^3.1"`
**Reason:** Deprecated in favor of Symfony MakerBundle.

#### Sensio Framework Extra Bundle
**Before:** `"sensio/framework-extra-bundle": "^6.2"`
**After:** `"sensio/framework-extra-bundle": "^6.2"` (unchanged)
**Note:** Still compatible but consider migrating to attributes.

### REST API & Serialization

#### FOS Rest Bundle
**Before:** `"friendsofsymfony/rest-bundle": "^3.5"`
**After:** `"friendsofsymfony/rest-bundle": "^3.7"`
**Reason:** Latest version with PHP 8.3 compatibility fixes.

#### JMS Serializer Bundle
**Before:** `"jms/serializer-bundle": "^5.0"`
**After:** `"jms/serializer-bundle": "^5.5"`
**Reason:** Bug fixes and PHP 8.3 improvements.

#### JMS Serializer
**Before:** `"jms/serializer": "^3.28"`
**After:** `"jms/serializer": "^3.32"`
**Reason:** Match bundle version requirements.

### Pagination & Components

#### KNP Paginator Bundle
**Before:** `"knplabs/knp-paginator-bundle": "^5.9"`
**After:** `"knplabs/knp-paginator-bundle": "^6.0"`
**Reason:** Version 6.x required for Symfony 7 compatibility.

#### KNP Components
**Before:** `"knplabs/knp-components": "^4.0"`
**After:** `"knplabs/knp-components": "^5.0"`
**Reason:** Match paginator bundle 6.x requirements.

#### KNP Menu Bundle
**Before:** `"knplabs/knp-menu-bundle": "^3.4"`
**After:** `"knplabs/knp-menu-bundle": "^3.5"`
**Reason:** Minor update for compatibility.

#### KNP Menu
**Before:** `"knplabs/knp-menu": "^3.5"`
**After:** `"knplabs/knp-menu": "^3.6"`
**Reason:** Match bundle version.

### JavaScript Routing

#### FOS JS Routing Bundle
**Before:** `"friendsofsymfony/jsrouting-bundle": "^3.2"`
**After:** `"friendsofsymfony/jsrouting-bundle": "^3.5"`
**Reason:** Latest version with bug fixes.

### Image Processing

#### Imagine
**Before:** `"imagine/imagine": "^1.3"`
**After:** `"imagine/imagine": "^1.5"`
**Reason:** PHP 8.3 compatibility fixes.

### Security

#### reCAPTCHA Bundle
**Before:** `"newscoop/recaptcha-bundle": "^3.0"`
**After:** `"newscoop/recaptcha-bundle": "^3.0"` (unchanged)

#### Sentry SDK
**Before:** `"sentry/sentry": "^4.3"`
**After:** `"sentry/sentry": "^4.9"`
**Reason:** Latest version with performance improvements.

### API Documentation

#### Nelmio API Doc Bundle
**Before:** `"nelmio/api-doc-bundle": "^4.17"`
**After:** `"nelmio/api-doc-bundle": "^5.2"`
**Reason:** Major version upgrade for OpenAPI 3.1 support and PHP 8.3 compatibility.

### Other Dependencies

#### Pimple Container
**Before:** `"pimple/pimple": "^3.5"`
**After:** `"pimple/pimple": "^3.6"`
**Reason:** Minor update.

#### Stof Doctrine Extensions
**Before:** `"stof/doctrine-extensions-bundle": "^1.11"`
**After:** `"stof/doctrine-extensions-bundle": "^1.13"`
**Reason:** Latest version with PHP 8.3 fixes.

#### Smarty Template
**Before:** `"smarty/smarty": "^5.0"`
**After:** `"smarty/smarty": "^5.4"`
**Reason:** Latest Smarty 5.x with PHP 8.3 compatibility.

#### Hybridauth
**Before:** `"hybridauth/hybridauth": "^3.11"`
**After:** `"hybridauth/hybridauth": "^3.11"` (unchanged)

#### Buzz HTTP Client
**Before:** `"kriswallsmith/buzz": "^1.2"`
**After:** `"kriswallsmith/buzz": "^1.2"` (unchanged)

#### Willdurand JS Translation
**Before:** `"willdurand/js-translation-bundle": "^5.0"`
**After:** `"willdurand/js-translation-bundle": "^5.0"` (unchanged)

#### FOS OAuth Server Bundle
**Before:** `"friendsofsymfony/oauth-server-bundle": "^2.0"`
**After:** `"friendsofsymfony/oauth-server-bundle": "^2.0"` (unchanged)

#### Jobby Cron Manager
**Before:** `"hellogerard/jobby": "^2.1"`
**After:** `"hellogerard/jobby": "^2.1"` (unchanged)

#### Dflydev Doctrine ORM Service Provider
**Before:** `"dflydev/doctrine-orm-service-provider": "^2.0"`
**After:** `"dflydev/doctrine-orm-service-provider": "^2.0"` (unchanged)

#### Plugins Installer
**Before:** `"newscoop/plugins-installer": "^1.0"`
**After:** `"newscoop/plugins-installer": "^1.0"` (unchanged)

#### Incenteev Parameter Handler
**Before:** `"incenteev/composer-parameter-handler": "^2.2"`
**After:** `"incenteev/composer-parameter-handler": "^2.2"` (unchanged)

#### Symfony Assetic Bundle
**Before:** `"symfony/assetic-bundle": "^2.8"`
**After:** `"symfony/assetic-bundle": "^2.8"` (unchanged)
**Note:** Deprecated but still functional. Consider migrating to Webpack Encore.

#### Symfony Swiftmailer Bundle
**Before:** `"symfony/swiftmailer-bundle": "^3.5"`
**After:** `"symfony/swiftmailer-bundle": "^3.5"` (unchanged)
**Note:** Deprecated in favor of Symfony Mailer, but retained for backward compatibility.

### Zend Framework 1 (Legacy)
**Before:** `"bombayworks/zendframework1": "1.12.*"`
**After:** `"bombayworks/zendframework1": "1.12.*"` (unchanged)
**Warning:** Zend Framework 1 is EOL and only supports up to PHP 7.4. This may cause issues with PHP 8.3+.

### Development Dependencies

#### PHPUnit
**Before:** `"phpunit/phpunit": "^10.5"`
**After:** `"phpunit/phpunit": "^11.5"`
**Reason:** Latest version with PHP 8.3/8.4 support and new features.

#### Behat
**Before:** `"behat/behat": "^3.13"`
**After:** `"behat/behat": "^3.17"`
**Reason:** Latest version with improvements.

#### Behat Common Contexts
**Before:** `"behat/common-contexts": "^1.5"`
**After:** `"behat/common-contexts": "^1.6"`
**Reason:** Minor update.

#### Behat Mink
**Before:** `"behat/mink": "^1.11"`
**After:** `"behat/mink": "^1.12"`
**Reason:** Latest version.

#### Behat Mink Selenium2 Driver
**Before:** `"behat/mink-selenium2-driver": "^1.6"`
**After:** `"behat/mink-selenium2-driver": "^1.7"`
**Reason:** Latest version.

#### Faker (REPLACED)
**Before:** `"fzaninotto/faker": "^1.5"`
**After:** `"fakerphp/faker": "^1.24"`
**Reason:** Original faker is abandoned. Fork maintained by FakerPHP is the recommended replacement.

#### PHPSpec
**Before:** `"phpspec/phpspec": "^7.0"`
**After:** `"phpspec/phpspec": "^7.0"` (unchanged)

#### LIIP RMT
**Before:** `"liip/rmt": "^1.6"`
**After:** `"liip/rmt": "^1.6"` (unchanged)

#### Behat Mink Extension
**Before:** `"behat/mink-extension": "^2.3"`
**After:** `"behat/mink-extension": "^2.3"` (unchanged)

#### Behat Mink Goutte Driver
**Before:** `"behat/mink-goutte-driver": "^2.1"`
**After:** `"behat/mink-goutte-driver": "^2.1"` (unchanged)

#### Ladybug Bundle
**Before:** `"raulfraile/ladybug-bundle": "^1.1"`
**After:** `"raulfraile/ladybug-bundle": "^1.1"` (unchanged)
**Note:** This package may be abandoned. Consider alternative debugging tools.

## Removed Packages

1. **sensio/distribution-bundle** - Deprecated, use Symfony Flex
2. **sensio/generator-bundle** - Deprecated, use Symfony MakerBundle

## Added Packages

1. **twig/twig** - Explicit requirement for clarity
2. **fakerphp/faker** - Replacement for abandoned fzaninotto/faker

## Migration Notes

### Breaking Changes

1. **Symfony 6 → 7**
   - Some deprecated methods removed
   - Check for deprecation notices in logs
   - Review service configuration

2. **Doctrine ORM 2 → 3**
   - Some deprecated APIs removed
   - Type system improvements
   - Review custom repository methods

3. **KNP Paginator 5 → 6**
   - Configuration changes may be required
   - Review template overrides

4. **Nelmio API Doc 4 → 5**
   - OpenAPI 3.1 support
   - Configuration structure changes

### Testing Requirements

After running `composer update`:

1. Clear cache: `php bin/console cache:clear`
2. Run tests: `php vendor/bin/phpunit`
3. Check deprecations: `php bin/console about`
4. Test critical paths manually

### Rollback Plan

If issues occur:

```bash
# Revert composer.json changes
git checkout composer.json

# Install previous versions
composer install --lock

# Or install specific versions
composer require symfony/symfony:^6.4 doctrine/orm:^2.17
```

## Compatibility Matrix

| Package | PHP 8.0 | PHP 8.1 | PHP 8.2 | PHP 8.3 | PHP 8.4 |
|---------|---------|---------|---------|---------|---------|
| Symfony 7.2 | ✗ | ✓ | ✓ | ✓ | ✓ |
| Doctrine ORM 3.3 | ✗ | ✗ | ✓ | ✓ | ✓ |
| Twig 3.18 | ✓ | ✓ | ✓ | ✓ | ✓ |
| Smarty 5.4 | ✓ | ✓ | ✓ | ✓ | ✓ |
| PHPUnit 11.5 | ✗ | ✗ | ✓ | ✓ | ✓ |

## Post-Update Commands

```bash
# Update dependencies
composer update

# Dump optimized autoload files
composer dump-autoload --optimize

# Check for security vulnerabilities
composer audit

# List outdated packages
composer outdated

# Check Symfony requirements
php bin/console about
```

## References

- [Symfony 7 Upgrade Guide](https://symfony.com/doc/current/setup/upgrade_major.html)
- [Doctrine ORM 3.0 Upgrade Guide](https://www.doctrine-project.org/2023/12/17/orm3.html)
- [PHP 8.3 Migration Guide](https://www.php.net/manual/en/migration83.php)
- [PHP 8.4 Migration Guide](https://www.php.net/manual/en/migration84.php)
