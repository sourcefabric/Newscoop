# PSR-4 Autoloading Migration Plan

## Overview

This document details the migration from legacy PSR-0/classmap autoloading to modern PSR-4 standard for the Newscoop codebase.

---

## Current State

### Existing Autoload Configuration

```json
{
    "autoload": {
        "psr-0": {
            "Newscoop": ["src", "library"],
            "Resource": "library",
            "Proxy": "library"
        },
        "classmap": [
            "classes",
            "template_engine",
            "plugins",
            "include",
            "application"
        ]
    },
    "include-path": ["include"]
}
```

### Problems with Current Setup

1. **PSR-0 is Legacy**: Deprecated in favor of PSR-4 since 2014
2. **Performance Issues**: Classmap scanning entire directories
3. **Mixed Strategies**: Confusing combination of PSR-0, classmap, and include-path
4. **No Clear Namespacing**: Many files lack proper namespace declarations
5. **Deprecated `include-path`**: Removed in Composer 2.x

---

## Target PSR-4 Configuration

### Proposed composer.json Autoload Section

```json
{
    "autoload": {
        "psr-4": {
            "Newscoop\\": "library/Newscoop/",
            "Newscoop\\Bundle\\": "src/Newscoop/",
            "Campsite\\": "classes/",
            "Resource\\": "library/Resource/",
            "Proxy\\": "library/Proxy/"
        },
        "classmap": [
            "include/Date.php",
            "include/File.php",
            "include/Mail.php",
            "include/System.php"
        ],
        "files": [
            "include/campsite_constants.php",
            "include/campsite_init.php"
        ]
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    }
}
```

### Namespace Mapping

| Namespace Prefix | Directory | Files Affected | Status |
|-----------------|-----------|----------------|--------|
| `Newscoop\` | `library/Newscoop/` | ~375 files | Partial |
| `Newscoop\Bundle\` | `src/Newscoop/` | ~178 files | ✓ Complete |
| `Campsite\` | `classes/` | ~105 files | ✗ Needs work |
| `Resource\` | `library/Resource/` | ~10 files | Verify |
| `Proxy\` | `library/Proxy/` | ~5 files | Verify |
| (none - classmap) | `include/*.php` | 4 utility files | Strategy |
| (none - files) | `include/constants.php` | 2 config files | Strategy |

---

## Migration Steps

### Step 1: Prepare Classes Directory (Campsite Namespace)

**Files:** All `classes/*.php` files (~84 files)

**Action Required:**
1. Add `namespace Campsite;` declaration after `<?php` opening tag
2. Replace `require_once` statements with `use` declarations
3. Update class references to use fully qualified names or `use` statements

**Example Transformation:**

**Before (`classes/Article.php`):**
```php
<?php
/**
 * @package Newscoop
 */

require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DbObjectArray.php');

class Article extends DatabaseObject
{
    var $m_keyColumnNames = array('Number', 'IdLanguage');
    
    function Article($articleId, $languageId)
    {
        // Constructor
    }
}
```

**After (`classes/Article.php`):**
```php
<?php
/**
 * @package Newscoop
 */

namespace Campsite;

use Campsite\DatabaseObject;
use Campsite\DbObjectArray;

class Article extends DatabaseObject
{
    protected array $m_keyColumnNames = ['Number', 'IdLanguage'];
    
