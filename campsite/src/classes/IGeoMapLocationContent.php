<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

/**
 * Geo Map Location Content interface
 */
interface IGeoMapLocationContent
{
    /**
     * Get name
     * @return string
     */
    public function getName();

    /**
     * Get content
     * @return string
     */
    public function getContent();

    /**
     * Get plain text
     * @return string
     */
    public function getText();
}
