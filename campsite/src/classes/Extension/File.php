<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once dirname(__FILE__) . '/Extension.php';

/**
 * Index item class
 */
class Extension_File
{
    /** @var string */
    private $path;

    /** @var string */
    private $checksum = '';

    /** @var array */
    private $extensions = NULL;

    /**
     * @param string array $path
     */
    public function __construct($path)
    {
        $real = realpath($path);
        if ($real === FALSE || !is_file($real)) {
            throw new InvalidArgumentException("File '$path' not found.");
        }

        $this->path = $real;
    }

    /**
     * @return string
     */
    public function getChecksum()
    {
        if (empty($this->checksum)) {
            $this->checksum = sha1_file($this->getPath());
        }
        return (string) $this->checksum;
    }

    /**
     * Get extensions provided by file
     * @param string $interface
     * @return array of Extension_Extension
     */
    public function getExtensions()
    {
        if ($this->extensions === NULL) {
            $this->extensions = $this->parse();
        }
        return $this->extensions;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return (string) $this->path;
    }

    /**
     * Find extensions implementing given interface within file
     * @param string $interface
     * @return array
     */
    public function find($interface)
    {
        $extensions = array();
        foreach ($this->getExtensions() as $extension) {
            if ($extension->hasInterface($interface)) {
                $extensions[] = $extension;
            }
        }
        return $extensions;
    }
    
    /**
     * Parse file for extensions
     * @return array
     */
    private function parse()
    {
        $this->extensions = array();
        $s = file_get_contents($this->getPath());
        $tokens = token_get_all($s);
        $tokens_size = sizeof($tokens);
        for ($i = 0; $i < $tokens_size; $i++) {
            if ($tokens[$i][0] != T_CLASS) {
                continue;
            }

            require_once $this->getPath();
            $class = $tokens[$i + 2][1];
            $reflector = new ReflectionClass($class);
            if (!$reflector->isInstantiable()) {
                continue;
            }

            foreach ($reflector->getInterfaceNames() as $interface) {
                $this->extensions[] = new Extension_Extension($interface,
                    $class, $this);
            }
        }

        return $this->extensions;
    }
}
