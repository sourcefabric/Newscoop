<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Model;

use Newscoop\Service\Model\Search\Search;
use Newscoop\Service\Model\Search\ColumnOrderLike;

/**
 * Provides the container for the search of Resource entities.
 */
class SearchResource extends Search
{

	/**
	 * Provides the class name as a constant. 
	 */
	const NAME = __CLASS__;
	
	/* --------------------------------------------------------------- */
	
	/**
	 * (DO NOT CHANGE)
	 *
	 *  @var Newscoop\Service\Model\Search\ColumnOrderLike
	 **/
	public $NAME;
	
	/**
	 * (DO NOT CHANGE)
	 *
	 *  @var Newscoop\Service\Model\Search\ColumnOrderLike
	 **/
	public $PATH;

	/* --------------------------------------------------------------- */

	public function __construct() {
		$this->NAME =  new ColumnOrderLike($this);
		$this->PATH =  new ColumnOrderLike($this);
	}
}