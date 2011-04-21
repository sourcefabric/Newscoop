<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

/**
 * Bootstrap controller plugin
 */
class Application_Plugin_Bootstrap extends Zend_Controller_Plugin_Abstract
{
    /** @var array */
    private $options = array();

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * Register plugins by settings
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $front = Zend_Controller_Front::getInstance();
        $module = $request->getModuleName();

        $plugins = isset($this->options[$module]['plugins'])
            ? $this->options[$module]['plugins'] : array();

        foreach ($plugins as $plugin => $options) {
            $reflection = new ReflectionClass('Application_Plugin_' . ucfirst($plugin));
            if ($reflection->hasMethod('__construct')) {
                $instance = $reflection->newInstanceArgs((array) $options);
            } else {
                $instance = $reflection->newInstance();
            }

            $front->registerPlugin($instance);
        }
    }
}
