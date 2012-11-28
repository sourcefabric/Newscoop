<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
namespace Newscoop\Entity;
use Newscoop\Entity\Article;

/**
 * Rating entity
 * @Entity( repositoryClass="Newscoop\Entity\Repository\RatingRepository" )
 * @Table( name="rating")
 */
class Rating extends Entity
{
	/**
	 * @id
	 * @generatedValue
	 * @column( type="integer", name="id" )
     * @var int
     */
    protected $id;

    /**
     * @column( type="integer", name="article_number" )
     * @var int
     */
    private $articleId;

    /**
     * @column( type="integer", name="user_id" )
     * @var int
     */
    private $userId;

    /**
     * @column( type="integer", name="rating_score" )
     * @var int
     */
    private $ratingScore;

    /**
     * @column(type="datetime", name="time_created" )
     * @var DateTime
     */
    private $timeCreated;

    /**
     * @return int
     */
    public function getArticleId()
    {
        return $this->articleId;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return int
     */
    public function getRatingScore()
    {
        return $this->ratingScore;
    }

    /**
     * @return DateTime
     */
    public function getTimeCreated()
    {
        return $this->timeCreated;
    }

    /**
     * Set timecreated
     *
     * @param DateTime $p_datetime
     * @return Newscoop\Entity\Rating
     */
    public function setTimeCreated(\DateTime $p_datetime)
    {
        $this->timeCreated = $p_datetime;
        // return this for chaining mechanism
        return $this;
    }

    public function setArticleId($articleId) {
        $this->articleId = $articleId;
        // return this for chaining mechanism
        return $this;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
        // return this for chaining mechanism
        return $this;
    }

    public function setRatingScore($ratingScore) {
        $this->ratingScore = $ratingScore;
        // return this for chaining mechanism
        return $this;
    }
}
