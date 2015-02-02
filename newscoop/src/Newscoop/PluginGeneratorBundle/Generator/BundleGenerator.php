<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Newscoop\PluginGeneratorBundle\Generator;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\Container;

/**
 * Generates a bundle.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class BundleGenerator extends Generator
{
    private $filesystem;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->filesystem = $this->container->get('filesystem');
    }

    public function generate($vendor, $pluginName, $namespace, $bundle, $dir, $format, $structure, $admin, $zip)
    {
        $dir .= '/'.strtr($namespace, array(ucwords($vendor) => '', '\\' => '', '\/' => ''));
        if (file_exists($dir)) {
            if (!is_dir($dir)) {
                throw new \RuntimeException(sprintf('Unable to generate the bundle as the target directory "%s" exists but is a file.', realpath($dir)));
            }
            $files = scandir($dir);
            if ($files != array('.', '..')) {
                throw new \RuntimeException(sprintf('Unable to generate the bundle as the target directory "%s" is not empty.', realpath($dir)));
            }
            if (!is_writable($dir)) {
                throw new \RuntimeException(sprintf('Unable to generate the bundle as the target directory "%s" is not writable.', realpath($dir)));
            }
        }

        $basename = substr($bundle, 0, -6);
        $parameters = array(
            'vendor' => $vendor,
            'admin' => $admin,
            'pluginName' => $pluginName,
            'namespace' => $namespace,
            'bundle'    => $bundle,
            'format'    => $format,
            'bundle_basename' => $basename,
            'extension_alias' => Container::underscore($basename),
        );

        $this->renderFile('bundle/composer.json.twig', $dir.'/composer.json', $parameters);
        $this->renderFile('bundle/Bundle.php.twig', $dir.'/'.$bundle.'.php', $parameters);
        $this->renderFile('bundle/Extension.php.twig', $dir.'/DependencyInjection/'.$basename.'Extension.php', $parameters);
        $this->renderFile('bundle/Configuration.php.twig', $dir.'/DependencyInjection/Configuration.php', $parameters);
        $this->renderFile('bundle/LifecycleSubscriber.php.twig', $dir.'/EventListener/LifecycleSubscriber.php', $parameters);
        $this->renderFile('bundle/DefaultController.php.twig', $dir.'/Controller/'.$pluginName.'Controller.php', $parameters);
        $this->renderFile('bundle/DefaultControllerTest.php.twig', $dir.'/Tests/Controller/'.$pluginName.'ControllerTest.php', $parameters);
        $this->renderFile('bundle/index.html.twig.twig', $dir.'/Resources/views/'.$pluginName.'/index.html.twig', $parameters);

        if ('xml' === $format || 'annotation' === $format) {
            $this->renderFile('bundle/services.xml.twig', $dir.'/Resources/config/services.xml', $parameters);
        } else {
            $this->renderFile('bundle/services.'.$format.'.twig', $dir.'/Resources/config/services.'.$format, $parameters);
        }

        if ('annotation' != $format) {
            $this->renderFile('bundle/routing.'.$format.'.twig', $dir.'/Resources/config/routing.'.$format, $parameters);
        }

        if ($admin) {
            $this->renderFile('bundle/ConfigurationMenuListener.php.twig', $dir.'/EventListener/ConfigurationMenuListener.php', $parameters);
            $this->renderFile('bundle/AdminController.php.twig', $dir.'/Controller/AdminController.php', $parameters);
            $this->renderFile('bundle/admin.index.html.twig.twig', $dir.'/Resources/views/Admin/index.html.twig', $parameters);
        }

        if ($structure) {
            $this->renderFile('bundle/messages.fr.xlf', $dir.'/Resources/translations/messages.fr.xlf', $parameters);

            $this->filesystem->mkdir($dir.'/Resources/doc');
            $this->filesystem->touch($dir.'/Resources/doc/index.rst');
            $this->filesystem->mkdir($dir.'/Resources/translations');
            $this->filesystem->mkdir($dir.'/Resources/public/css');
            $this->filesystem->mkdir($dir.'/Resources/public/images');
            $this->filesystem->mkdir($dir.'/Resources/public/js');
        }

        if ($zip) {
            $zipFilePath = dirname($this->container->getParameter('kernel.root_dir')).'/plugins/private_plugins/' . $vendor . $pluginName .'PluginBundle.zip';
            $zipFile = new \ZipArchive();
            // open archive
            if ($zipFile->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
                throw new \RuntimeException(sprintf('Could not create zip archive "%s".', realpath($zipFilePath)));
            }

            $dirPathLength = strlen($dir);
            // initialize an iterator
            // pass it the directory to be processed
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
            // iterate over the directory
            // add each file found to the archive
            $addedDirs = array('/');
            foreach ($iterator as $key=>$value) {
                $fname = substr($key, $dirPathLength);
                if (strlen($fname) > 0 && !in_array(basename($fname), array('.', '..')) ) {
                    if ( !in_array(dirname($fname),$addedDirs) ) {
                        if (!$zipFile->addEmptyDir(dirname($fname))) {
                            return false;
                        }
                        $addedDirs[]=dirname($fname);
                    }
                    if (!$zipFile->addFile(realpath($key), ltrim($fname, '/'))) {
                        throw new \RuntimeException(sprintf('Could not add file to zip archive "%s".', $key));
                    }
                }
            }

            // close and save archive
            if (!$zipFile->close()) {
                throw new \RuntimeException(sprintf('Could not save zip archive "%s".', realpath($zipFilePath)));
            }
        }
    }
}
