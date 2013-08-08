<?php
/**
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\View;

/**
 * Zend View wrapper for setting in container.
 */
class ViewFactory
{
    /**
     * Get view from Zend Registry
     * @return object Zend_View
     */
    public function getView() 
    {
        if (\Zend_Registry::isRegistered('view') ){
            return \Zend_Registry::get('view');
        }
    }
}
