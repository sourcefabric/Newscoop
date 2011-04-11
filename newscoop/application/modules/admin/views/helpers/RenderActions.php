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
        if ($actions === NULL) {
            $actions = $this->view->actions;
        }

        if (empty($actions)) {
            return;
        }

        $navigation = new Zend_Navigation($actions);
        $this->view->navigation()->menu()->setContainer($navigation);
        echo $this->view->render('actions.phtml');
    }
}
