<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Theme\Loader;

/**
 * Theme Loader interface
 */
interface Loader
{
    /**
     * Get all themes
     *
     * @return Traversable
     */
    public function findAll();

    /**
     * Get theme
     *
     * @param string $id
     * @return Newscoop\Entity\Theme
     */
    public function find($id);
}
