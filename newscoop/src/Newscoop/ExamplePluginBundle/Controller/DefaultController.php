<?php

namespace Newscoop\ExamplePluginBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/testnewscoop")
     */
    public function indexAction(Request $request)
    {
        return $this->render('NewscoopExamplePluginBundle:Default:index2.html.smarty');
    }
}
