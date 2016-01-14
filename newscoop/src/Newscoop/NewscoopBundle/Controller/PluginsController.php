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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Process\Process;
use Newscoop\NewscoopBundle\Form\Type\PrivatePluginUploadType;
use Symfony\Component\Filesystem\Filesystem;

class PluginsController extends Controller
{

    /**
     * Private plugins directory, this directory is configured in composer.json
     * under: "repositories"
     */
    const PRIVATE_PLUGINS_DIR = '/private_plugins';

    const PRIVATE_PLUGINS_CACHE_DIR = '/cache';

    /**
     * @Route("/admin/plugins")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $user = $this->container->get('user')->getCurrentUser();
        $translator = $this->container->get('translator');
        if (!$user->hasPermission('plugin_manager')) {
            throw new AccessDeniedException($translator->trans("You do not have the right to manage plugins.", array(), 'plugins'));
        }

        $pluginService = $this->container->get('newscoop.plugins.service');
        $allAvailablePlugins = array();

        // show only modern plugins
        foreach ($pluginService->getAllAvailablePlugins() as $key => $value) {
            if (strpos($value->getName(), '/') !== false) {
                $allAvailablePlugins[] = $value;
            }
        }

        $form = $this->container->get('form.factory')
            ->create(new PrivatePluginUploadType(), array());

        $this->createFolderStructure($pluginService);

        // handle private plugin upload
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $file = $form['package']->getData();

                $extension = $file->guessExtension();
                if ($extension != 'zip') {
                    $request->getSession()->getFlashBag()->add('error', $translator->trans('newscoop.plugins_manager.form.package_must_be_zip', array(), 'plugins_manager'));

                    return $this->redirect($this->generateUrl('newscoop_newscoop_plugins_index'));
                }
                $file->move($pluginService->getPluginsDir().self::PRIVATE_PLUGINS_DIR, $file->getClientOriginalName());

                return $this->redirect($this->generateUrl('newscoop_newscoop_plugins_index'));
            }
        }

        // check if private plugins is writable
        $privatePluginsPathWritable = is_writable($pluginService->getPluginsDir().self::PRIVATE_PLUGINS_DIR);

        // search for private plugins
        $privatePackages = $this->searchForPrivatePlugins($pluginService);

        foreach ($privatePackages as $resultKey => $package) {
            $package['installed'] = false;
            foreach ($pluginService->getAllAvailablePlugins() as $key => $plugin) {
                if ($package['name'] == $plugin->getName()) {
                    $package['installed'] = true;
                }
            }

            $privatePackages[$resultKey] = $package;
        }

        return array(
            'allAvailablePlugins' => $allAvailablePlugins,
            'privatePackages' => $privatePackages,
            'form' => $form->createView(),
            'newscoopPath' => realpath($pluginService->getPluginsDir() .'/../'),
            'privatePluginsPath' => $pluginService->getPluginsDir().self::PRIVATE_PLUGINS_DIR,
            'privatePluginsPathWritable' => $privatePluginsPathWritable,
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
        $packagistResponse =  $browser->get('https://packagist.org/search.json?type=newscoop-plugin&q='.$query);
        $packages = json_decode($packagistResponse->getContent(), true);
        $results = $packages['results'];
        $this->aasort($results, 'downloads');

        // hide already installed plugins
        $cleanResults = array();
        $pluginService = $this->container->get('newscoop.plugins.service');
        foreach ($results as $resultKey => $package) {
            $installed = false;
            foreach ($pluginService->getAllAvailablePlugins() as $key => $plugin) {
                if ($package['name'] == $plugin->getName()) {
                    $installed = true;
                    $packages['total'] = $packages['total']-1;
                }
            }

            if (!$installed) {
                $cleanResults[] = $package;
            }
        }

        $packages['results'] = $cleanResults;

        return $response->setData($packages);
    }



    /**
     * @Route("/admin/plugins/chnageStatus/{action}/{pluginId}", requirements={"action" = "enable|disable"})
     */
    public function changePluginStatusAction(Request $request, $action, $pluginId)
    {
        $pluginService = $this->container->get('newscoop.plugins.service');
        $pluginsManager = $this->container->get('newscoop.plugins.manager');

        $plugin = $pluginService->getPluginByCriteria('id', intval($pluginId))->first();
        $em = $this->container->get('em');
        if ($action == 'enable') {
            $plugin->setEnabled(true);
        } else {
            $plugin->setEnabled(false);
        }

        $em->flush();

        // send event for plugin
        $pluginsManager->dispatchEventForPlugin($plugin->getName(), $action);

        return new Response(json_encode(array(
            $pluginId => $plugin->getEnabled()
        )));
    }

    private function createFolderStructure($pluginService)
    {
        $filesystem = new Filesystem();
        $privatePluginsPath = $pluginService->getPluginsDir() . self::PRIVATE_PLUGINS_DIR;
        $privatePluginsCachePath = $pluginService->getPluginsDir() . self::PRIVATE_PLUGINS_CACHE_DIR;
        if (!$filesystem->exists($privatePluginsPath)) {
            $filesystem->mkdir($privatePluginsPath);
        }

        if (!$filesystem->exists($privatePluginsCachePath)) {
            $filesystem->mkdir($privatePluginsCachePath);
        }
    }

    private function searchForPrivatePlugins($pluginService)
    {
        $packages = array();

        foreach (new \RecursiveDirectoryIterator($pluginService->getPluginsDir().self::PRIVATE_PLUGINS_DIR) as $file) {
            /* @var $file \SplFileInfo */
            if (!$file->isFile()) {
                continue;
            }

            if (!extension_loaded('zip')) {
                throw new Exception("In order to use private plugins, you need to have zip extension enabled");
            }

            $zip = new \ZipArchive();
            $zip->open($file->getPathname());

            if (0 == $zip->numFiles) {
                continue;
            }

            $foundFileIndex = $zip->locateName('composer.json', \ZipArchive::FL_NODIR);
            if (false === $foundFileIndex) {
                continue;
            }

            $configurationFileName = $zip->getNameIndex($foundFileIndex);

            $composerFile = "zip://{$file->getPathname()}#$configurationFileName";
            $json = file_get_contents($composerFile);

            $package = json_decode($json, true);
            $package['dist'] = array(
                'type' => 'zip',
                'url' => $file->getRealPath(),
                'reference' => $file->getBasename(),
                'shasum' => sha1_file($file->getRealPath())
            );

            $packages[] = $package;
        }

        return $packages;
    }

    public function dump_chunk($chunk)
    {
       echo $chunk;
       echo "\r\n";
       flush();
       ob_flush();
    }

    private function aasort(&$array, $key)
    {
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
