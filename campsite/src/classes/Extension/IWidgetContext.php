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
 * Widget context interface
 */
interface IWidgetContext
{
    const DEFAULT_NAME = 'preview';

    /**
     * @return string
     */
    public function getName();

    /**
     * @return bool
     */
    public function isDefault();

    /**
     * Render context
     * @return void
     */
    public function render();
}
