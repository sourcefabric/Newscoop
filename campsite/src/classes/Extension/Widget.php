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
    private $settings = NULL;

    /** @var array */
    private $annotations = NULL;

    /** @var WidgetManagerDecorator */
    private $manager = NULL;

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
     * Get setting
     * @param string $name
     * return mixed
     */
    public function getSetting($name)
    {
        if ($this->settings === NULL) {
            $this->settings = array();
        }
        return isset($this->settings[$name]) ? $this->settings[$name] : NULL;
    }

    /**
     * Get annotation
     * @param string $name
     * return string
     */
    public function getAnnotation($name)
    {
        if ($this->annotations === NULL) {
            $this->annotations = array();
            $reflection = new ReflectionObject($this);
            $doc = $reflection->getDocComment();
            $matches = array();
            if (preg_match_all('/@([a-zA-Z]+)([^*]+)\*/', $doc, $matches)) {
                for ($i = 0, $size = sizeof($matches[1]); $i < $size; $i++) {
                    $this->annotations[$matches[1][$i]] = trim($matches[2][$i]);
                }
            }
        }
        return isset($this->annotations[$name]) ? $this->annotations[$name] : NULL;
    }

    /**
     * Set widget manager
     * @param WidgetManagerDecorator $manager
     * @return IWidget
     */
    public function setManager(WidgetManagerDecorator $manager)
    {
        $this->manager = $manager;
        return $this;
    }

    /**
     * Get property value
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments) {
        $matches = array();
        if (!preg_match('/^get([A-Z][a-zA-Z0-9_-]+)$/', $name, $matches)) {
            return NULL;
        }

        // extract name
        $property = $matches[1];
        $property[0] = strtolower($property[0]); // lowercase 1st

        // get value from db
        $value = $this->getSetting($property);

        // get value from property
        if (empty($value) && property_exists($this, $property)) {
            $value = $this->$property;
        }

        // get value from annotation
        if (empty($value)) {
            $value = $this->getAnnotation($property);
        }

        // get value from ini file
        if (empty($value)) {
            $value = $this->manager->getMeta($property);
        }

        // return value
        // TODO set type by property if exists
        return $value;
    }
}
