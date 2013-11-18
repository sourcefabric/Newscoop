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

        // search https://packagist.org/search.json?type=%22newscoop-plugi%22

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

        $browser = new \Buzz\Browser(new \Buzz\Client\Curl());
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
     * @Route("/admin/plugins/getStream")
     */
    public function streamedAction()
    {
        $response = new StreamedResponse();
        $response->setCallback(function () {
            $process = new Process('cd /var/www/newscoop/newscoop/ && php application/console plugins:update newscoop/articles-calendar-plugin');
            $process->start();
            $process->wait(function ($type, $buffer) {
                if (Process::ERR === $type) {
                    echo 'ERR > '.$buffer;
                } else {
                    echo 'OUT > '.$buffer;
                }
            });
            ob_flush();
            flush();
        });

        return $response;
    }

    /**
     * @Route("/admin/plugins/getStreamTest")
     */
    public function someFunctionInsideController()
    {
        $response = new Response();
        $response->headers->set('Content-Encoding', 'chunked');
        $response->headers->set('Transfer-Encoding', 'chunked');
        $response->headers->set('Content-Type', 'multipart/x-mixed-replace;');
        $response->headers->set('Connection', 'keep-alive');
        $response->sendHeaders();
        flush();
        ob_flush();
        putenv("COMPOSER_HOME=/var/www/newscoop/newscoop/");
        $process = new Process('php /var/www/newscoop/newscoop/application/console plugins:update newscoop/articles-calendar-plugin');
        $process->setTimeout(3600);
        $process->run(function ($type, $buffer) {
            $this->dump_chunk($buffer);
        });
        return new Response();
    }

    private function dump_chunk($chunk) {
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
