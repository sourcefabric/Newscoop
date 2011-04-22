<?php

/**
 * Render resource view helper
 */
class Admin_View_Helper_RenderResource extends Zend_View_Helper_Abstract
{
    /**
     * Render resource
     *
     * @param string $resource
     * @param array $rules
     * @return void
     */
    public function renderResource($resource, array $rules)
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
