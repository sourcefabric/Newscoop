<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Implementation;

use Newscoop\Service\Implementation\Exception\XMLMissingAttribueException;
use Newscoop\Service\Error\ThemeErrors;
use Newscoop\Service\IErrorHandler;
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

	const TAG_THEME = 'theme';
	const ATTR_THEME_NAME = 'name';
	const ATTR_THEME_DESIGNER = 'designer';
	const ATTR_THEME_VERSION = 'version';
	const ATTR_THEME_NEWSCOOP_VERSION = 'require';

	const TAG_DESCRIPTION = 'description';

	const TAG_PRESENT = 'presentation-img';
	const ATTR_PRESENT_NAME = 'name';
	const ATTR_PRESENT_SRC = 'src';

	/* --------------------------------------------------------------- */

	/** @var Newscoop\Service\Resource\ResourceId */
	private $id;
	/** @var Newscoop\Service\IErrorHandler */
	private $errorHandler = NULL;

	/* ------------------------------- */

	/** @var array */
	protected $cacheThemeConfigs = NULL;
	/** @var array */
	protected $cacheXMLEmelemt = array();

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
		$rpath = $themesConfigs[$id];
		if(isset($rpath)){
			$path = $this->toFullPath($rpath);
			$xml = $this->loadXML($path);
			if($xml != NULL){
				return $this->loadTheme($xml, $id, $rpath);
			}
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
		Validation::notEmpty($theme, self::TAG_THEME);

		$xml = $this->loadXML($this->toFullPath($theme, $this->themeConfigFileName));

		$presentResources = array();
		if($xml != NULL){
			$nodes = $this->getNodes($xml, self::TAG_PRESENT);
			foreach ($nodes as $node){
				/* @var $node \SimpleXMLElement */
				$rsc = new Resource();
				try{
					$rsc->setName($this->readAttribute($node, self::ATTR_PRESENT_NAME));
					$rsc->setPath($theme->getPath().$this->readAttribute($node, self::ATTR_PRESENT_SRC));
					$presentResources[] = $rsc;
				}catch(XMLMissingAttribueException $e){
					$this->getErrorHandler()->error(ThemeErrors::XML_MISSING_ATTRIBUTE, $e->getAttributeName(), $node->getName());
				}
			}
		}

		return $presentResources;
	}

	/* --------------------------------------------------------------- */

	/**
	 * Provides the resource id.
	 *
	 * @return Newscoop\Services\Resource\ResourceId
	 *		The resource id.
	 */
	protected function getResourceId()
	{
		return $this->id;
	}

	/**
	 * Provides the error handler.
	 *
	 * @return Newscoop\Service\IErrorHandler
	 *		The error handler to be used.
	 */
	protected function getErrorHandler()
	{
		if ($this->errorHandler === NULL) {
			$this->errorHandler = $this->getResourceId()->getService(IErrorHandler::NAME);
		}
		return $this->errorHandler;
	}

	/* --------------------------------------------------------------- */


	/**
	 * Finds all paths to the configurations XML files for themes, that are located in the theme folder.
	 *
	 * @return array
	 * 		The array containing as key the id of the theme config and as a value the relative
	 * 		path of the theme configuration XML file in escaped form.
	 * 		The id of a theme is formed based on the publication path with the crec32 applied.
	 */
	protected function findAllThemesConfigPaths()
	{
		if($this->cacheThemeConfigs === NULL){
			$this->cacheThemeConfigs = array();
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
											$escapedPath = $this->escapePath($filePath);
											$this->cacheThemeConfigs[crc32($escapedPath)] = $escapedPath;
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
		}
		return $this->cacheThemeConfigs;
	}

	/* --------------------------------------------------------------- */

	/**
	 * Filters the provided array of themes based on the search.
	 * @param SearchTheme $search
	 * 		The search to filter the themes by, *(not null not empty).
	 * @param array $themes
	 * 		The array of Theme objects to be filtered, *(not null not empty).
	 * @return array
	 * 		The array containing all themes that respect the search.
	 */
	protected function filterThemes(SearchTheme $search, array $themes)
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
	protected function trim(array $array, $offset, $limit)
	{
		$array = array_slice($array, $offset);
		if($limit >= 0){
			$array = array_slice($array, 0, $limit);
		}
		return $array;
	}

	/* --------------------------------------------------------------- */

	/**
	 * Loads the provided theme configuration files.
	 *
	 * @param array $themesConfigs
	 * 		The array containing as key the ide of the theme config (index) and as a value the relative
	 * 		path of the theme configuration XML file, *(not null not empty).
	 * @return array
	 * 		The array containing all the loaded themes.
	 */
	protected function loadThemes(array $themesConfigs)
	{
		$themes = array();
		foreach ($themesConfigs as $id => $rpath){
			$path = $this->toFullPath($rpath);
			if(file_exists($path)){
				$xml = $this->loadXML($path);
				if($xml != NULL){
					$theme = $this->loadTheme($xml, $id, $rpath);
					if($theme != NULL){
						$themes[] = $theme;
					}
				}
			}
		}
		return $themes;
	}

	/**
	 * Load the theme from the provided relative path.
	 *
	 * @param str $themePath
	 * 		The theme relative path.
	 * @return Theme
	 * 		The loaded theme.
	 */
	protected function loadThemeByPath($themePath)
	{
		$themePath .= $this->themeConfigFileName;
		foreach ($this->findAllThemesConfigPaths() as $id => $rpath){
			if($themePath == $rpath){
				$path = $this->toFullPath($rpath);
				if(file_exists($path)){
					$xml = $this->loadXML($path);
					if($xml != NULL){
						$theme = $this->loadTheme($xml, $id, $rpath);
						if($theme != NULL){
							return $theme;
						}
					}
				}
				return null;
			}
		}
		return null;
	}

	/* --------------------------------------------------------------- */

	/**
	 * Loads the XML content into SimpleXMLElement.
	 *
	 * @param string $path
	 * 		The path to the XML file, *(not null not empty).
	 *
	 * @return \SimpleXMLElement
	 * 		The XML element, NULL if the XML is not valid.
	 */
	protected function loadXML($path)
	{
		if(!isset($this->cacheXMLEmelemt[$path])){
			try{
				$xml = simplexml_load_file($path);
			}catch (\Exception $e){
				$xml = FALSE;
			}
			if($xml === FALSE){
				$this->getErrorHandler()->error(ThemeErrors::XML_INVALID, $path);
				return NULL;
			}
			$this->cacheXMLEmelemt[$path] = $xml;

		} else {
			$xml = $this->cacheXMLEmelemt[$path];
		}
		return $xml;
	}

	/**
	 * Loads the theme object.
	 *
	 * @param \SimpleXMLElement $nodeTheme
	 * 		The node Theme XML element, *(not null not empty).
	 * @param string $id
	 * 		The id of the loaded Theme, *(not null not empty).
	 * @param string $themeConfig
	 * 		The path of the Theme XML file in order to extract the theme path, *(not null not empty).
	 *
	 * @return Newscoop\Entity\Theme
	 * 		The loaded theme object, NULL if there was an issue.
	 */
	protected function loadTheme(\SimpleXMLElement $nodeTheme, $id, $themeConfig)
	{
		if($nodeTheme->getName() !== self::TAG_THEME){
			$this->getErrorHandler()->error(ThemeErrors::XML_NO_ROOT);
			return NULL;
		}

		$theme = new Theme();
		$theme->setId($id);
		$theme->setPath($this->extractRelativePathFrom($themeConfig));

		try{
			$theme->setName($this->readAttribute($nodeTheme, self::ATTR_THEME_NAME));
			$theme->setDesigner($this->readAttribute($nodeTheme, self::ATTR_THEME_DESIGNER));
			$theme->setVersion($this->readAttribute($nodeTheme, self::ATTR_THEME_VERSION));
			$theme->setMinorNewscoopVersion($this->readAttribute($nodeTheme, self::ATTR_THEME_NEWSCOOP_VERSION));
			$theme->setDescription($nodeTheme->{self::TAG_DESCRIPTION}->__toString());
		}catch(XMLMissingAttribueException $e){
			$this->getErrorHandler()->error(ThemeErrors::XML_MISSING_ATTRIBUTE, $e->getAttributeName(), $nodeTheme->getName());
			return NULL;
		}

		return $theme;
	}

	/**
	 * Convienient method for read an attribute from a node.
	 * This method will throw an exception in case the attribute is not specified.
	 *
	 * @param \SimpleXMLElement $node
	 * 		The node to read from, *(not null not empty).
	 * @param string $attribute
	 * 		The attribute name, *(not null not empty).
	 *
	 * @return string
	 * 		The attribute value, not null.
	 * @throws XMLMissingAttribueException
	 * 		In case of no value specified for the attribute.
	 */
	protected function readAttribute(\SimpleXMLElement $node, $attribute)
	{
		$value = $node[$attribute];
		if(!isset($value)){
			throw new XMLMissingAttribueException($attribute);
		}
		return $value;
	}

	/**
	 * Finds all the childrens from the provided node that have the provided tag name
	 *
	 * @param \SimpleXMLElement $node
	 * 		The node in which to search the childrens, not null.
	 * @param string $tagName
	 * 		The tag name for the childrens to find, not null.
	 * @param string $attribute
	 * 		Optional attribute name to search the kids by, beside the tag name.
	 * @param $value
	 * 		Optional but if $attribute is specified than specify also the value of the attribute to find by.
	 * @return array
	 * 		an array containing all the found nodes, not null can be empty.
	 */
	protected function getNodes(\SimpleXMLElement $node, $tagName, $attribute = NULL, $value = '')
	{
		$found = array();
		foreach ($node->children() as $kid){
			/* @var $kid \SimpleXMLElement */
			if($kid->getName() === $tagName){
				if($attribute != NULL){
					if($kid[$attribute] == $value){
						$found[] = $kid;
					}
				}else{
					$found[] = $kid;
				}
			}
		}
		return $found;
	}

	/* --------------------------------------------------------------- */

	/**
	 * Converts the provided path from the OS specific form (if is the cae) to using just forward slashes.
	 *
	 * @param string $path
	 * 		The path, *(not null not empty).
	 * @return string
	 * 		The escaped path.
	 */
	protected function escapePath($path)
	{
		return str_replace(DIR_SEP, '/', $path);
	}

	/**
	 * Extracts from a provided file path the path where that file is located, attention the path needs
	 * to be in the escaped form.
	 *
	 * @param string $path
	 * 		The path from which to extract the relative path, *(not null not empty).
	 *
	 * @return string
	 * 		The relative path for the provided path, not null.
	 */
	protected function extractRelativePathFrom($path)
	{
		$pos = strrpos($path, '/');
		if ($pos !== false) {
			return substr($path, 0, $pos + 1);
		}
		return '';
	}

	/**
	 * Provides the full path for a theme.
	 *
	 * @param Theme|string $theme
	 * 		The Theme or path, *(not null not empty).
	 * @param string $file
	 * 		Optional a file to be appended to the path.
	 * @return string
	 * 		The full path to the theme and file if is the case.
	 */
	public function toFullPath($theme, $file = '')
	{
		if($theme instanceof Theme){
			return $this->themesFolder.$theme->getPath().$file;
		}
		return $this->themesFolder.$theme.$file;
	}
}
