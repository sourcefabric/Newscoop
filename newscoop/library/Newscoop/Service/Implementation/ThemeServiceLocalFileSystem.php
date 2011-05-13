<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Implementation;

use Newscoop\Entity\Resource;
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
	 * Construct the service based on the provided resource id.
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
		Validation::notEmpty($id, 'id');
		$theme = $this->findById($id);
		if(is_null($theme)){
			throw new \Exception("There is no theme for id '$id'.");
		}
		return $theme;
	}

	function findById($id)
	{
		Validation::notEmpty($id, 'id');
		$themesConfigs = $this->findAllThemesConfigPaths();

		if($id > 0 && $id <= count($themesConfigs)){
			$rpath = $themesConfigs[$id - 1];
			$path = $this->themesFolder.$rpath;
			return $this->loadTheme($this->loadXML($path), $id, $rpath);
		}
		return NULL;
	}

	function getCount(Search $search = NULL)
	{
		$themesConfigs = $this->findAllThemesConfigPaths();
		if($search === NULL){
			return count($themesConfigs);
		}
		if(!($search instanceof SearchTheme)){
			throw new \Exception("The search needs to be a SearchTheme instance.");
		}
		$themes = $this->loadThemes($themesConfigs);
		$themes = $this->filterThemes($search, $themes);
		return count($themes);
	}

	function getEntities(Search $search = NULL, $offset = 0, $limit = -1)
	{
		$themes = $this->loadThemes($this->findAllThemesConfigPaths());
		if($search !== NULL){
			if(!($search instanceof SearchTheme)){
				throw new \Exception("The search needs to be a SearchTheme instance.");
			}
			$themes = $this->filterThemes($search, $themes);
		}
		
		return $this->trim($themes, $offset, $limit);
	}

	/* --------------------------------------------------------------- */

	function getPresentationImages(Theme $theme)
	{
		Validation::notEmpty($theme, 'theme');

		$xml = $this->loadXML($this->themesFolder.$theme->getPath().$this->themeConfigFileName);

		$presentResources = array();
		foreach ($xml->children() as $node){
			/* @var $node \SimpleXMLElement */
			if($node->getName() === 'presentation-img'){
				$presentResources[] = $this->loadResource($node, $theme->getPath());
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
				while (($dir = readdir($dh)) !== false) {
					$folder = $this->themesFolder.$dir;
					if ($dir != "." && $dir != ".." && is_dir($folder)){
						// Reading the subdirectories which contain the themes
						if($subDh = opendir($folder)){
							while (($file = readdir($subDh)) !== false) {
								if ($file != "." && $file != ".."){
									$filePath = $dir.DIR_SEP.$file.DIR_SEP.$this->themeConfigFileName;
									if(file_exists($this->themesFolder.$filePath)){
										$themesConfigs[] = $filePath;
									}
								}
							}
							closedir($subDh);
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
		$id = 1;
		foreach ($themesConfigs as $rpath){
			$path = $this->themesFolder.$rpath;
			if(file_exists($path)){
				$themes[] = $this->loadTheme($this->loadXML($path), $id++, $rpath);
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
				$filtered = $this->sort($filtered, 'getName', $column->isOrderAscending());
			}
			else if($column === $search->DESIGNER){
				$filtered = $this->sort($filtered, 'getDesigner', $column->isOrderAscending());
			}
			else if($column === $search->VERSION){
				$filtered = $this->sort($filtered, 'getVersion', $column->isOrderAscending());
			}
			else if($column === $search->MINOR_NEWSCOOP_VERSION){
				$filtered = $this->sort($filtered, 'getMinorNewscoopVersion', $column->isOrderAscending());
			}
		}

		return $filtered;
	}

	/**
	 * Sort the array.
	 *
	 * @param array $array
	 * 		The array of elements to be sorted.
	 * @param string $property
	 * 		The method name that provides the sorting key.
	 * @param bool $asscending
	 * 		True if the sort is ascending, false for descending.
	 * @return array
	 * 		The sorted array.
	 */
	protected function sort($array, $property, $asscending)
	{
		usort($array, function($a, $b) use ($property, $asscending) {
			if($asscending){
				return strcmp($a->$property(), $b->$property());
			} else {
				return strcmp($b->$property(), $a->$property());
			}
		});
		return $array;
	}

	/**
	 * Trims the array based on the offset and limit.
	 * 
	 * @param array $array
	 * 		The array to be trimed, not null.
	 * @param int $offset
	 * 		The offset.
	 * @param int $limit
	 * 		The limit.
	 * @return array
	 * 		The trimed array.
	 */
	protected function trim($array, $offset, $limit)
	{
		$array = array_slice($array, $offset);
		if($limit >= 0){
			$array = array_slice($array, 0, $limit);
		}
		return $array;
	}

	/* --------------------------------------------------------------- */

	/**
	 * Loads the theme object.
	 *
	 * @param \SimpleXMLElement $root
	 * 		The root Theme XML element, *(not null not empty).
	 * @param string $id
	 * 		The id of the loaded Theme, *(not null not empty).
	 * @param string $themeConfig
	 * 		The path of the Theme XML file in order to extract the theme path, *(not null not empty).
	 *
	 * @return Newscoop\Entity\Theme
	 * 		The loaded theme object.
	 */
	protected function loadTheme(\SimpleXMLElement $root, $id, $themeConfig)
	{
		if($root->getName() !== 'theme'){
			throw new \Exception("Invalid XML cannot locate the 'theme' root.");
		}

		$theme = new Theme();
		$theme->setId($id);
		$theme->setPath($this->extractRelativePathFrom($themeConfig));
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
	 * @return Newscoop\Entity\Resource
	 * 		The loaded resource object.
	 */
	protected function loadResource(\SimpleXMLElement $root, $path)
	{
		$rsc = new Resource();
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