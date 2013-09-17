<?php

namespace Newscoop\NewscoopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class PluginsController extends Controller
{
    /**
     * @Route("/admin/plugins")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->container->get('em');

        $pluginService = $this->container->get('newscoop.plugins.service');

        $allPlugins = $pluginService->getAllAvailablePlugins();

        // search https://packagist.org/search.json?type=%22newscoop-plugi%22

        return array();
    }
}
