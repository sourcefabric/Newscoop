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
    const DEFAULT_VIEW = 'default';

    /** @var string */
    private $view = self::DEFAULT_VIEW;

    /**
     * Set view
     * @param string $view
     * @return IWidget
     */
    final public function setView($view = self::DEFAULT_VIEW)
    {
        $this->view = (string) $view;
        return $this;
    }

    /**
     * Get view
     * @return string
     */
    final protected function getView()
    {
        return $this->view;
    }

    /**
     * Get user
     * @return User
     */
    final public function getUser()
    {
        global $g_user;
        return $g_user;
    }
}
