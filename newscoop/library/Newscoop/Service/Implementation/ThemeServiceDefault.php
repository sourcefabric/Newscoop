<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Implementation;

use Doctrine\ORM\EntityRepository;

use Newscoop\Service\IThemeService;
use Newscoop\Service\Resource\ResourceId;

/**
 * Provides the services implementation for the themes.
 */
class ThemeServiceDefault extends EntityRepository implements IThemeService
{

	/** @var Newscoop\Service\Resource\ResourceId */
	private $id;

	function __construct(ResourceId $id)
	{
		$this->id = $id;
	}

	/* --------------------------------------------------------------- */
	
	public function getMsg(){
		return $this->id->getFullId();
	}
}