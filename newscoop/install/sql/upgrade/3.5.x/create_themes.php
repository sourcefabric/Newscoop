<?php

use Symfony\Component\Console\Input,
	Doctrine\DBAL\Types,
	Newscoop\Storage,
	Newscoop\Entity,
	Newscoop\Service\IThemeManagementService,
	Newscoop\Service\Resource;

require_once dirname(__FILE__) . '/../../../../conf/configuration.php';
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
$themesPath = realpath(APPLICATION_PATH . '/../themes/unassigned');

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

	/**
	 * @var Newscoop\Entity\Resource
	 */
	private $resourceId;

	/**
	 * @var Newscoop\Service\IThemeService
	 */
	private $themeService;

	/**
	 * @var string
	 */
	private $themeXMLFile = 'theme.xml';


	public function __construct($templatesPath, $themesPath)
	{
		$this->templatesPath = $templatesPath;
		$this->themesPath = $themesPath;
	}


    /**
     * Provides the controller resource id.
     *
     * @return Newscoop\Services\Resource\ResourceId
     * 		The controller resource id.
     */
    public function getResourceId()
    {
        if ($this->resourceId === NULL) {
            $this->resourceId = new Newscoop\Service\Resource\ResourceId(__CLASS__);
        }
        return $this->resourceId;
    }


    /**
     * Provides the theme service.
     *
     * @return Newscoop\Service\IThemeService
     * 		The theme service to be used by this controller.
     */
    public function getThemeService()
    {
        if ($this->themeService === NULL) {
            $this->themeService = $this->getResourceId()->getService(IThemeManagementService::NAME_1);
        }
        return $this->themeService;
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
			if (!is_dir($filePath) || $fileName == 'system_templates'
			|| count(glob($filePath . "/*.tpl")) == 0) {
				continue;
			}

			$themes[$fileName] = $this->createName($fileName);
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
	public function createThemes()
	{
//		foreach ($this->themesList() as $themePath=>$themeName) {
//			$this->createTheme($themePath);
//		}

		$themes = $this->getThemeService()->getEntities();
		foreach ($themes as $theme) {
			$theme->setName($this->createName(basename($theme->getPath())));
			$this->getThemeService()->updateTheme($theme);
		}
	}


	public function setThemeOutputSettings(Theme $theme)
	{
		global $g_ado_db;

		$sql = "SELECT DISTINCT iss.IdPublication
FROM Issues AS iss
    LEFT JOIN Templates AS tpl_f ON iss.IssueTplId = tpl_f.Id
    LEFT JOIN Templates AS tpl_s ON iss.SectionTplId = tpl_s.Id
    LEFT JOIN Templates AS tpl_a ON iss.ArticleTplId = tpl_a.Id";
		$publicationIds = $g_ado_db->GetAll($sql);
		foreach ($publicationIds as $publicationId) {
			$sql = "SELECT IssueTplId, SectionTplId, ArticleTplId, pub.url_error_tpl_id
FROM Issues AS iss LEFT JOIN Publications AS pub ON iss.IdPublication = pub.Id
WHERE IdPublication = $publicationId AND IssueTplId > 0 AND SectionTplId > 0 AND ArticleTplId > 0
ORDER BY Number DESC
LIMIT 0, 1";
			$outSettings = $g_ado_db->GetAll($sql);
			$frontTemplateId = $outSettings['IssueTplId'];
			$sectionTemplateId = $outSettings['SectionTplId'];
			$articleTemplateId = $outSettings['ArticleTplId'];
			$errorTemplateId = $outSettings['url_error_tpl_id'];
		}
	}


    /**
	 * Moves a group of templates from their directory to the new theme structure, creating the theme
	 *
	 * @param string $themeSrcDir
	 * @return bool
	 * 		True if the move was performed succesfully, false otherwise
	 */
	public function createTheme($themeSrcDir)
	{
		$srcPath = $this->templatesPath . (empty($themeSrcDir) ? '' : '/' . $themeSrcDir);
		$dstPath = $this->themesPath . (empty($themeSrcDir) ? '/default' : '/' . $themeSrcDir);

		mkdir($dstPath);
		copy(dirname(__FILE__) . '/' . $this->themeXMLFile, "$dstPath/$this->themeXMLFile");
		return $this->copyPath($srcPath, $dstPath);
	}


	/**
	 * Copies the given file or directory to the destination path.
	 *
	 * @param string $srcPath
	 *
	 * @param string $dstPath
	 *
	 * @return bool
	 */
	private function copyPath($srcPath, $dstPath)
	{
		if (is_dir($srcPath)) {
			echo "<p>creating $dstPath</p>\n";
			if (!is_dir($dstPath)) {
				mkdir($dstPath);
			}
			$files = array_merge(glob($srcPath . "/*"), glob($srcPath . "/.*"));
			foreach ($files as $filePath) {
				if (basename($filePath) == '.' || basename($filePath) == '..') {
					continue;
				}
				if (!$this->copyPath($filePath, $dstPath . '/' . basename($filePath))) {
					return false;
				}
			}
			return true;
		} elseif (is_file($srcPath)) {
			echo "<p>moving $srcPath to $dstPath</p>\n";
			return copy($srcPath, $dstPath);
		}
		return false;
	}
}


$themeUpgrade = new ThemeUpgrade($templatesPath, $themesPath);
print_r($themeUpgrade->themesList());
$themeUpgrade->createThemes();
