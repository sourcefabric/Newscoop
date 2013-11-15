<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Process\Process;

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
        $allAvailablePlugins = $pluginService->getAllAvailablePlugins();

        return array(
            'allAvailablePlugins' => $allAvailablePlugins
        );
    }

    /**
     * @Route("/admin/plugins/getPackagesFromPackagist")
     */
    public function searchOnPackagistAction(Request $request)
    {
        $response = new JsonResponse();
        $query = $request->get('q', '');

        $client = new \Buzz\Client\Curl();
        $client->setTimeout(3600);
        $browser = new \Buzz\Browser($client);
        $packagistResponse =  $browser->get('https://packagist.org/search.json?type=newscoop-plugi&q='.$query);
        $packages = json_decode($packagistResponse->getContent(), true);
        $results = $packages['results'];
        $this->aasort($results, 'downloads');

        $cleanResults = array();
        foreach ($results as $package) {
            $cleanResults[] = $package;
        }
        $packages['results'] = $cleanResults;

       return $response->setData($packages);
    }


    /**
     * @Route("/admin/plugins/getStream/{action}/{name}", requirements={"name" = ".+"})
     */
    public function getStreamAction($action, $name)
    {
        $response = new Response();
        $response->headers->set('Transfer-Encoding', 'chunked');
        $response->sendHeaders();

        flush();
        ob_flush();
        $this->dump_chunk('<pre>');

        @apache_setenv('no-gzip', 1);
        @ini_set('implicit_flush', 1);

        $newscoopDir = __DIR__ . '/../../../../';
        putenv("COMPOSER_HOME=".$newscoopDir);
        $process = new Process('php '.$newscoopDir.'application/console plugins:'. $action .' '. $name);
        $process->setTimeout(3600);
        $CI = $this;
        $process->run(function ($type, $buffer) use($CI) {
            $CI->dump_chunk($buffer);
        });
        $this->dump_chunk('</pre>');
        die();
    }

    public function dump_chunk($chunk) {
       echo $chunk;
       echo "\r\n";
       flush();
       ob_flush();
    }

    private function aasort (&$array, $key) {
        $sorter=array();$ret=array();reset($array);

        foreach ($array as $ii => $va) {
            $sorter[$ii]=$va[$key];
        }

        arsort($sorter);

        foreach ($sorter as $ii => $va) {
            $ret[$ii]=$array[$ii];
        }

        $array=$ret;
    }
}
