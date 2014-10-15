<?php
/**
 * @package Newscoop\CommunityTickerBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\CommunityTickerBundle\TemplateList;

use Newscoop\Criteria;

/**
 * Available criteria for community feeds listing.
 */
class ListCriteria extends Criteria
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $length;

    /**
     * @var string
     */
    public $event;

    /**
     * @var array
     */
    public $created = array('created' => 'asc');
}