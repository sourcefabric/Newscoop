<?php

class Admin_View_Helper_RenderActions extends Zend_View_Helper_Abstract
{
    public function renderActions(array $actions)
    {
        $this->view->actions = $actions;
        echo $this->view->render('actions.phtml');
    }
}
