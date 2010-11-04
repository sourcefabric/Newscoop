<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once dirname(__FILE__) . '/IWidget.php'; 
require_once dirname(__FILE__) . '/IWidgetContext.php';

/**
 * Widget renderer interface
 */
interface IWidgetRenderer extends IWidget
{
    /**
     * @param IWidget
     */
    public function __construct(IWidget $widget);

    /**
     * Render widget.
     * @param IWidgetContext
     * @return void
     */
    public function render(IWidgetContext $context = NULL);
}
