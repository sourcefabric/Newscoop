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
class SlideshowCriteria extends Criteria
{
    /**
    * @var int
    */
    public $id;

    /**
    * @var string
    */
    public $headline;

    /**
    * @var string
    */
    public $description;

    /**
    * @var string
    */
    public $rendition;

    /**
    * @var string
    */
    public $slug;

    /**
    * @var int
    */
    public $itemsCount;

    /**
     * Custom handled
     * @var integer
     */
    public $publication;

    /**
     * @var array
     */
    public $orderBy = array('id' => 'desc');
}
