<?php

namespace Newscoop\ZendBridgeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BridgeController extends Controller
{
    /**
    * @Route("/admin/")
    * @Route("/admin/{rest}", requirements={"rest" = ".+"})
    * @Route("/")
    * @Route("/{rest}", requirements={"rest" = ".+"})
    */
    public function indexAction(Request $request)
    {
        // don't render page on tinymce files
        $specialfiles = array(
            'js/tinymce/plugins/campsiteimage/popup.php',
            'js/tinymce/plugins/campsiteimage/images.php',
            'js/tinymce/plugins/campsiteattachment/popup.php',
            'js/tinymce/plugins/campsiteattachment/attachments.php',
            'js/tinymce/plugins/campsiteinternallink/link.php',
            'js/tinymce/plugins/campsitemedia/popup.php',
            'js/tinymce/plugins/campsitemedia/attachments.php',
            'bin/events-notifier',
            'bin/newscoop-autopublish',
            'bin/newscoop-indexer',
            'bin/newscoop-statistics'
        );

        foreach ($specialfiles as $file) {
            if (strpos($request->server->get('SCRIPT_FILENAME'), $file) !== false) {
                return new Response();
            }
        }

        $logger = $this->container->get('monolog.logger.sentry');
        \Monolog\ErrorHandler::register($logger);

        $application = \Zend_Registry::get('zend_application');
        $bootstrap = $application->getBootstrap();

        $front = $bootstrap->getResource('FrontController');
        $front->setDefaultControllerName('legacy');
        $front->returnResponse(true);
        $default = $front->getDefaultModule();
        if (null === $front->getControllerDirectory($default)) {
            throw new \Zend_Application_Bootstrap_Exception(
                'No default controller directory registered with front controller'
            );
        }

        $front->setParam('bootstrap', $bootstrap);
        $front->setParam('locale', $request->getLocale());
        $front->setBaseUrl('/');
        $response = $front->dispatch();

        //copy headers
        $symfonyHeaders = array();
        foreach ($response->getHeaders() as $key => $header) {
            $symfonyHeaders[$header['name']] = $header['value'];
        }

        return new Response($response, $response->getHttpResponseCode(), $symfonyHeaders);
    }
}
