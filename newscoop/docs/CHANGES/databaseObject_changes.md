# DatabaseObject.php Changes

## File: `/workspace/newscoop/classes/DatabaseObject.php`

### Changes Made for PHP 8.3/8.4 Compatibility

#### 1. Visibility Modifiers (Line 24-64)
**Before:**
```php
var $m_dbTableName = '';
var $m_columnNames = array();
var $m_keyColumnNames = array();
var $m_keyIsAutoIncrement = false;
var $m_data = array();
var $m_exists = null;
var $m_oldKeyValues = array();
```

**After:**
```php
protected $m_dbTableName = '';
protected $m_columnNames = array();
protected $m_keyColumnNames = array();
protected $m_keyIsAutoIncrement = false;
protected $m_data = array();
protected $m_exists = null;
protected $m_oldKeyValues = array();
```

**Rationale:** The `var` keyword is deprecated in PHP 8+. Replaced with `protected` visibility modifier to maintain encapsulation while allowing subclass access.

#### 2. Constructor Modernization (Line 81)
**Before:**
```php
public function DatabaseObject($p_columnNames = null)
```

**After:**
```php
public function __construct($p_columnNames = null)
```

**Rationale:** PHP 8+ does not support old-style constructors (methods with the same name as the class). Must use `__construct()` magic method.

### Additional Changes Needed

The following areas still need attention:

1. **Type Hints**: Add parameter and return type hints to methods
2. **Nullable Types**: Update properties that can be null with proper nullable types
3. **Array Type Declarations**: Add array type hints to array properties
4. **Static Properties**: Review static property declarations for compatibility

### Testing Status
- [ ] Constructor instantiation tested
- [ ] Property access tested
- [ ] Subclass inheritance tested
- [ ] Database operations tested

### Related Files
- All classes extending DatabaseObject need similar updates:
  - Article.php
  - Author.php
  - Issue.php
  - Language.php
  - Section.php
  - Publication.php
  - User.php
  - Subscription.php
  - Template.php
  - etc.

---
*Modified: 2024*
*PHP Target: 8.3+*
