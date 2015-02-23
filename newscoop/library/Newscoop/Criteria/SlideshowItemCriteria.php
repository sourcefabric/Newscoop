<?php
/**
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Criteria;

use Newscoop\Criteria;

/**
 * Available criteria for slideshows listing.
 */
class SlideshowItemCriteria extends Criteria
{
    /**
    * @var int
    */
    public $id;

    /**
     * @var int
     */
    public $slideshow;

    /**
     * @var string
     */
    public $type;

    /**
     * @var array
     */
    public $orderBy = array('offset' => 'asc');
}
