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
 * @Entity(repositoryClass="Newscoop\Entity\Repository\RatingRepository")
 * @Table(name="rating")
 */
class Rating extends Entity
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer", name="id")
     * @Var int
     */
    protected $id;

    /**
     * @Column(type="integer", name="article_number")
     * @Var int
     */
    private $articleId;

    /**
     * @Column(type="integer", name="user_id")
     * @Var int
     */
    private $userId;

    /**
     * @Column(type="integer", name="rating_score")
     * @Var int
     */
    private $ratingScore;

    /**
     * @Column(type="datetime", name="time_created")
     * @Var DateTime
     */
    private $timeCreated;

    /**
     * @Column(type="datetime", name="time_updated")
     * @Var DateTime
     */
    private $timeUpdated;

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
     * @return DateTime
     */
    public function getTimeUpdated()
    {
        return $this->timeUpdated;
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
        return $this;
    }

    /**
     * Set timeupdated
     *
     * @param DateTime $p_datetime
     * @return Newscoop\Entity\Rating
     */
    public function setTimeUpdated(\DateTime $p_datetime)
    {
        $this->timeUpdated = $p_datetime;
        return $this;
    }

    /**
     * Set articleId
     *
     * @param int $articleId
     * @return Newscoop\Entity\Rating
     */
    public function setArticleId($articleId)
    {
        $this->articleId = $articleId;
        return $this;
    }

    /**
     * Set userId
     *
     * @param int $userId
     * @return Newscoop\Entity\Rating
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * Set ratingScore
     *
     * @param int $ratingScore
     * @return Newscoop\Entity\Rating
     */
    public function setRatingScore($ratingScore)
    {
        $this->ratingScore = $ratingScore;
        return $this;
    }
}
