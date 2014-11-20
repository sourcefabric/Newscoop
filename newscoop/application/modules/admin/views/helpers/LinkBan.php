<?php
/**
 * @package Newscoop
 * @subpackage Subscriptions
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 *
 *
 */

/**
 * Anchor for deleteing view helper
 */
class Admin_View_Helper_LinkBan extends Zend_View_Helper_Abstract
{
    /**
     * Render actions
     *
     * @return void
     */
    public function linkBan( $p_url, $p_params = null)
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $params = array_merge(array(
            'name'  => $translator->trans('Ban', array(), 'home'),
            'title' => $translator->trans('Ban', array(), 'home'),
            'class' => array('ban','confirm'),
            'attributes' => array()
        ),is_null($p_params)? array():$p_params);
        //concatenating the class array into a class string
        $params['class'] = implode(' ', $params['class']);
        $this->view->urlOptions = array_merge(array('action' => 'toggle-ban', 'forum' => 2),$p_url);
        foreach($params as $key => $value)
            $this->view->$key = $value;
        return $this->view->render('link-ban.phtml');
    }
}
