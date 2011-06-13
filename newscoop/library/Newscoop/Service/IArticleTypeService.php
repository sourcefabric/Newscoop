<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service;

use Newscoop\Entity\ArticleType;

use Newscoop\Service\IEntityService;

/**
 * Provides the services for the Publications.
 */
interface IArticleTypeService
{
	/**
	 * Provides the class name as a constant. 
	 */
	const NAME = __CLASS__;
	
	/* --------------------------------------------------------------- */
	
	/**
	 * Find all article types, which you can get fields for later on
	 * 
	 * @return array most probably consisting of \Newscroop\Entity\ArticleType instances
	 */
	public function findAllTypes();

	/**
	 * Find one article type by name
	 * 
	 * @param string $name
	 * @return ArticleType|null
	 * 		returns null if not found!
	 */
	public function findTypeByName( $name );
	
	/**
	 * Get all fields per article type
	 * 
	 * @param ArticleType $type
	 * @return array
	 */
	public function findFields( ArticleType $type );
	
}