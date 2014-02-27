<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class ErrorController extends Zend_Controller_Action
{
    public function init(){}

    /**
     * Forward to legacy controller if controller/action not found
     */
    public function preDispatch()
    {
        $errors = $this->_getParam('error_handler');
        if (!$errors) {
            return;
        }

        $request = $this->getRequest();
        $adminControllerFile = __DIR__.'/../..'.str_replace('/admin', '/admin-files', $request->getPathInfo());
        if (file_exists($adminControllerFile)) {
            $this->_forward('index', 'legacy', 'admin', array());

            return;
        }

        foreach (\CampPlugin::GetEnabled() as $CampPlugin) {
            $adminControllerFile = dirname(APPLICATION_PATH).'/'.$CampPlugin->getBasePath().str_replace('/admin', '/admin-files', $request->getPathInfo());

            if (file_exists($adminControllerFile)) {
                $this->_forward('index', 'legacy', 'admin', array());

                return;
            }
        }
    }

    public function errorAction()
    {
        $translator = \Zend_Registry::get('container')->getService('translator');

        if (defined('APPLICATION_ENV') && APPLICATION_ENV == 'development') {
            $this->_helper->layout->disableLayout(); // allow debuging
        }

        $errors = $this->_getParam('error_handler');
        $request = $this->getRequest();

        if (!$errors) {
            $this->view->message = $translator->trans('You have reached the error page', array(), 'bug_reporting');
            return;
        }

        $notFound = array(
            Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER,
            Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION,
        );

        if (in_array($errors->type, $notFound)) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $templatesService = \Zend_Registry::get('container')->get('newscoop.templates.service');
            $templatesService->renderTemplate('404.tpl');

            return;
        }

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = $translator->trans('Page not found', array(), 'bug_reporting');
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = $translator->trans('Application error', array(), 'bug_reporting');
                break;
        }

        if (defined('APPLICATION_ENV') && APPLICATION_ENV == 'development') {
            // conditionally display exceptions
            if ($this->getInvokeArg('displayExceptions') == true) {
                $this->view->exception = $errors->exception;
            }
        
            $this->view->request = $errors->request;
            $this->view->errors = $errors;
        }
    }
}
