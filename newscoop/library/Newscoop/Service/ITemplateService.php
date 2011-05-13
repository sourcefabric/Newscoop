<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service;


/**
 * Provides the template services.
 */
use Newscoop\Entity\Output\OutputSettingsTheme;

use Newscoop\Entity\Theme;

interface ITemplateService extends IEntityService
{

	/**
	 * Provides the class name as a constant. 
	 */
	const NAME = __CLASS__;
	
	/* --------------------------------------------------------------- */
	 
	function findThemeOutputSettings(Theme $theme);
	
	/* --------------------------------------------------------------- */
	
	function updateThemeOutputSettings(OutputSettingsTheme $settings);
	
}