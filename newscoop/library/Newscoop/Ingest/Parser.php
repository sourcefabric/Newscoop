<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Ingest;

/**
 * Parser interface
 */
interface Parser
{
    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Get content
     *
     * @return string
     */
    public function getContent();

    /**
     * Get created
     *
     * @return DateTime
     */
    public function getCreated();

    /**
     * Get updated
     *
     * @return DateTime
     */
    public function getUpdated();
}
