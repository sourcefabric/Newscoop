<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Implementation;

use Newscoop\Service\Exception\DuplicateNameException;

use Newscoop\Version;

use Newscoop\Service\Implementation\Exception\FailedException;
use Newscoop\Service\Error\ThemeErrors;
use Newscoop\Service\IOutputService;
use Newscoop\Entity\Resource;
use Newscoop\Service\Resource\ResourceId;
use Newscoop\Service\Model\SearchTheme;
use Newscoop\Entity\OutputSettings;
use Newscoop\Entity\Output;
use Newscoop\Entity\Theme;
use Newscoop\Entity\Publication;
use Newscoop\Service\IThemeManagementService;
use Newscoop\Utils\Validation;

/**
 * Provides the management services implementation for the themes.
 * The implementation relays on the local structure, this means that this service will use also the file system and Doctrine
 * for synchronizing with the database.
 */
class ThemeManagementServiceLocal extends ThemeServiceLocalFileSystem implements IThemeManagementService
{
	/**
	 * Provides the relative folder name where the unassigned themes are located.
	 */
	const FOLDER_UNASSIGNED = 'unassigned';

	/**
	 * Provides the prefix fodler name for the folders that contain themes for a publication.
	 */
	const FOLDER_PUBLICATION_PREFIX = 'publication_';

	/**
	 * Provides the prefix fodler name for the folders that are themes.
	 */
	const FOLDER_THEME_PREFIX = 'theme_';

	/**
	 * Provides the template extension.
	 */
	const FILE_TEMPLATE_EXTENSION = 'tpl';

	const TAG_OUTPUT = 'output';
	const ATTR_OUTPUT_NAME = 'name';

	const TAG_PAGE_FRONT = 'front-page';
	const TAG_PAGE_SECTION = 'section-page';
	const TAG_PAGE_ARTICLE = 'article-page';
	const TAG_PAGE_ERROR = 'error-page';
	const ATTR_PAGE_SRC = 'src';

	/* --------------------------------------------------------------- */

	/** @var Newscoop\Service\IOutputService */
	private $outputService = NULL;

	/* ------------------------------- */

	/**
	 * Provides the ouput service.
	 *
	 * @return Newscoop\Service\IOutputService
	 *		The service service to be used.
	 */
	public function getOutputService()
	{
		if ($this->outputService === NULL) {
			$this->outputService = $this->getResourceId()->getService(IOutputService::NAME);
		}
		return $this->outputService;
	}

	/* --------------------------------------------------------------- */

	function getUnassignedThemes(SearchTheme $search = NULL, $offset = 0, $limit = -1)
	{
		$allConfigs = $this->findAllThemesConfigPaths();
		$configs = array();

		$length = strlen(self::FOLDER_UNASSIGNED);
		foreach ($allConfigs as $id => $config){
			if(strncmp($config, self::FOLDER_UNASSIGNED, $length) == 0){
				$configs[$id] = $config;
			}
		}

		$themes = $this->loadThemes($configs);
		if($search !== NULL){
			$themes = $this->filterThemes($search, $themes);
		}

		return $this->trim($themes, $offset, $limit);
	}

	function getThemes(Publication $publication, SearchTheme $search = NULL, $offset = 0, $limit = -1)
	{
		Validation::notEmpty($publication, 'publication');
		Validation::notEmpty($publication->getId(), 'publication.id');

		$allConfigs = $this->findAllThemesConfigPaths();
		$configs = array();

		$pubFolder = self::FOLDER_PUBLICATION_PREFIX.$publication->getId();
		$length = strlen($pubFolder);
		foreach ($allConfigs as $id => $config){
			if(strncmp($config, $pubFolder, $length) == 0){
				$configs[$id] = $config;
			}
		}

		$themes = $this->loadThemes($configs);
		if($search !== NULL){
			$themes = $this->filterThemes($search, $themes);
		}

		return $this->trim($themes, $offset, $limit);
	}

	function getTemplates(Theme $theme)
	{
		Validation::notEmpty($theme, 'theme');

		$resources = array();
		$folder = $this->themesFolder.$theme->getPath();
		if (is_dir($folder)) {
			if($dh = opendir($folder)){
				while (($file = readdir($dh)) !== false) {
					if ($file != "." && $file != ".."){
						if(pathinfo($file, PATHINFO_EXTENSION) === self::FILE_TEMPLATE_EXTENSION){
							$rsc = new Resource();
							$rsc->setName($file);
							$rsc->setPath($theme->getPath().$file);
							$resources[] = $rsc;
						}
					}
				}
				closedir($dh);
			}
		}

		return $resources;
	}

