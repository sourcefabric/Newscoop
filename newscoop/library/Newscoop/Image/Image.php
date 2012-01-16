<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

/**
 * Image Entity
 * @Entity
 * @Table(name="Images")
 */
class Image
{
    /**
     * @Id @Column(type="integer", name="Id") @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @Column(name="ImageFileName")
     * @var string
     */
    private $basename;

    /**
     * @param string $basename
     */
    public function __construct($basename)
    {
        $this->basename = (string) $basename;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return 'images/' . $this->basename;
    }
}
