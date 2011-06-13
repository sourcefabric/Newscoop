<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service;

use Newscoop\Service\Exception\DuplicateNameException;
use Newscoop\Service\Model\SearchTheme;
use Newscoop\Entity\OutputSettings;
use Newscoop\Entity\Output;
use Newscoop\Entity\Publication;
use Newscoop\Entity\Theme;

/**
 * Provides the management services for the themes.
 */
interface IThemeManagementService extends IThemeService
{

	/**
	 * Provides the class name as a constant.
	 */
	const NAME_1 = __CLASS__;

	/* --------------------------------------------------------------- */

	/**
	 * Provides the themes that are unassigned to any publication.
	 *
	 * @param Newscoop\Service\Model\Search\SearchTheme $search
	 *		The search criteria, not null.
	 *
	 * @param int|0 $offset
	 *	 	The offset from where to retrieve the entities, if an offset is specified
	 *		than also a limit must be specified in order for the offset to take effect.
	 *
	 * @param int $limit
	 *		The limit of entities to fetch, negaive value will fetch all entities found.
	 *
	 * @return array of Newscoop\Entity\Theme
	 * 		The array containing all the unassigned Themes, not null migh be empty.
	 */
	function getUnassignedThemes(SearchTheme $search = NULL, $offset = 0, $limit = -1);

	/**
	 * Provides the themes that are assigned to the publication.
	 *
	 * @param Publication|int $publication
	 * 		The publication to retrieve the themes for or the publication id, not null.
	 * 
	 * @param Newscoop\Service\Model\Search\SearchTheme $search
	 *		The search criteria, not null.
	 *
	 * @param int|0 $offset
	 *	 	The offset from where to retrieve the entities, if an offset is specified
	 *		than also a limit must be specified in order for the offset to take effect.
	 *
	 * @param int $limit
	 *		The limit of entities to fetch, negaive value will fetch all entities found.
	 *
	 * @return array of Newscoop\Entity\Theme
	 * 		The array containing all the Themes assigned to the provided publication, not null migh be empty.
	 */
	function getThemes($publication, SearchTheme $search = NULL, $offset = 0, $limit = -1);

	/**
	 * Provides the all template resources (.tpl) files found for the theme.
	 *
	 * @param Theme|string $theme
	 * 		The theme to retrieve the template resource for
	 * 		can be the Theme object or the theme path, not null.
	 *
	 * @return array of Newscoop\Entity\Resource
	 * 		The array containing all the template resources assigned to the provided theme, not null migh be empty.
	 */
	function getTemplates($theme);

	/**
	 * Provides the output setting for the provided theme and ouput.
	 *
	 * @param Theme $theme
	 * 		The theme to retrieve the output setting for, not null.
	 * @param Output $output
	 * 		The output, not null.
	 *
	 * @return array of Newscoop\Entity\OutputSettings
	 * 		The output setting, null if none found for the theme and output.
	 */
	function findOutputSetting(Theme $theme, Output $output);

	/**
	 * Provides the all output setting found for the theme.
	 *
	 * @param Theme $theme
	 * 		The theme to retrieve the output setting  for, not null.
	 *
	 * @return array of Newscoop\Entity\OutputSettings
	 * 		The array containing all the OutputSettings assigned to the provided theme, not null migh be empty.
	 */
	function getOutputSettings(Theme $theme);

	/* --------------------------------------------------------------- */

	/**
	 * Export the theme.
	 *
	 * @param Theme|int $theme
	 * 		The theme or theme id to be exported, not null.
	 * @return the file name containing the exported archive.
	 */
	function exportTheme($theme);
	
	/**
	 * Updates the theme.
	 *
	 * @param str $filePath
	 * 		The file path to the zip containing the theme, not null.
	 * @return bool
	 * 		True if the theme was succesfully added, false otherwise.
	 */
	function installTheme($filePath);
	
	/**
	 * Updates the theme.
	 *
	 * @param Theme $theme
	 * 		The theme to be updated, not null.
	 *
	 * @throws DuplicateNameException
	 * 		Thrown when the theme has the same name as another theme in the same scope (for instance 2 themes
	 * 		belong to the same publication and you try to rename 1 theme to have the same name as the other).
	 */
	function updateTheme(Theme $theme);

	/**
	 * Delete the theme and all coresponding connections. Please check the isUsedTheme method from
	 * IOutputSettingIssueService before removing the theme.
	 *
	 * @param Theme|int $theme
	 * 		The theme or theme id to be deleted, not null.
	 *
	 * @return bool
	 * 		TRUE if the theme was succesfully deleted, FLASE if the theme is in use and cannot be removed.
	 */
	function removeTheme($theme);

	/**
	 * Assign the theme to the publication.
	 *
	 * @param Theme $theme
	 * 		The theme to be assigned, not null.
	 *
	 * @param Publication $publication
	 * 		The publication to be assigned the theme for, not null.
	 *
	 * @throws DuplicateNameException
	 * 		Thrown when there is already another theme with the same name under the publication.
	 */
	function assignTheme(Theme $theme, Publication $publication);

	/**
	 * Assign the output settings to the theme.
	 *
	 * @param OutputSettings $outputSettings
	 * 		The output settings to be assigned to the theme.
	 * @param Theme $theme
	 * 		The theme to be assigned to, not null.
	 */
	function assignOutputSetting(OutputSettings $outputSettings, Theme $theme);

	/**
	 * Assign the article types to the theme.
	 *
	 * @param object|array $outputSettings
	 * 		The article types to be assigned to the theme.
	 * @param Theme $theme
	 * 		The theme to be assigned to, not null.
	 */
	function assignArticleTypes($articleTypes, Theme $theme);

}