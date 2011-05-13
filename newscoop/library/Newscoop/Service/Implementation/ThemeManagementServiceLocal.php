<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Implementation;

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
	 * Provides the prefix fodler name for the folders that containthemes for a publication.
	 */
	const FOLDER_PUBLICATION_PREFIX = 'publication_';

	/**
	 * Provides the template extension.
	 */
	const FILE_TEMPLATE_EXTENSION = 'tpl';

	/* --------------------------------------------------------------- */

	/** @var Newscoop\Service\Resource\ResourceId */
	private $id;

	/* ------------------------------- */

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

	function getUnassignedThemes(SearchTheme $search = NULL, $offset = 0, $limit = -1)
	{
		$allConfigs = $this->findAllThemesConfigPaths();
		$configs = array();

		$length = strlen(self::FOLDER_UNASSIGNED);
		foreach ($allConfigs as $config){
			if(strncmp($config, self::FOLDER_UNASSIGNED, $length) == 0){
				$configs[] = $config;
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
		foreach ($allConfigs as $config){
			if(strncmp($config, $pubFolder, $length) == 0){
				$configs[] = $config;
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

	}

	function getOutputSettings(Theme $theme)
	{

	}

	/* --------------------------------------------------------------- */

	function updateTheme(Theme $theme)
	{
		//TODO: implement
	}

	function assignTheme(Theme $theme, Publication $publication)
	{
		//TODO: implement
	}

	function assignOutputSetting(OutputSettings $outputSettings, Theme $theme)
	{
		//TODO: implement
	}

}