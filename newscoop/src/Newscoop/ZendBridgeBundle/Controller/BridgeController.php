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
        $application = \Zend_Registry::get('zend_application');
        $bootstrap = $application->getBootstrap();

        $front = $bootstrap->getResource('FrontController');
        $front->returnResponse(true);
        $default = $front->getDefaultModule();
        if (null === $front->getControllerDirectory($default)) {
            throw new \Zend_Application_Bootstrap_Exception(
                'No default controller directory registered with front controller'
            );
        }

        $front->setParam('bootstrap', $bootstrap);
        $response = $front->dispatch();

        //copy headers
        $symfonyHeaders = array();
        foreach ($response->getHeaders() as $key => $header) {
            $symfonyHeaders[$header['name']] = $header['value'];
        }

        return new Response($response, $response->getHttpResponseCode(), $symfonyHeaders);
    }
}