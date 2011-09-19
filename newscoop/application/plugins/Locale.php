<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Handle L10n
 */
class Application_Plugin_Locale extends Zend_Controller_Plugin_Abstract
{
    /**
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {

        switch ($request->getModuleName()) {
            case 'default':
                Zend_Form::setDefaultTranslator(Zend_Registry::get('Zend_Translate'));
                break;
        }
    }
}