	function findOutputSetting(Theme $theme, Output $output)
	{
		Validation::notEmpty($theme, 'theme');
		Validation::notEmpty($output, 'output');

		$xml = $this->loadXML($this->toFullPath($theme, $this->themeConfigFileName));

		foreach ($xml->children() as $node){
			/* @var $node \SimpleXMLElement */
			if($node->getName() === self::TAG_OUTPUT){
				try{
					$outputName = $this->readAttribute($node, self::ATTR_OUTPUT_NAME);
					if($output->getName() == $outputName){
						$oset = $this->loadOutputSetting($node, $theme->getPath());
						$oset->setOutput($output);

						return $oset;
					}
				}catch(FailedException $e){
					// Nothing to do.
				}
			}
		}

		return NULL;
	}

	function getOutputSettings(Theme $theme)
	{
		Validation::notEmpty($theme, 'theme');

		$xml = $this->loadXML($this->toFullPath($theme, $this->themeConfigFileName));

		$outputs = array();
		foreach ($xml->children() as $node){
			/* @var $node \SimpleXMLElement */
			if($node->getName() === self::TAG_OUTPUT){
				try{
					// First we have to search if there is an ouput
					// registered with the name specifed in the XML.
					$outputName = $this->readAttribute($node, self::ATTR_OUTPUT_NAME);
					$output = $this->getOutputService()->findByName($outputName);
					if($output != NULL){
						$oset = $this->loadOutputSetting($node, $theme->getPath());
						$oset->setOutput($output);

						$outputs[] = $oset;
					} else {
						$this->getErrorHandler()->warning(ThemeErrors::OUTPUT_MISSING, $outputName);
					}
				}catch(FailedException $e){
					// Nothing to do.
				}
			}
		}

		return $outputs;
	}

	/* --------------------------------------------------------------- */

	function updateTheme(Theme $theme)
	{
		Validation::notEmpty($theme, 'theme');
		$allConfigs = $this->findAllThemesConfigPaths();

		$config = $allConfigs[$theme->getId()];
		if(!isset($config)){
			throw new \Exception("Unknown theme id '.$theme->getId().' to update.");
		}
		// We have to check if there is no other theme by the new theme name.
		$inFolder = $this->filterThemesConfigPathsInSameFolder($config, $allConfigs);
		// Remove the theme to be updated.
		unset($inFolder[$theme->getId()]);

		$themes = $this->loadThemes($inFolder);
		foreach($themes as $th){
			/* @var $th Theme */
			if(trim($th->getName()) === trim($theme->getName())){
				throw new DuplicateNameException();
			}
		}

		$xml = $this->loadXML($this->themesFolder.$config);
		$xml[self::ATTR_THEME_NAME] = $theme->getName();
		$xml[self::ATTR_THEME_DESIGNER] = $theme->getDesigner();
		$xml[self::ATTR_THEME_VERSION] = $theme->getVersion();
		$xml[self::ATTR_THEME_NEWSCOOP_VERSION] = Version::VERSION;
		$xml->{self::TAG_DESCRIPTION} = $theme->getDescription();

		$xml->asXML($this->toFullPath($theme, $this->themeConfigFileName));
	}

	function assignTheme(Theme $theme, Publication $publication)
	{
		Validation::notEmpty($theme, 'theme');
		Validation::notEmpty($publication, 'publication');

		$number = 1;
		$pubFolder = self::FOLDER_PUBLICATION_PREFIX.$publication->getId().'/';
		$pubFullfodler = $this->themesFolder.$pubFolder;
		$length = strlen(self::FOLDER_THEME_PREFIX);
		if (is_dir($pubFullfodler)) {
			if ($dh = opendir($pubFullfodler)) {
				while (($dir = readdir($dh)) !== false) {
					if ($dir != "." && $dir != ".." && is_dir($pubFullfodler.$dir)){
						if(strncmp($dir, self::FOLDER_THEME_PREFIX, $length) == 0){
							$themeNr = substr($dir, $length);
							if(is_numeric($themeNr)){
								$number = ((int)$themeNr) + 1;
							}
						}
					}
				}
				closedir($dh);
			}
		} else {
			mkdir($pubFullfodler);
		}

		$themeFolder = $pubFolder.self::FOLDER_THEME_PREFIX.$number.'/';
		$themeFullFolder = $this->themesFolder.$themeFolder;

		// We have to check if there is no other theme by the new theme name.
		$inFolder = $this->filterThemesConfigPathsInSameFolder($themeFolder.$this->themeConfigFileName, $this->findAllThemesConfigPaths());

		$themes = $this->loadThemes($inFolder);
		foreach($themes as $th){
			/* @var $th Theme */
			if(trim($th->getName()) === trim($theme->getName())){
				throw new DuplicateNameException();
			}
		}

		mkdir($themeFullFolder);

		//TODO add failover in  case of copy failed.
		$this->copy($this->themesFolder.$theme->getPath(), $themeFullFolder);

		// Reset the theme configs cache so also the new theme will be avaialable
		$this->cacheThemeConfigs = NULL;

		//TODO add OutputSettingsTheme persistance
	}

