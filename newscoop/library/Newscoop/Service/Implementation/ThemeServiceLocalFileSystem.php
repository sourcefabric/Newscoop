<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Implementation;

use Newscoop\Entity\Theme\Resource;
use Newscoop\Entity\Theme;
use Newscoop\Utils\Validation;
use Newscoop\Service\IThemeService;
use Newscoop\Service\Resource\ResourceId;
use Newscoop\Service\Model\Search\Search;
use Newscoop\Service\Model\SearchTheme;

/**
 * Provides the services implementation for the themes.
 */
class ThemeServiceLocalFileSystem implements IThemeService
{

	/** @var Newscoop\Service\Resource\ResourceId */
	private $id;

	/* ------------------------------- */

	/** @var string */
	public $themesFolder;
	/** @var string */
	public $themeConfigFileName = 'theme.xml';


	/**
	 * Construvt the service base d on the provided resource id.
	 * @param ResourceId $id
	 * 		The resource id, not null not empty
	 */
	function __construct(ResourceId $id)
	{
		Validation::notEmpty($id, 'id');
		$this->id = $id;
	}

	/* --------------------------------------------------------------- */

	function getById($id)
	{
		$theme = $this->findById($id);
		if(is_null($theme)){
			throw new \Exception("There is no theme for id '$id'.");
		}
		return $theme;
	}

	function findById($id)
	{
		Validation::notEmpty($id, 'id');

		$path = $this->themesFolder.$id;
		if(file_exists($path)){
			return $this->loadTheme($this->loadXML($path), $id);
		}
		return NULL;
	}

	function getCount(Search $search = NULL)
	{
		$themesConfigs = $this->findAllThemesConfigPaths();
		if(search === NULL){
			return count($themesConfigs);
		}
		$themes = $this->loadThemes($themesConfigs);
		$themes = $this->filterThemes($search, $themes);
		return count($themes);
	}

	function getEntities(Search $search = NULL, $offset = 0, $limit = -1)
	{
		$themes = $this->loadThemes($this->findAllThemesConfigPaths());
		if($search !== NULL){
			$themes = $this->filterThemes($search, $themes);
		}
		$themes = array_slice($themes, $offset);
		if($limit >= 0){
			$themes = array_slice($themes, 0, $limit);
		}

		return $themes;
	}

	/* --------------------------------------------------------------- */

	function getPresentationImages(Theme $theme)
	{
		Validation::notEmpty($theme, 'theme');

		$xml = $this->loadXML($this->themesFolder.$theme->getId());

		$path = $this->extractRelativePathFrom($theme->getId());
		$presentResources = array();
		foreach ($xml->children() as $node){
			/* @var $node \SimpleXMLElement */
			if($node->getName() === 'presentation-img'){
				$presentResources[] = $this->loadResource($node, $path);
			}
		}

		return $presentResources;
	}

	/* --------------------------------------------------------------- */

	/**
	 * Finds all paths to the configurations XML files for themes, that are located in the theme folder.
	 *
	 * @return array
	 * 		The array containing all the relative path of the theme configuration XML file.
	 */
	protected function findAllThemesConfigPaths()
	{
		$themesConfigs = array();
		if (is_dir($this->themesFolder)) {
			if ($dh = opendir($this->themesFolder)) {
				while (($file = readdir($dh)) !== false) {
					if ($file != "." && $file != ".."){
						$filePath = $file.'/'.$this->themeConfigFileName;
						if(file_exists($this->themesFolder.$filePath)){
							$themesConfigs[] = $filePath;
						}
					}
				}
				closedir($dh);
			}
		}

		return $themesConfigs;
	}

	/* --------------------------------------------------------------- */

