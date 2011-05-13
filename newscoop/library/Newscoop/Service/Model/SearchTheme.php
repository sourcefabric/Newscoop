<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Model;

use Newscoop\Service\Model\Search\Search;
use Newscoop\Service\Model\Search\ColumnOrder;

/**
 * Provides the container for the search of Theme entities.
 */
class SearchTheme extends Search
{
	/**
	 * Provides the class name as a constant. 
	 */
	const NAME = __CLASS__;
	
	/* --------------------------------------------------------------- */
	
	/**
	 * (DO NOT CHANGE)
	 *
	 *  @var Newscoop\Service\Model\Search\ColumnOrder
	 **/
	public $NAME;

	/**
	 * (DO NOT CHANGE)
	 *
	 * @var Newscoop\Service\Model\Search\ColumnOrder
	 **/
	public $DESIGNER;

	/**
	 * (DO NOT CHANGE)
	 *
	 * @var Newscoop\Service\Model\Search\ColumnOrder
	 **/
	public $VERSION;

	/**
	 * (DO NOT CHANGE)
	 *
	 * @var Newscoop\Service\Model\Search\ColumnOrder
	 **/
	public $MINOR_NEWSCOOP_VERSION;


	/* --------------------------------------------------------------- */

	public function __construct() {
		$this->NAME =  new ColumnOrder($this);
		$this->DESIGNER = new ColumnOrder($this);
		$this->VERSION = new ColumnOrder($this);
		$this->MINOR_NEWSCOOP_VERSION = new ColumnOrder($this);
	}
}