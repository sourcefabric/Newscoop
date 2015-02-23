<?php
/**
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @package Newscoop
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Criteria;

use Newscoop\Criteria;
use Symfony\Component\HttpFoundation\Request;

/**
 * Available criteria for slideshows listing.
 */
class ArticleSearchCriteria extends Criteria
{
    /**
    * @var string
    */
    public $query;

    /**
    * @var string
    */
    public $publication;

    /**
    * @var string
    */
    public $issue;

    /**
    * @var string
    */
    public $section;
    
    /**
    * @var string
    */
    public $language;

    /**
    * @var string
    */
    public $article_type;

    /**
    * @var datetime
    */
    public $publish_date;

    /**
    * @var datetime
    */
    public $published_after;

    /**
    * @var datetime
    */
    public $published_before;

    /**
    * @var int
    */
    public $author;

    /**
    * @var int
    */
    public $creator;

    /**
     * @var string
     */
    public $status;

    /**
     * @var int
     */
    public $topic;

    /**
     * @var array
     */
    public $orderBy = array('id' => 'desc');

    public function fillFromRequest(Request $request) {
        foreach($this as $key => $value) {
            $this->$key = $request->get($key, false);
        }
    }
}
