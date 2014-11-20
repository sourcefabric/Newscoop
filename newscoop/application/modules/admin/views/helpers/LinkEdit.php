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
class Admin_View_Helper_LinkEdit extends Zend_View_Helper_Abstract
{
    /**
     * Render actions
     *
     * @return void
     */
    public function linkEdit( $p_url, $p_params = null)
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $params = array_merge(array(
            'name'  => $translator->trans('Edit'),
            'title' => $translator->trans('Edit'),
            'class' => array('edit','confirm'),
            'attributes' => array()
        ),is_null($p_params)? array():$p_params);
        //concatenating the class array into a class string
        $params['class'] = implode(' ', $params['class']);
        $this->view->urlOptions = array_merge(array('action' => 'edit'),$p_url);
        foreach($params as $key => $value)
            $this->view->$key = $value;
        return $this->view->render('link-edit.phtml');
    }
}
