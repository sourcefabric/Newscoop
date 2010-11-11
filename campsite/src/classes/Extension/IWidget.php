<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once dirname(__FILE__) . '/IWidgetContext.php';
 
/**
 * Widget interace
 */
interface IWidget
{
    /**
     * Render widget
     * @return void
     */
    public function render();
}
