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

camp_load_translation_strings('Widget');
 
/**
 * Widget interace
 */
abstract class Widget implements IWidget
{
    const DEFAULT_VIEW = 'default';
    const FULLSCREEN_VIEW = 'fullscreen';

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

    /**
     * Is view fullscreen?
     * @return bool
     */
    final public function isFullscreen()
    {
        return $this->getView() == self::FULLSCREEN_VIEW;
    }

    /**
     * Allow widgets to be translatable
     * @param string $translateString
     * @return string
     */
    final public function _($translateString)
    {
        $args = func_get_args();
        return call_user_func_array('getGS', $args);
    }
}
