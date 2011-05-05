<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\File\Manager;

use Newscoop\File\LocalFile;

/**
 * Local file manager
 */
class LocalManager extends \DirectoryIterator implements Manager
{
    /** @var string */
    private $root;

    /**
     * @param string $path
     * @param string $root
     */
    public function __construct($path, $root)
    {
        parent::__construct($path);

        $this->root = realpath($root);
        if (!$this->root) {
            throw new \InvalidArgumentException($root);
        }
    }

    /**
     * Is root?
     *
     * @return bool
     */
    public function isRoot()
    {
        return $this->root == $this->getPath();
    }
}