	function assignOutputSetting(OutputSettings $outputSettings, Theme $theme)
	{
		//TODO: implement
	}

	/* --------------------------------------------------------------- */

	/**
	 * load the output setting from the provided xml node.
	 *
	 * @param \SimpleXMLElement $node
	 * 		The node from which to load, *(not null not empty).
	 *
	 * @param string $themePath
	 * 		The theme path to construct the resource path based on, *(not null not empty).
	 * @throws FailedException
	 * 		Thrown if the output setting has failed to be obtained, this exception will not contain any message, the resons of failure
	 * 		will be looged in the error handler.
	 * @return \Newscoop\Entity\OutputSettings
	 * 		The loaded output setting, not null.
	 */
	protected function loadOutputSetting(\SimpleXMLElement $node, $themePath)
	{
		$oset = new OutputSettings();

		$oset->setFrontPage($this->loadOutputResource($node, self::TAG_PAGE_FRONT, $themePath));
		$oset->setSectionPage($this->loadOutputResource($node, self::TAG_PAGE_SECTION, $themePath));
		$oset->setArticlePage($this->loadOutputResource($node, self::TAG_PAGE_ARTICLE, $themePath));
		$oset->setErrorPage($this->loadOutputResource($node, self::TAG_PAGE_ERROR, $themePath));

		return $oset;
	}

	/**
	 * Reads the resources from an output tag.
	 * @param \SimpleXMLElement $parent
	 * 		The parent output node to read the resources from, *(not null not empty).
	 * @param string $tagName
	 * 		The tag name containing the resource, *(not null not empty).
	 * @param string $themePath
	 * 		The theme path to construct the resource path based on, *(not null not empty).
	 * @param string $name
	 * 		The name of the created resource based on the found tag, *(not null not empty).
	 * @throws FailedException
	 * 		Thrown if the resource has failed to be obtained, this exception will not contain any message, the resons of failure
	 * 		will be looged in the error handler.
	 * @return \Newscoop\Entity\Resource
	 * 		The obtained resource, not null.
	 */
	protected function loadOutputResource(\SimpleXMLElement $parent, $tagName, $themePath)
	{
		foreach ($parent->children() as $node){
			/* @var $node \SimpleXMLElement */
			if($node->getName() === $tagName){
				try{
					$rsc = new Resource();
					$rsc->setName($tagName);
					$rsc->setPath($this->escapePath($themePath.$this->readAttribute($node, self::ATTR_PAGE_SRC)));
					return $rsc;
				}catch(XMLMissingAttribueException $e){
					$this->getErrorHandler()->error(ThemeErrors::XML_MISSING_ATTRIBUTE, $e->getAttributeName(), $tagName);
					throw new FailedException();
				}
			}
		}
		$this->getErrorHandler()->error(ThemeErrors::XML_MISSING_TAG, $tagName, $parent->getName());
		throw new FailedException();
	}

	/* --------------------------------------------------------------- */

	/**
	 * Filter from the provided configs array all the configs that are located under the same folder.
	 * For instance if the config is in a publication folder than this method will return all the configs for that
	 * publicatio.
	 *
	 * @param string $config
	 * 		The config path to be searched for, not null.
	 * @param array @configs
	 * 		The array containing as key the id of the theme config (index) and as a value the relative
	 * 		path of the theme configuration XML file for all configurations to be filtered, not null.
	 * @return array
	 * 		The array containing as key the id of the theme config (index) and as a value the relative
	 * 		path of the theme configuration XML file for all configurations that are iun the same folder, not null can be empty.
	 */
	protected function filterThemesConfigPathsInSameFolder($config, array $allConfigs)
	{
		// First we extract the relative path for the 'theme.xml' config file
		$rPath = $this->extractRelativePathFrom($config);
		// Now we extract the relative path of the theme folder.
		$rPath = $this->extractRelativePathFrom(substr($rPath, 0, -1));

		$inFolder = array();
		$length = strlen($rPath);
		foreach ($allConfigs as $id => $cnf){
			if(strncmp($cnf, $rPath, $length) == 0){
				$inFolder[$id] = $cnf;
			}
		}

		return $inFolder;
	}

	/**
	 * Copies recursivelly the folder content from src to destination.
	 * @param string $src
	 * 		The source folder, *(not null not empty).
	 * @param string $dst
	 * 		the destination folder, *(not null not empty).
	 */
	protected function copy($src, $dst)
	{
		$dir = opendir($src);
		mkdir($dst);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					$this->copy($src . '/' . $file,$dst . '/' . $file);
				}
				else {
					copy($src . '/' . $file,$dst . '/' . $file);
				}
			}
		}
		closedir($dir);
	}
}