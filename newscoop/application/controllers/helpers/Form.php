<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Form action helper
 */
class Action_Helper_Form extends Zend_Controller_Action_Helper_Abstract
{
    const MASK_APP = 'Application_Form_%s';
    const MASK_THEME = 'Theme_Form_%s';

    /**
     */
    public function init()
    {
        $themesService = \Zend_Registry::get('container')->getService('newscoop_newscoop.themes_service');
        $this->basePath = $themesService->getThemePath();
    }

    /**
     * Get form by given name
     *
     * @param  string    $name
     * @return Zend_Form
     */
    public function direct($name)
    {
        $className = ucfirst($name);
        $themeForm = APPLICATION_PATH . '/../themes/' . $this->basePath . 'forms/' . $className . '.php';
        if (file_exists($themeForm)) {
            include_once($themeForm);
            $class = sprintf(self::MASK_THEME, $className);
        } else {
            $class = sprintf(self::MASK_APP, $className);
        }

        return new $class;
    }
}
