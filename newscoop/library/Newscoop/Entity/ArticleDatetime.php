<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;
use Newscoop\Entity\Article;

/**
 * ArticleDatetime entity
 * @ORM\Entity( repositoryClass="Newscoop\Entity\Repository\ArticleDatetimeRepository" )
 * @ORM\Table( name="article_datetimes",
 * 	uniqueConstraints={ @ORM\UniqueConstraint(
 * 		name="search_idx",
 * 		columns={"end_date", "start_date", "end_time", "start_time", "article_id", "article_type", "field_name"}
 *  )})
 */
class ArticleDatetime extends AbstractEntity
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column( type="integer", name="id_article_datetime" )
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column( type="date", name="start_date" )
     * @var string
     */
    protected $startDate;

    /**
     * @ORM\Column( type="date", name="end_date", nullable=True )
     * @var string
     */
    protected $endDate;

    /**
     * @ORM\Column( type="time", name="start_time", nullable=True )
     * @var string
     */
    protected $startTime;

    /**
     * @ORM\Column( type="time", name="end_time", nullable=True )
     * @var string
     */
    protected $endTime;

    /**
     * @ORM\Column( type="string", name="recurring", nullable=True )
     * @var string
     */
    protected $recurring;

    /**
     * @ORM\Column( type="integer", name="article_id" )
     * @ORM\JoinColumn(name="article_id", referencedColumnName="id")
     * @var int
     */
    protected $articleId;

    /**
     * @ORM\Column( type="string", name="article_type" )
     * @var string
     */
    protected $articleType;

    /**
     * @ORM\Column( type="string", name="field_name" )
     * @var string
     */
    protected $fieldName;

    /**
     * @ORM\Column( type="string", name="event_comment" )
     * @var string
     */
    protected $eventComment;

    /**
     * @return int
     */
    public function getArticleId()
    {
        return $this->articleId;
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @return string
     */
    public function getEventComment()
    {
        return $this->eventComment;
    }

	/**
     * @return string
     */
    public function getArticleType()
    {
        return $this->articleType;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function getEndtime()
    {
        return $this->endTime;
    }

    public function getRecurring()
    {
        return $this->recurring;
    }

    /**
     * @param array $article
     * @param ArticleDatetime $dateData
     */
    public function setValues($dateData, $article, $fieldName, $articleType=null, $otherInfo=null)
    {
        if (is_null($articleType) && !($article instanceof Article)) {
            return false;
        }
        $this->articleId = $article instanceof Article ? $article->getId() :  $article;
        $this->articleType = is_null($articleType) ? $article->getType() : $articleType;
        $this->fieldName = $fieldName;
        $this->startDate = $dateData->getStartDate();
        $this->endDate = $dateData->getEndDate();
        $this->startTime = $dateData->getStartTime();
        $this->endTime = $dateData->getEndTime();
        $this->recurring = $dateData->getRecurring();

        $this->eventComment = null;
        if ( (is_array($otherInfo)) && isset($otherInfo['eventComment']) ) {
            $this->eventComment = $otherInfo['eventComment'];
        }
    }

    public function __get($name)
    {
        $getMethod = 'get'.preg_replace("`(?<=[a-z])(_([a-z]))`e","ucfirst(strtoupper('\\2'))",trim($name));
        if (!is_callable(array($this, $getMethod))) {
            return null;
        }
        return $this->$getMethod();
    }
    
    public function setStartDate($startDate) {
        $this->startDate = $startDate;
    }
    public function setStartTime($startTime) {
        $this->startTime = $startTime;
    }
    public function setEndDate($endDate) {
        $this->endDate = $endDate;
    }
    public function setEndTime($endTime) {
        $this->endTime = $endTime;
    }
    public function setRecurring($recurring) {
        $this->recurring = $recurring;
    }
    public function setArticleId($articleId) {
        $this->articleId = $articleId;
    }
    public function setArticleType($articleType) {
        $this->articleType = $articleType;
    }
    public function setFieldName($fieldName) {
        $this->fieldName = $fieldName;
    }
    public function setEventComment($eventComment) {
        $this->eventComment = $eventComment;
    }
}
