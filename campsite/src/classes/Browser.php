<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once dirname(__FILE__) . '/browser_detection.php';

/**
 * Browser class
 */
class Browser
{
    /** @var array */
    private static $browser = NULL;

    /**
     */
    public function __construct()
    {
        if (self::$browser !== NULL) {
            return;
        }

        self::$browser = browser_detection('full_assoc', '1');
        self::$browser[self::$browser['browser_name']] = (float) self::$browser['browser_math_number'];

        switch (self::$browser['browser_name']) {
            case 'gecko': // add firefox alias
                self::$browser['firefox'] = self::$browser['gecko'];
                break;
        }
    }

    /**
     * Getter
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        // try getName function
        $method = 'get' . ucfirst($name);
        if (is_callable(array($this, $method))) {
            return $this->$method();
        }

        // try browser info property
        if (isset(self::$browser[$name])) {
            return self::$browser[$name];
        }

        return False;
    }

    /**
     * Get browser name
     *
     * @return string
     */
    public function __toString()
    {
        return self::$browser['browser_name'];
    }

    /**
     * Get browser mobile flag
     *
     * @return bool
     */
    public function getMobile()
    {
        return !empty(self::$browser['mobile_test']);
    }
}
