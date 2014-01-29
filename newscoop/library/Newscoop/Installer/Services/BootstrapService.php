<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Installer\Services;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Bootstrap installer service
 */
class BootstrapService
{
    public $mustBeWritable;
    public $basePath;
    private $filesystem;
    private $logger;

    /**
     * Construct class
     *
     * @param object $logger
     * @param array  $directories
     * @param string $basePath
     */
    public function __construct(
        $logger = null,
        $directories = array('cache', 'log', 'library/Proxy', 'themes'),
        $basePath = null
    ) {
        $this->mustBeWritable = $directories;
        $this->filesystem = new Filesystem();
        $this->logger = $logger;
        $this->basePath = $basePath;

        if (is_null($basePath)) {
            $this->basePath = __DIR__ . '/../../../../';
        }
    }

    /**
     * Check if all directories are writable
     *
     * @return mixed true if all writable or array with not writable directories
     */
    public function checkDirectories()
    {
        $notWritable = array();

        foreach ($this->mustBeWritable as $directory) {
            $fullPath = $this->basePath .'/'.$directory;
            if (!is_writable($fullPath)) {
                $notWritable[] = $fullPath;
            }
        }

        if (count($notWritable) > 0) {
            return $notWritable;
        }

        return true;
    }

    /**
     * Try to make all directories writeable
     *
     * @return boolean
     */
    public function makeDirectoriesWritable()
    {
        foreach ($this->mustBeWritable as $directory) {
            $fullPath = $this->basePath .'/'. $directory;

            try {
                if (!$this->filesystem->exists($fullPath)) {
                    $this->filesystem->mkdir($fullPath);
                }

                $this->filesystem->chown($fullPath, 'www-data', true);
                $this->filesystem->chmod($fullPath, 0777, 0000, true);
            } catch (IOException $e) {
                if ($this->logger != null) {
                    $this->logger->addDebug($e->getMessage());
                }
            }
        }

        return true;
    }
}
