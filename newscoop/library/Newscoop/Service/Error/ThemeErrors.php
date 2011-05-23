<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Error;


/**
 * Provides the errors keys as constants for the themes.
 */
class ThemeErrors
{
	
	const XML_INVALID = 'Invalid XML theme config file $1.';	
	const XML_NO_ROOT = 'Invalid XML cannot locate the theme root.';
	const XML_MISSING_ATTRIBUTE = 'Missing attribute $1 for node $2.';
	const XML_MISSING_TAG = 'Missing tag with name $1 from parent tag $2.';
	const XML_TO_MANY_TAGS = 'To many tags for name $1 from parent tag $2, expected $3.';
	
	const OUTPUT_MISSING = 'Missing output with name $1 from the database.';

	/* --------------------------------------------------------------- */

	private function __construct(){}
}