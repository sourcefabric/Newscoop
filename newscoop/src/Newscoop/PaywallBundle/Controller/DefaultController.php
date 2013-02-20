<?php

namespace Newscoop\PaywallBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * Show succes page or redirect to one
     * 
     * @Route("/paywall/return/success")
     */
    public function statusSuccessAction()
    {
        return $this->render('NewscoopPaywallBundle:Default:success.html.smarty');
    }

    /**
     * Show error page or redirect to one
     * 
     * @Route("/paywall/return/error")
     */
    public function statusErrorAction()
    {
        return $this->render('NewscoopPaywallBundle:Default:success.html.smarty');
    }

    /**
     * Show cancel page or redirect to one
     * 
     * @Route("/paywall/return/cancel")
     */
    public function statusCancelAction()
    {
        return $this->render('NewscoopPaywallBundle:Default:success.html.smarty');
    }

    /**
     * Get callback response from paywall/payment provider and proccess it.
     * 
     * @Route("/paywall/return/callback")
     */
    public function callbackAction(Request $request)
    {
        $adapter = $this->container->getService('newscoop.paywall.adapter');
        $adapterResult = $adapter->setRequest($request);
        $adapterResult = $adapter->proccess();

        if (!($adapterResult instanceof Response)) {
            throw new \Exception("Returned value from adapter must be instance of Symfony\Component\HttpFoundation\Response", 1);
        }

        return $adapterResult;
    }
}
