<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service;

use Newscoop\Entity\Theme;
use Newscoop\Service\IEntityService;

/**
 * Provides the services for the themes.
 */
interface IThemeService extends IEntityService
{

	/**
	 * Provides the class name as a constant. 
	 */
	const NAME = __CLASS__;
	
	/* --------------------------------------------------------------- */
	
	/**
	 * Provides the array of ThemeResources that contain the images that provide the theme presentation.
	 *
	 * @param Newscoop\Entity\Theme $theme
	 *		The theme to find the presentation resources for, not null.
	 *
	 * @return array
	 *		The array containing the Newscoop\Entity\Resource, not null might be empty.
	 */
	function getPresentationImages(Theme $theme);
}