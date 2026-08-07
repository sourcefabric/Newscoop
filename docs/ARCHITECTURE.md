# Newscoop CMS Architecture Documentation

## Phase 1: Audit, Execution Map, and Module Breakdown

---

## 1. Execution Flow Map

### 1.1 Web Request Flow (Public Frontend)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                         WEB REQUEST EXECUTION FLOW                          │
└─────────────────────────────────────────────────────────────────────────────┘

1. ENTRY POINT: /newscoop/index.php
   ├── Parses URI to determine request type
   ├── Checks for statistics requests (_statistics path)
   └── Routes to public/index.php for normal requests

2. PUBLIC FRONT CONTROLLER: /newscoop/public/index.php
   ├── Checks vendor dependencies exist
   ├── Loads constants.php (APPLICATION_PATH, APPLICATION_ENV)
   ├── Checks installation/upgrade status
   ├── Redirects to /install/ if not configured
   ├── Loads Symfony bootstrap cache
   ├── Initializes AppKernel (Symfony Kernel)
   │   ├── Creates kernel based on environment (prod/dev)
   │   └── Enables Debug in dev mode
   ├── Creates Request object from globals
   ├── Kernel handles request (routing, controllers, response)
   └── Sends response and terminates

3. SYMFONY KERNEL PROCESSING (AppKernel.php)
   ├── registerBundles() - Loads all bundles
   │   ├── FrameworkBundle, SecurityBundle, TwigBundle
   │   ├── DoctrineBundle (ORM/DBAL)
   │   ├── FOSRestBundle (REST API)
   │   ├── Newscoop custom bundles
   │   └── Plugin bundles (if available_plugins.json exists)
   ├── initializeContainer()
   │   ├── Sets timezone from system_preferences_service
   │   └── Sets TMPDIR environment variable
   └── loadContainerConfiguration() - Loads config/config_<env>.yml

4. ROUTING LAYER
   ├── Symfony Routing component
   ├── Routes defined in application/configs/symfony/routing.yml
   ├── REST API routes (FOSRestBundle)
   └── Module-based routes (admin module)

5. CONTROLLER LAYER
   ├── Application Controllers (/application/controllers/)
   │   ├── AuthController - Authentication
   │   ├── DashboardController - Admin dashboard
   │   ├── UserController - User management
   │   ├── ImageController - Image handling
   │   └── ... (15+ controllers)
   └── Module Controllers (/application/modules/admin/controllers/)
       └── Zend Framework 1 style controllers

6. MODEL/BUSINESS LOGIC LAYER
   ├── Legacy Classes (/classes/*.php)
   │   ├── DatabaseObject (base class for all DB entities)
   │   ├── Article, Author, Issue, Section, Publication
   │   ├── GeoLocation, GeoMap (geolocation features)
   │   └── CampPlugin, CampCache (system utilities)
   ├── Doctrine Entities (via NewscoopBundle)
   │   └── Modern ORM entities for new features
   └── Service Layer (/library/Newscoop/*/)
       ├── PackageManager, StorageService
       └── User/UserManager, SearchService

7. DATABASE ABSTRACTION
   ├── Doctrine DBAL/ORM (primary)
   │   ├── Custom types: PointType, UTCDateTimeType
   │   └── Custom DQL functions (MysqlRandom, MysqlField, etc.)
   ├── AdoDb Adapter (legacy compatibility)
   │   └── /library/Newscoop/Doctrine/AdoDbAdapter.php
   └── Direct SQL (in legacy classes via DatabaseObject)

