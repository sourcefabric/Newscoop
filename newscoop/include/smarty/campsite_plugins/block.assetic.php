<?php
/*
 * Smarty plugin
 * ------------------------------------------------------------
 * Type:       block
 * Name:       assetic
 * Purpose:    smarty plugin for Assetic
 * Author:     Pierre-Jean Parra, Paweł Mikołajczuk
 * Version:    1.0
 *
 * ------------------------------------------------------------
 */
use Assetic\AssetManager;
use Assetic\FilterManager;
use Assetic\Filter;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\Worker\CacheBustingWorker;
use Assetic\AssetWriter;
use Assetic\Asset\AssetCache;
use Assetic\Cache\FilesystemCache;


if (isset($_SERVER['LESSPHP'])) {
    require_once $_SERVER['LESSPHP'];
}

function smarty_block_assetic($params, $content, $template, &$repeat)
{
    // In debug mode, we have to be able to loop a certain number of times, so we use a static counter
    static $count;
    static $assetsUrls;

    $realpath = realpath( __DIR__.'/../../../'.$params['config_path']);
    $root = realpath($realpath.'/../').'/';
    $base_path = null;
    $bundles = null;
    $dependencies = null;

    $themesService = \Zend_Registry::get('container')->getService('newscoop_newscoop.themes_service');
    $themePath = $themesService->getThemePath();
    $viewBildPath = '/themes/'.$themePath.$params['build_path'];
    $params['build_path'] = $root.$params['build_path'];

    // Set defaults
    if (!array_key_exists('debug', $params)) {
        $params['debug'] = false;
    }

    // Read config file
    if (isset($params['config_path'])){
        $base_path = __DIR__.'/../../../'. $params['config_path'];
    }
    $config = json_decode(file_get_contents($base_path . '/config.json'));

    // Opening tag (first call only)
    if ($repeat) {
        // Read bundles and dependencies config files
        if (file_exists($base_path . '/bundles.json')) {
            $bundles = json_decode(file_get_contents($base_path . '/bundles.json'));
        }
        if (file_exists($base_path . '/dependencies.json')) {
            $dependencies = json_decode(file_get_contents($base_path . '/dependencies.json'));
        }

        $am = new AssetManager();

        $fm = new FilterManager();

        if (property_exists($config, 'cssembed_path')) {
            $cssEmbedFilter = new Filter\CssEmbedFilter($config->cssembed_path, $config->java_path);
            $cssEmbedFilter->setRoot($root);
            $fm->set('cssembed', $cssEmbedFilter);
        }

        if (property_exists($config, 'yuicompressor_path') && property_exists($config, 'java_path')) {
            $fm->set('yui_js', new Filter\Yui\JsCompressorFilter($config->yuicompressor_path, $config->java_path));
            $fm->set('yui_css', new Filter\Yui\CssCompressorFilter($config->yuicompressor_path, $config->java_path));
        }
        $fm->set('less', new Filter\LessphpFilter());
        $fm->set('sass', new Filter\Sass\SassFilter());
        $fm->set('closure_api', new Filter\GoogleClosure\CompilerApiFilter());
        $fm->set('rewrite', new Filter\CssRewriteFilter());
        if (property_exists($config, 'closurejar_path') && property_exists($config, 'java_path')) {
            $fm->set('closure_jar', new Filter\GoogleClosure\CompilerJarFilter($config->closurejar_path, $config->java_path));
        }

        // Factory setup
        $factory = new AssetFactory($root);
        $factory->setAssetManager($am);
        $factory->setFilterManager($fm);
        $factory->setDefaultOutput('*.'.$params['output']);
        $factory->setDebug($params['debug']);

        if (isset($params['filters'])) {
            $filters = explode(',', $params['filters']);
        } else {
            $filters = array();
        }

        // Prepare the assets writer
        $writer = new AssetWriter($params['build_path']);

        // If a bundle name is provided
        if (isset($params['bundle'])) {
            $asset = $factory->createAsset(
                $bundles->$params['output']->$params['bundle'],
                $filters
            );

            $cache = new AssetCache(
                $asset,
                new FilesystemCache($params['build_path'])
            );

            $writer->writeAsset($cache);
        // If individual assets are provided
        } elseif (isset($params['assets'])) {
            $assets = array();
            // Include only the references first
            foreach (explode(',', $params['assets']) as $a) {
                // If the asset is found in the dependencies file, let's create it
                // If it is not found in the assets but is needed by another asset and found in the references, don't worry, it will be automatically created
                if (isset($dependencies->$params['output']->assets->$a)) {
                    // Create the reference assets if they don't exist
                    foreach ($dependencies->$params['output']->assets->$a as $ref) {
                        try {
                            $am->get($ref);
                        }
                        catch (InvalidArgumentException $e) {
                            $path = $dependencies->$params['output']->references->$ref;

                            $assetTmp = $factory->createAsset($path);
                            $am->set($ref, $assetTmp);
                            $assets[] = '@'.$ref;
                        }
                    }
                }
            }

            // Now, include assets
            foreach (explode(',', $params['assets']) as $a) {
                // Add the asset to the list if not already present, as a reference or as a simple asset
                $ref = null;
                if (isset($dependencies->$params['output'])) {
                    foreach ($dependencies->$params['output']->references as $name => $file) {
                        if ($file == $a) {
                            $ref = $name;
                            break;
                        }
                    }
                }

                if (array_search($a, $assets) === FALSE && ($ref === null || array_search('@' . $ref, $assets) === FALSE)) {
                    $assets[] = $a;
                }
            }

            // Create the asset
            $asset = $factory->createAsset(
                $assets,
                $filters
            );

            $cache = new AssetCache(
                $asset,
                new FilesystemCache($params['build_path'])
            );

            $writer->writeAsset($cache);
        }

        // If debug mode is active, we want to include assets separately
        if ($params['debug']) {
            $assetsUrls = array();
            foreach ($asset as $a) {

                $cache = new AssetCache(
                    $a,
                    new FilesystemCache($params['build_path'])
                );

                $writer->writeAsset($cache);
                $assetsUrls[] = $a->getTargetPath();
            }
            // It's easier to fetch the array backwards, so we reverse it to insert assets in the right order
            $assetsUrls = array_reverse($assetsUrls);

            $count = count($assetsUrls);

            if (isset($config->site_url))
                $template->assign($params['asset_url'], $config->site_url.'/'.$params['build_path'].'/'.$assetsUrls[$count-1]);
            else
                $template->assign($params['asset_url'], $viewBildPath.'/'.$assetsUrls[$count-1]);


        // Production mode, include an all-in-one asset
        } else {
            if (isset($config->site_url)) {
                $template->assign($params['asset_url'], $config->site_url.'/'.$params['build_path'].'/'.$asset->getTargetPath());
            } else {
                $template->assign($params['asset_url'], $viewBildPath.'/'.$asset->getTargetPath());
            }

        }

    // Closing tag
    } else {
        if (isset($content)) {
            // If debug mode is active, we want to include assets separately
            if ($params['debug']) {
                $count--;
                if ($count > 0) {
                    if (isset($config->site_url))
                        $template->assign($params['asset_url'], $config->site_url.'/'.$params['build_path'].'/'.$assetsUrls[$count-1]);
                    else
                        $template->assign($params['asset_url'], '/'.$params['build_path'].'/'.$assetsUrls[$count-1]);
                }
                $repeat = $count > 0;
            }

            return $content;
        }
    }

}
