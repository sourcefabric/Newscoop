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
require_once WWW_DIR . '/admin-files/localizer/Localizer.php';
 
/**
 * Widget interace
 */
abstract class Widget implements IWidget
{
    const DEFAULT_VIEW = 'default';
    const FULLSCREEN_VIEW = 'fullscreen';

    /** @var CampCache */
    private $cache = NULL;

    /** @var string */
    private $view = self::DEFAULT_VIEW;

    /** @var array */
    private $settings = array();

    /**
     * Get cache object
     * @return CampCache
     */
    public function getCache()
    {
        if ($this->cache === NULL) {
            $this->cache = CampCache::singleton();
        }
        return $this->cache;
    }

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
    final public function getView()
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
     * Get setting
     * @param string $name
     * return mixed
     */
    public function getSetting($name)
    {
        return isset($this->settings[$name]) ? $this->settings[$name] : $this->$name;
    }

    /**
     * Set widget settings
     * @param array $settings
     * @return IWidget
     */
    public function setSettings(array $settings = array())
    {
        $this->settings = $settings;
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