	/**
	 * Loads the provided theme configuration files.
	 * @param array $themesConfigs
	 * 		The array containing all the relative path of the theme configuration XML file, *(not null not empty).
	 * @return array
	 * 		The array containing all the loaded themes.
	 */
	protected function loadThemes($themesConfigs)
	{
		$themes = array();
		foreach ($themesConfigs as $id){
			$path = $this->themesFolder.$id;
			if(file_exists($path)){
				$themes[] = $this->loadTheme($this->loadXML($path), $id);
			}
		}
		return $themes;
	}

	/**
	 * Filters the provided array of themes based on the search.
	 * @param SearchTheme $search
	 * 		The search to filter the themes by, *(not null not empty).
	 * @param array $themes
	 * 		The array of Theme objects to be filtered, *(not null not empty).
	 * @return array
	 * 		The array containing all themes that respect the search.
	 */
	protected function filterThemes(SearchTheme $search, $themes)
	{
		//TODO: to implement also the actual filtering.
		$filtered = $themes;

		foreach ($search->getOrderedBy() as $column){
			if($column === $search->NAME){
				//TODO: continue implementation
				if($column->isOrderAscending()){
					usort($filtered, function($a, $b) {
						return strcmp($b->getName(), $a->getName());
					});
				} else {
					usort($filtered, function($a, $b) {
						return strcmp($a->getName(), $b->getName());
					});
				}
			}
			else if($column === $search->VERSION){
				usort($filtered, function($a, $b) {
					return strcmp($a->getVersion(), $b->getVersion());
				});
			}
		}

		return $filtered;
	}

	/* --------------------------------------------------------------- */

	/**
	 * Loads the theme object.
	 *
	 * @param \SimpleXMLElement $root
	 * 		The root Theme XML element, *(not null not empty).
	 * @param string $id
	 * 		The id of the loaded Theme, *(not null not empty).
	 *
	 * @return Newscoop\Entity\Theme
	 * 		The loaded theme object.
	 */
	protected function loadTheme(\SimpleXMLElement $root, $id)
	{
		if($root->getName() !== 'theme'){
			throw new \Exception("Invalid XML cannot locate the 'theme' root.");
		}

		$theme = new Theme();
		$theme->setId($id);
		$theme->setName($root['name']);
		$theme->setDesigner($root['designer']);
		$theme->setVersion($root['version']);
		$theme->setMinorNewscoopVersion($root['require']);
		$theme->setDescription($root->description->__toString());

		return $theme;
	}

	/**
	 * Loads the theme object.
	 *
	 * @param \SimpleXMLElement $root
	 * 		The root resource XML element, *(not null not empty).
	 * @param string $id
	 * 		The id of the loaded Resource, *(not null not empty).
	 * @param string $path
	 * 		The relative path where the images are located, *(not null not empty).
	 *
	 * @return Newscoop\Entity\Theme\Resource
	 * 		The loaded resource object.
	 */
	protected function loadResource(\SimpleXMLElement $root, $path)
	{
		$rsc = new Resource();
		$rsc->setId($path.$root['src']);
		$rsc->setName($root['name']);
		$rsc->setPath($path.$root['src']);

		return $rsc;
	}

	/* --------------------------------------------------------------- */

	/**
	 * Loads the XML content into SimpleXMLElement.
	 *
	 * @param string $path
	 * 		The path to the XML file, *(not null not empty).
	 *
	 * @return \SimpleXMLElement
	 * 		The XML element.
	 */
	protected function loadXML($path)
	{
		$xml = simplexml_load_file($path);
		if($xml === FALSE){
			throw new \Exception("Cannot obtain a valid XML for path '$path'.");
		}
		return $xml;
	}

	/* --------------------------------------------------------------- */

	/**
	 * Extracts from a provided file path the path where that file is located.
	 *
	 * @param string $filePath
	 * 		The file path from which to extract the relative path, *(not null not empty).
	 *
	 * @return string
	 * 		The relative path for the provided file path, not null.
	 */
	protected function extractRelativePathFrom($filePath)
	{
		$pos = strrpos($filePath, '/');
		if ($pos !== false) {
			return substr($filePath, 0, $pos + 1);
		}
		return '';
	}
}