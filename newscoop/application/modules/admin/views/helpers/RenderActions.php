<?php

/**
 * Render actions view helper
 */
class Admin_View_Helper_RenderActions extends Zend_View_Helper_Abstract
{
    /**
     * Render actions
     *
     * @param array|NULL $actions
     * @return void
     */
    public function renderActions(array $actions = NULL)
    {
        if (empty($actions)) {
            return;
        }

        $this->view->actions = $actions;
        echo $this->view->render('actions.phtml');
    }
}