8. TEMPLATE RENDERING
   ├── Twig Engine (Symfony/TwigBundle)
   │   ├── Templates in application/views/
   │   └── Module templates in application/modules/*/views/
   └── Smarty Engine (legacy)
       ├── /include/smarty/
       ├── /template_engine/classes/
       └── TemplateConverter for migration
```

### 1.2 Admin Panel Request Flow

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                       ADMIN PANEL EXECUTION FLOW                            │
└─────────────────────────────────────────────────────────────────────────────┘

1. ENTRY POINT: /newscoop/admin.php
   └── Bootstrap admin-specific configuration

2. LEGACY ADMIN FILES: /newscoop/admin-files/
   ├── index.php - Admin dashboard entry
   ├── menu.php - Navigation menu
   ├── home.php - Home page
   ├── articles/ - Article management
   ├── issues/ - Issue publishing workflow
   ├── sections/ - Section management
   ├── users/ - User administration
   ├── languages/ - Language configuration
   ├── plugins/ - Plugin management
   └── pub/ - Publication settings

3. ZEND FRAMEWORK 1 MODULE
   └── /application/modules/admin/
       ├── Bootstrap.php - Module initialization
       ├── controllers/ - Zend controllers
       ├── forms/ - Zend form definitions
       └── views/ - View scripts
```

### 1.3 CLI/Cron Execution Flow

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        CLI/CRON EXECUTION FLOW                              │
└─────────────────────────────────────────────────────────────────────────────┘

1. CONSOLE COMMANDS: /newscoop/application/console
   ├── Symfony Console component
   ├── newscoop:install - Installation command
   ├── scheduler:run - Cron job scheduler
   └── Custom Newscoop commands

2. BIN SCRIPTS: /newscoop/bin/
   ├── cli_script_lib.php - Shared CLI functions
   ├── newscoop-autopublish - Auto-publish articles
   ├── newscoop-backup - Database backup utility
   ├── newscoop-restore - Restore from backup
   ├── newscoop-indexer - Search indexer
   ├── newscoop-statistics - Stats collection
   ├── newscoop-utf8-converter - UTF-8 conversion
   ├── events-notifier - Event notifications
   └── subscription-notifier - Subscription alerts

3. CRON JOBS
   └── Scheduler runs every minute:
       php /var/www/newscoop/application/console scheduler:run
```

### 1.4 Installation Flow

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                         INSTALLATION EXECUTION FLOW                         │
└─────────────────────────────────────────────────────────────────────────────┘

1. DETECTION: public/index.php checks for conf/database_conf.php
   └── If missing → redirect to /install/

2. INSTALLER: /newscoop/install/index.php
   ├── System requirements check (SymfonyRequirements.php)
   ├── Database configuration
   ├── Database schema import (campsite_core.sql)
   ├── Sample data import (optional: campsite_demo_data.sql)
   ├── Geonames data import (CityLocations.csv, CityNames.csv)
   └── Configuration file generation

3. SQL SCHEMA FILES: /newscoop/install/Resources/sql/
   ├── campsite_core.sql - Main database schema
   ├── campsite_demo_data.sql - Demo content
   ├── geonames.sql - Geolocation data structure
   └── upgrade/ - Version upgrade scripts
       ├── 4.2.x/
       ├── 4.3.x/
       └── 4.4.x/
```

---

## 2. Module Responsibility Breakdown

### 2.1 Core Directory Structure

| Directory | Purpose | Functionality Type | Key Files |
|-----------|---------|-------------------|-----------|
| `/newscoop/` | Root directory | Core | index.php, admin.php, composer.json |
| `/newscoop/public/` | Web root (public files) | Public | index.php, css/, js/, files/, videos/ |
| `/newscoop/application/` | Symfony application layer | Core | AppKernel.php, Bootstrap.php, configs/ |
| `/newscoop/classes/` | Legacy business logic classes | Core/Admin | Article.php, Author.php, Issue.php |
| `/newscoop/library/` | Modern service layer | Core | Newscoop/, Resource/, Proxy/ |
| `/newscoop/admin-files/` | Legacy admin interface | Admin | articles/, issues/, users/, sections/ |
| `/newscoop/include/` | Utility libraries | Core | smarty/, Date.php, Mail.php, PEAR/ |
| `/newscoop/template_engine/` | Smarty template system | Core | classes/, metaclasses/ |
| `/newscoop/install/` | Installation system | Core | index.php, Resources/sql/ |
| `/newscoop/bin/` | CLI scripts | Core/Admin | newscoop-*, cli_script_lib.php |
| `/newscoop/themes/` | Theme templates | Public | system_templates/ |
| `/newscoop/plugins/` | Plugin system | Core | cache/, private_plugins/ |
| `/newscoop/extensions/` | Extension modules | Core | article-lists/, media-archive/ |

### 2.2 Major Modules Detail

#### **Application Module (Symfony)**
- **Path:** `/newscoop/application/`
- **Purpose:** Symfony2 framework integration, modern MVC layer
- **Key Files:**
  - `AppKernel.php` - Symfony kernel, bundle registration
  - `Bootstrap.php.cache` - Autoloader bootstrap
  - `configs/symfony/` - Symfony configuration (config.yml, security.yml, routing.yml)
  - `controllers/` - Symfony controllers (AuthController, UserController, etc.)
  - `modules/admin/` - Zend Framework 1 admin module
- **Dependencies:** Symfony 2.7, Doctrine ORM 2.4, Twig, FOSRestBundle
- **Functionality:** Core, Admin

#### **Legacy Classes Module**
- **Path:** `/newscoop/classes/`
- **Purpose:** Original Newscoop/Campsite business logic, database entities
- **Key Classes:**
  - `DatabaseObject.php` - Base class for all database entities (32KB)
  - `Article.php` - Article management (132KB - largest class)
  - `GeoMap.php` - Geolocation mapping (113KB)
  - `GeoMapLocation.php` - Location handling (56KB)
  - `ArticleTypeField.php` - Custom article type fields (34KB)
  - `Author.php`, `Issue.php`, `Section.php`, `Publication.php`
  - `CampPlugin.php` - Plugin system
  - `CampCache.php` - Caching abstraction
- **Database Tables:** All tables prefixed with application context (Aliases, Articles, Authors, Issues, Sections, Publications, Languages, Subscriptions, etc.)
- **Functionality:** Core, Admin, Public

#### **Library Module (Modern Services)**
- **Path:** `/newscoop/library/Newscoop/`
- **Purpose:** Modern service layer, dependency injection, new architecture
- **Sub-modules:**
  - `Controller/` - Action helpers, plugins, datatable components
  - `Package/` - Content packaging, article packages
  - `Storage/` - File storage service
  - `User/` - User management, search, criteria
  - `Doctrine/` - AdoDb adapter bridge
  - `Entity/` - Doctrine entities
  - `GimmeBundle/` - REST API bundle
  - `NewscoopBundle/` - Main Newscoop Symfony bundle
- **Functionality:** Core

#### **Admin Files Module (Legacy UI)**
- **Path:** `/newscoop/admin-files/`
- **Purpose:** Legacy administrative interface (pre-Symfony)
- **Key Directories:**
  - `articles/` - Article CRUD, workflow management
  - `issues/` - Issue creation, publishing workflow
  - `sections/` - Section organization
  - `users/` - User administration, permissions
  - `languages/` - Language configuration
  - `plugins/` - Plugin management UI
  - `pub/` - Publication settings
  - `media-archive/` - Media library
  - `universal-list/` - Generic list component
- **Supporting Files:**
  - `lib_campsite.php` - Legacy function library
  - `menu.php` - Navigation rendering
- **Functionality:** Admin

#### **Template Engine Module**
- **Path:** `/newscoop/template_engine/`
- **Purpose:** Smarty-based templating system for public themes
- **Components:**
  - `classes/` - Template processing classes
  - `metaclasses/` - Meta template handlers
- **Integration:** Works with `/include/smarty/campsite_plugins/`
- **Migration:** `TemplateConverter` classes for Twig migration
- **Functionality:** Public

#### **Include Utilities Module**
- **Path:** `/newscoop/include/`
- **Purpose:** Shared utility libraries
- **Components:**
  - `smarty/` - Smarty engine with Campsite plugins
  - `Date.php` - Date handling utilities
  - `Mail.php` - Email sending
  - `File.php` - File operations
  - `System.php` - System utilities
  - `Archive/` - Archive handling (Tar, Zip)
  - `Console/` - Console output formatting
  - `Event/` - Event dispatching
  - `PEAR/` - PEAR compatibility layer
- **Functionality:** Core

#### **Installation Module**
- **Path:** `/newscoop/install/`
- **Purpose:** Fresh installation and upgrades
- **Resources:**
  - `Resources/sql/campsite_core.sql` - Main schema (157KB)
  - `Resources/sql/campsite_demo_data.sql` - Demo data (545KB)
  - `Resources/sql/CityLocations.csv` - Geo data (7.7MB)
  - `Resources/sql/CityNames.csv` - Geo names (8.8MB)
  - `Resources/upgrade/` - Version upgrade scripts
- **Functionality:** Core

#### **Bin Scripts Module**
- **Path:** `/newscoop/bin/`
- **Purpose:** Command-line utilities and cron jobs
- **Scripts:**
  - `cli_script_lib.php` (43KB) - Shared CLI functions
  - `newscoop-autopublish` - Scheduled publishing
  - `newscoop-backup` - Backup utility
  - `newscoop-restore` - Restore utility
  - `newscoop-indexer` - Search indexing
  - `newscoop-statistics` - Analytics collection
  - `subscription-notifier` - Subscription emails
  - `events-notifier` - Event notifications
- **Functionality:** Core, Admin

#### **Plugins & Extensions Module**
- **Path:** `/newscoop/plugins/`, `/newscoop/extensions/`
- **Purpose:** Extensibility system
- **Extensions:**
  - `article-lists/` - Custom article listings
  - `feed-reader/` - RSS/Atom feed parsing
  - `media-archive/` - Media asset management
  - `wikipedia/` - Wikipedia integration
  - `sourcefabric/` - Sourcefabric services
- **Functionality:** Core

---

## 3. Current State Assessment

### 3.1 PHP Version Compatibility

| Component | Current Requirement | Target Version | Issues |
|-----------|-------------------|----------------|--------|
| composer.json | `php: >=5.3.3` | PHP 8.3/8.4 | MAJOR - needs update |
| Symfony | 2.7.*@dev | Incompatible | Needs upgrade to 4.4+ or 5.x |
| Doctrine ORM | 2.4.6 | Compatible with caveats | Update to 2.10+ recommended |
| Twig Extensions | 1.1.* | Compatible | Update to 2.x/3.x |
| Smarty | 3.1.21 | Compatible | Update to 4.x for PHP 8 |
| Zend Framework 1 | 1.11.* | INCOMPATIBLE | Critical - ZF1 is EOL |
| PHPUnit | ~4.0 | Incompatible | Update to 9.x for PHP 8 |

### 3.2 Deprecated Patterns Identified

#### **PHP 5.x Patterns Requiring Updates:**

1. **Old-style Constructors (PHP 8 incompatible)**
   - Location: `/newscoop/classes/DatabaseObject.php:81`
   - Pattern: `public function DatabaseObject($p_columnNames = null)`
   - Issue: PHP 8 removes support for same-name constructors
   - Fix: Rename to `__construct()`

2. **Var Keyword for Properties (PHP 8 deprecated)**
   - Locations: Multiple files in `/classes/`
   - Pattern: `var $m_dbTableName = '';`
   - Examples found:
     - `Attachment.php:19-22`
     - `AuthorAlias.php:18-21`
     - `ArticleIndex.php:17-25`
     - `ContextBox.php:19-22`
   - Fix: Replace with `public`, `protected`, or `private`

3. **Curly Brace Array Access (PHP 8 deprecated)**
   - Location: `/newscoop/application/AppKernel.php:112`
   - Pattern: `$path{0}` instead of `$path[0]`
   - Fix: Use square bracket syntax

4. **Required Parameters After Optional (PHP 8 error)**
   - Need to audit all constructor signatures
   - Common pattern in legacy classes

5. **get_class() without parameter (PHP 8 deprecated)**
   - Pattern: `get_class()` inside class methods
   - Fix: Use `self::class` or `static::class` or `get_class($this)`

6. **strpos() with non-string needles (PHP 8 warning)**
   - Need to audit all strpos/stripos calls

#### **Removed Functions in PHP 8:**

1. **Array/String Functions**
   - `each()` - Removed in PHP 8.0
   - `create_function()` - Removed in PHP 7.2, ensure not used

2. **MySQL Extension**
   - `mysql_*` functions - Already removed in PHP 7
   - Status: ✓ Not found in codebase (uses AdoDb/PDO)

#### **Symfony 2.7 Specific Issues:**

1. **Security Component Changes**
   - UserProviderInterface signature changes
   - Token authentication changes

2. **Form Component**
   - FormType inheritance changes
   - Options resolver signature changes

3. **Twig Integration**
   - Twig 1.x → 3.x migration required
   - Template loader changes

### 3.3 Legacy Dependencies

| Package | Version | Status | Action Required |
|---------|---------|--------|-----------------|
| symfony/symfony | 2.7.*@dev | EOL, insecure | Upgrade to 4.4 LTS or 5.4+ |
| bombayworks/zendframework1 | 1.11.* | EOL, PHP 7.4 max | Replace or fork for PHP 8 |
| sensio/distribution-bundle | ~3.0 | Deprecated | Remove, use Symfony recipes |
| friendsofsymfony/rest-bundle | 0.13.*@dev | Old version | Upgrade to 3.x |
| jms/serializer-bundle | 0.13.0 | Old version | Upgrade to 4.x |
| knplabs/knp-paginator-bundle | 2.4.0 | Compatible | Update to 5.x |
| hybridauth/hybridauth | v2.4.1 | Old version | Update to 3.x |
| recaptcha/php5 | v1.0.0 | Abandoned | Replace with google/recaptcha |
| raven/raven | 0.9.0 | Deprecated | Replace with sentry/sdk |
| phpunit/phpunit | ~4.0 | PHP 5 only | Upgrade to 9.x |

### 3.4 Database Schema Assessment

- **Current Schema Version:** 4.4.6 (based on composer.json)
- **Database Engine:** MySQL MyISAM (legacy), should migrate to InnoDB
- **Character Set:** UTF-8
- **Tables:** 50+ tables identified in campsite_core.sql
- **Upgrade Path:** Scripts available for 4.2.x → 4.3.x → 4.4.x
- **Schema Changes Required:** None for PHP 8 compatibility

### 3.5 File Permission & Security Issues

1. **Writable Directories (need proper ownership):**
   - `/cache/` - Template and data cache
   - `/log/` - Application logs
   - `/public/files/` - Uploaded files
   - `/public/videos/` - Video uploads
   - `/public/pdf/` - PDF exports
   - `/images/thumbnails/` - Image thumbnails
   - `/backup/` - Database backups

2. **Configuration Files:**
   - `/conf/configuration.php` - Main config (generated)
   - `/conf/database_conf.php` - Database credentials (generated)
   - `/conf/installation.php` - Installation marker (removed after install)

### 3.6 Summary of Required Actions for PHP 8.3/8.4

**Critical (Must Fix):**
1. Update composer.json PHP requirement to `^8.3`
2. Replace old-style constructors with `__construct()`
3. Replace `var` with visibility modifiers
4. Fix curly brace array access
5. Upgrade or replace Zend Framework 1
6. Upgrade Symfony to 4.4 LTS minimum (preferably 5.4+)
7. Update all Composer dependencies for PHP 8 compatibility

**High Priority:**
1. Add strict typing where safe
2. Fix nullable type hints
3. Update PHPUnit for testing
4. Migrate from MyISAM to InnoDB tables

**Medium Priority:**
1. Deprecation cleanup
2. Code style modernization
3. Documentation updates

---

## Appendix A: Database Table Overview

Core tables identified in `campsite_core.sql`:

| Table Name | Purpose | Key Columns |
|------------|---------|-------------|
| Aliases | URL aliases | Id, Name, IdPublication |
| ArticleAttachments | Article-file relationships | fk_article_number, fk_attachment_id |
| ArticleAuthors | Article-author assignments | fk_article_number, fk_author_id, fk_type_id |
| ArticleImageCaptions | Image captions | id, IdLanguage, IdImage, caption |
| ArticleImages | Article images | Id, NrArticle, IdLanguage |
| ArticleIndex | Full-text search index | Id, NrArticle, languageId, content |
| ArticleTopics | Article-topic associations | fk_article_number, fk_topic_id |
| ArticleTypes | Content type definitions | name, id |
| ArticleTypeFields | Custom field definitions | id, article_type_name, field_name |
| Attachments | File attachments | id, filename, mime_type |
| Authors | Author profiles | id, name, email |
| AuthorAliases | Author alternate names | id, fk_author_id, alias |
| AuthorBiographies | Author bios per language | fk_author_id, fk_language_id |
| ContextBoxes | Contextual content boxes | id, fk_article_no |
| Countries | Country reference data | id, name, code |
| GeoLocations | Geographic locations | id, latitude, longitude |
| GeoMaps | Map configurations | id, name, zoom_level |
| Images | Image library | id, filename, dimensions |
| Issues | Publication issues | id, id_publication, date_published |
| Languages | Supported languages | id, name, abbreviation |
| Publications | Publication definitions | id, name, url |
| Sections | Content sections | id, id_publication, name |
| Subscriptions | User subscriptions | id, email, type |
| Templates | Template definitions | id, name, type |
| Topics | Topic/tag taxonomy | id, name, parent_id |
| Users | User accounts | id, username, password, type |

---

*Document generated for Newscoop CMS Modernization Project - Phase 1*
*Version: 1.0*
*Date: 2024*
