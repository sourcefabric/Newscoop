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
 * Widget interace
 */
interface Extension_IWidget
{
    /**
     * Get name.
     * @return string
     */
    public function getName();

    /**
     * Get widget title.
     * @return string|NULL
     */
    public function getTitle();

    /**
     * Render widget preview.
     * @return void
     */
    public function renderPreview();

    /**
     * Render widget content.
     * @return void
     */
    public function renderDefault();
}
