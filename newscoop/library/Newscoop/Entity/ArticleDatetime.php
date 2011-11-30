<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
namespace Newscoop\Entity;
use Newscoop\Entity\Article;
/**
 * ArticleDatetime entity
 * @Entity( repositoryClass="Newscoop\Entity\Repository\ArticleDatetimeRepository" )
 * @Table( name="article_datetimes",
 * 	uniqueConstraints={ @UniqueConstraint(
 * 		name="search_idx",
 * 		columns={"end_date", "start_date", "end_time", "start_time", "article_id", "article_type", "field_name"}
 *  )})
 */
class ArticleDatetime extends Entity
{
	/**
	 * @Id
	 * @GeneratedValue
	 * @Column( type="integer", name="id_article_datetime" )
     * @var int
     */
    protected $id;

    /**
     * @Column( type="date", name="start_date" )
     * @var string
     */
    protected $startDate;

    /**
     * @Column( type="date", name="end_date", nullable="true" )
     * @var string
     */
    protected $endDate;

    /**
     * @Column( type="time", name="start_time", nullable="true" )
     * @var string
     */
    protected $startTime;

    /**
     * @Column( type="time", name="end_time", nullable="true" )
     * @var string
     */
    protected $endTime;

    /**
     * @Column( type="string", name="recurring", nullable="true" )
     * @var string
     */
    protected $recurring;

    /**
     * @Column( type="integer", name="article_id" )
     * @JoinColumn(name="article_id", referencedColumnName="id")
     * @var int
     */
    private $articleId;

    /**
     * @Column( type="string", name="article_type" )
     * @var string
     */
    protected $articleType;

    /**
     * @Column( type="string", name="field_name" )
     * @var string
     */
    protected $fieldName;

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
     * @param array $articleData
     * @param ArticleDatetime $dateData
     */
    public function setValues($dateData, $article, $fieldName, $articleType=null)
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
    }

    public function __get($name)
    {
        $getMethod = 'get'.preg_replace("`(?<=[a-z])(_([a-z]))`e","ucfirst(strtoupper('\\2'))",trim($name));
        if (!is_callable(array($this, $getMethod))) {
            return null;
        }
        return $this->$getMethod();
    }
}