<?php

/**
 * Render actions view helper
 */
class Admin_View_Helper_RenderActions extends Zend_View_Helper_Abstract
{
    /**
     * Render actions
     *
     * @param array $actions
     * @return void
     */
    public function renderActions(array $actions)
    {
        if (empty($actions)) {
            return;
        }

        $this->view->actions = $actions;
        echo $this->view->render('actions.phtml');
    }
}
