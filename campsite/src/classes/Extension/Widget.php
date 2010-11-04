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
 
/**
 * Widget interace
 */
abstract class Widget implements IWidget
{
    /**
     * Widget context
     * @var IWidgetContext
     */
    private $context = NULL;

    /**
     * Set context
     * @param IWidgetContext $context
     * @return IWidget
     */
    public function setContext(IWidgetContext $context = NULL)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Get context
     * @return IWidgetContext
     */
    protected function getContext()
    {
        return $this->context;
    }

    /**
     * Get user
     * @return User
     */
    public function getUser()
    {
        global $g_user;
        return $g_user;
    }
}
