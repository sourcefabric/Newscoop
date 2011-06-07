<?php

use Symfony\Component\Console\Input,
	Doctrine\DBAL\Types,
	Newscoop\Storage,
	Newscoop\Entity;

require_once dirname(__FILE__) . '/../../../../db_connect.php';

global $g_ado_db;


// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../../../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
    '/usr/share/php/libzend-framework-php',
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);


require 'Doctrine/Common/ClassLoader.php';
$classLoader = new \Doctrine\Common\ClassLoader('Newscoop', realpath(APPLICATION_PATH . '/../library'));
$classLoader->register(); // register on SPL autoload stack

$templatesPath = realpath(APPLICATION_PATH . '/../templates');
$themesPath = realpath(APPLICATION_PATH . '/../themes');

$storage = new Storage($templatesPath);
$items = $storage->listItems('');
//print_r($items);


class ThemeUpgrade
{
	/**
	 * @var string
	 */
	private $templatesPath;

	/**
	 * @var string
	 */
	private $themesPath;


	public function __construct($templatesPath, $themesPath)
	{
		$this->templatesPath = $templatesPath;
		$this->themesPath = $themesPath;
	}


	/**
	 * Returns an array of themes (theme path)
	 *
	 * @return array
	 */
	public function themesList()
	{
		$hasOneTheme = count(glob($this->templatesPath . "/*.tpl")) > 0;
		if ($hasOneTheme) {
			return array(''=>'Default');
		}
		$themes = array();
		foreach (glob($this->templatesPath . "/*") as $filePath) {
			$fileName = basename($filePath);
			if (is_dir($filePath) && $fileName != 'system_templates') {
				$themes[$fileName] = $this->createName($fileName);
			}
		}
		return $themes;
	}


	/**
	 * Creates a name from the theme path
	 *
	 * @param string $themePath
	 *
	 * @return string
	 * 		Returns the theme name
	 */
	public function createName($themePath)
	{
		$parts = preg_split('/[\s_\-.,]+/', $themePath);
		$name = implode(' ', array_map('ucfirst', $parts));
		return $name;
	}


	/**
	 * Moves the existing templates into the new themes structure
	 *
	 * @return bool
	 * 		True on success, false otherwise
	 */
	public function moveThemes()
	{
	}


	/**
	 * Moves the templates from a directory to the new theme structure
	 *
	 * @param string $themeDir
	 * @param string $themeName
	 * @return bool
	 * 		True if the upgrade was performed succesfully, false otherwise
	 */
	public function moveTheme(Theme $theme)
	{
		$srcPath = $this->templatesPath . ($theme->getPath() == '' ? '' : '/' . $theme->getPath());
		return $this->moveFile($srcPath, $this->themesPath);
	}


	/**
	 * Moves the content of the source directory to the destination directory.
	 *
	 * @param string $srcDir
	 *
	 * @param string $dstDir
	 *
	 * @return bool
	 */
	private function moveFile($srcPath, $dstPath)
	{
		if (is_dir($srcPath)) {
			$dstPath .= '/' . basename($srcPath);
			mkdir($dstPath);
			$files = array_merge(glob($srcPath . "/*"), glob($srcPath . "/.*"));
			foreach ($files as $filePath) {
				if (!$this->moveFile($filePath, $dstPath)) {
					return false;
				}
			}
			return true;
		} elseif (is_file($srcPath)) {
			echo "<p>moving $srcPath to $dstPath</p>\n";
			return true;
			return move($srcPath, $dstPath);
		}
		return false;
	}
}


$themeUpgrade = new ThemeUpgrade($templatesPath, $themesPath);
print_r($themeUpgrade->themesList());
