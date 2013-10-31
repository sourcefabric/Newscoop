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

class BootstrapService
{	
	private $mustBeWritable;
	private $filesystem;
	private $logger;

	public function __construct($logger){
		$this->mustBeWritable = array('cache', 'log', 'library/Proxy', 'themes');
		$this->filesystem = new Filesystem();
		$this->logger = $logger;
	}

	public function checkDirectories()
	{
		$notWritable = array();

		foreach ($this->mustBeWritable as $directory) {
			$fullPath = __DIR__ . '/../../../../' . $directory;
			if (!is_writable($fullPath)) {
				$notWritable[] = $fullPath;
			}
		}

		if (count($notWritable) > 0) {
			return $notWritable;
		}

		return true;
	}

	public function makeDirectoriesWritable()
	{
		foreach ($this->mustBeWritable as $directory) {
			$fullPath = __DIR__ . '/../../../../' . $directory;

			try {
				if (!$this->filesystem->exists($fullPath)) {
					$this->filesystem->mkdir($fullPath);
				}

				$this->filesystem->chown($fullPath, 'www-data', true);
				$this->filesystem->chmod($fullPath, 0777, 0000, true);
			} catch (IOException $e) {
				$this->logger->addDebug($e->getMessage());
			}
		}
	}
}