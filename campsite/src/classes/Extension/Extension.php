<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once dirname(__FILE__) . '/File.php';

/**
 * Extension
 */
class Extension_Extension
{
    /** @var string */
    private $interface = '';

    /** @var string */
    private $class = '';

    /** @var Extension_File */
    private $file = NULL;

    /** @var object */
    private static $instance = NULL;

    /**
     * @param string $interface
     * @param string $class
     * @param Extension_File $file
     */
    public function __construct($interface, $class, Extension_File $file)
    {
        $this->interface = $interface;
        $this->class = $class;
        $this->file = $file;
    }

    /**
     * Get class name
     * @return string
     */
    public function getClass()
    {
        return (string) $this->class;
    }

    /**
     * Get file
     * @return Extension_File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get path
     * @return string
     */
    public function getPath()
    {
        return $this->getFile()->getPath();
    }

    /**
     * Get instance
     * @return object
     */
    public function getInstance()
    {
        if (self::$instance === NULL) {
            require_once $this->getFile()->getPath();
            $classname = $this->getClass();
            self::$instance = new $classname;
        }
        return self::$instance;
    }

    /**
     * Has class interface?
     * @param string $interface
     * @return bool
     */
    public function hasInterface($interface)
    {
        return $interface === $this->interface;
    }
}