    public function __construct(int $articleId, int $languageId)
    {
        // Constructor
    }
}
```

### Step 2: Prepare Include Utilities

**Strategy Decision:** Two approaches possible

#### Option A: Namespace Everything (Recommended)
```json
"autoload": {
    "psr-4": {
        "Campsite\\Utility\\": "include/"
    }
}
```

Add `namespace Campsite\Utility;` to all class files in `include/`.

#### Option B: Hybrid Approach (Safer)
Keep complex PEAR classes in classmap, add namespaces only to Newscoop-specific utilities:

```json
"autoload": {
    "psr-4": {
        "Campsite\\Utility\\": "include/campsite/"
    },
    "classmap": [
        "include/Date.php",
        "include/File.php",
        "include/Mail.php",
        "include/System.php",
        "include/PEAR.php",
        "include/PEAR5.php"
    ]
}
```

**Recommendation:** Use Option B for smoother migration.

### Step 3: Fix library/Newscoop Inconsistencies

**Audit Results:**
- ~20 files already have correct namespaces
- ~355 files need verification

**Action:**
1. Ensure every file has namespace matching its directory
2. Example: `library/Newscoop/Article/ArticleService.php` → `namespace Newscoop\Article;`
3. Add missing `use` statements for cross-namespace references

### Step 4: Verify Resource and Proxy Directories

Check if these directories still exist and are in use:
- `library/Resource/` 
- `library/Proxy/`

If they exist, ensure proper namespace declarations.

### Step 5: Update composer.json

Replace the entire autoload section with PSR-4 configuration.

### Step 6: Regenerate Autoloader

```bash
composer dump-autoload --optimize --classmap-authoritative
```

### Step 7: Testing

Run comprehensive tests:
```bash
vendor/bin/phpunit
```

Manual testing:
- Admin panel access
- Article CRUD operations
- Template rendering
- Plugin loading

---

## File-by-File Checklist

### classes/ Directory (Campsite Namespace)

- [ ] `DatabaseObject.php` - Base class, do first
- [ ] `DbObjectArray.php` - Array wrapper
- [ ] `Article.php` - Main entity
- [ ] `Author.php` - Author entity
- [ ] `Issue.php` - Issue entity
- [ ] `Publication.php` - Publication entity
- [ ] `Language.php` - Language entity
- [ ] `Section.php` - Section entity
- [ ] `Topic.php` - Topic entity
- [ ] `User.php` - User entity
- [ ] `Comment.php` - Comment entity
- [ ] `Image.php` - Image entity
- [ ] `File.php` - File entity
- [ ] `Template.php` - Template entity
- [ ] `Subscription.php` - Subscription entity
- [ ] `Attachment.php` - Attachment entity
- [ ] `ArticleType.php` - Article type entity
- [ ] `ArticleTypeField.php` - Article type field entity
- [ ] `GeoMap.php` - Geolocation service
- [ ] `GeoMapLocation.php` - Location entity
- [ ] `BugReporter.php` - Bug reporting
- [ ] `CampPlugin.php` - Plugin system
- [ ] `CampCache.php` - Caching
- [ ] `CampMail.php` - Mail wrapper
- [ ] `Country.php` - Country entity (already done)
- [ ] `Alias.php` - Alias entity (already done)
- [ ] `Event.php` - Event entity (already done)
- [ ] `DbReplication.php` - Replication (already done)
- [ ] `ArticleData.php` - Article data (already done)
- [ ] `ArticleIndex.php` - Article index (already done)
- [ ] `ArticleAuthor.php` - Article-author relation (already done)
- [ ] `ArticleImage.php` - Article image relation
- [ ] `ArticleTopic.php` - Article-topic relation
- [ ] `ArticlePublish.php` - Article publishing
- [ ] `ArticleAttachment.php` - Article attachment relation
- [ ] `AuthorAlias.php` - Author alias
- [ ] `AuthorAssignedType.php` - Author type assignment
- [ ] `AuthorBiography.php` - Author biography
- [ ] `AuthorType.php` - Author type
- [ ] `Browser.php` - Browser detection
- [ ] `CampCacheList.php` - Cache list
- [ ] `CampTemplateCache.php` - Template cache
- [ ] `ContextBox.php` - Context box
- [ ] `ContextBoxArticle.php` - Context box article
- [ ] `Exceptions.php` - Exception classes
- [ ] `FileTextSearch.php` - File search
- [ ] `GeoLocation.php` - Geo location

### include/ Directory (Classmap or Utility Namespace)

**Classmap Candidates:**
- [ ] `Date.php` - PEAR Date class (complex, keep in classmap)
- [ ] `File.php` - File utilities (keep in classmap)
- [ ] `Mail.php` - Mail wrapper (keep in classmap)
- [ ] `System.php` - System utilities (keep in classmap)
- [ ] `PEAR.php` - PEAR core (keep in classmap)
- [ ] `PEAR5.php` - PEAR PHP5 compat (keep in classmap)

**Files Autoload (procedural):**
- [x] `campsite_constants.php` - Constants (already configured)
- [x] `campsite_init.php` - Initialization (already configured)

**Subdirectories (evaluate individually):**
- [ ] `Archive/` - Archive utilities
- [ ] `Console/` - Console utilities
- [ ] `Event/` - Event handling
- [ ] `html2pdf/` - PDF generation
- [ ] `captcha/` - CAPTCHA system
- [ ] `crypto/` - Cryptography
- [ ] `data/` - Data utilities
- [ ] `smarty/` - Smarty integration

---

## Compatibility Notes

### Backward Compatibility

The migration maintains backward compatibility through:

1. **Class Aliases**: Can add class aliases for old class names if needed
2. **Autoloader Fallback**: Classmap can include legacy paths temporarily
3. **Gradual Migration**: Can migrate module by module

### Breaking Changes

Potential breaking changes to watch for:

1. **Direct File Includes**: Code using `require_once 'classes/Article.php'` will break
   - **Fix**: Replace with autoloader usage

2. **String Class References**: Code using `new $className` with old class names
   - **Fix**: Update to use fully qualified class names

3. **Serialization**: Serialized objects with old class names
   - **Fix**: May need migration script for database-stored serialized data

---

## Performance Benefits

### Expected Improvements

1. **Faster Autoloading**: PSR-4 is more efficient than PSR-0
2. **Optimized Classmap**: Only essential files in classmap
3. **APCu Integration**: Can use APCu for autoloader caching

### Benchmarking

Before and after comparison:
```bash
# Before
composer dump-autoload
time php -r "require 'vendor/autoload.php'; new \\Campsite\\Article(1, 1);"

# After
composer dump-autoload --optimize
time php -r "require 'vendor/autoload.php'; new \\Campsite\\Article(1, 1);"
```

---

## Rollback Plan

If issues occur during migration:

1. **Revert composer.json**: Keep backup of original configuration
2. **Clear Cache**: `composer clear-cache`
3. **Regenerate Autoloader**: `composer dump-autoload`
4. **Restore Files**: Git checkout for modified PHP files

---

## Success Criteria

Migration is complete when:

- [ ] All files have appropriate namespace declarations
- [ ] No `require_once` statements for class files
- [ ] `composer dump-autoload` completes without warnings
- [ ] All PHPUnit tests pass
- [ ] Manual testing confirms functionality
- [ ] Performance benchmarks show improvement

---

## Timeline Estimate

| Phase | Duration | Dependencies |
|-------|----------|--------------|
| Planning & Backup | 0.5 day | None |
| Classes Directory | 2-3 days | DatabaseObject first |
| Include Utilities | 1 day | Classes complete |
| Library Verification | 2 days | Parallel possible |
| Testing & Fixes | 2 days | All above complete |
| **Total** | **~1 week** | |

---

## Next Steps

1. Review and approve this migration plan
2. Create backups of all files
3. Begin with `DatabaseObject.php` refactoring
4. Proceed systematically through checklist
5. Test frequently and document issues
