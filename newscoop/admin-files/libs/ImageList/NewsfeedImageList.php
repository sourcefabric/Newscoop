<?php
/**
 * @package Campsite
 *
 * @author Martin Saturka <martin.saturka@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once dirname(__FILE__) . '/ImageList.php';

/**
 * Newsfeed Image list component
 */
class NewsfeedImageList extends ImageList
{
    /** @var array */
    protected $filters = array(
        "Source = 'newsfeed'",
    );

    /**
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see BaseList
     */
    public function doData()
    {
        $args = $this->getArgs();
        return parent::doData();
    }
}
