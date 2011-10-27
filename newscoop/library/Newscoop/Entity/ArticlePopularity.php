<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\Common\Collections\ArrayCollection,
    Newscoop\Utils\Validation;

/**
 * @Entity(repositoryClass="Newscoop\Entity\Repository\ArticlePopularityRepository")
 * @Table(name="article_popularity")
 */
class ArticlePopularity
{
    /**
     * @Id
     * @Column(type="integer", name="fk_article_id")
     * @var int
     */
    private $article_id;

    /**
     * @Id
     * @Column(type="integer", name="fk_language_id")
     * @var int
     */
    private $language_id;

    /**
     * @Column(type="string",unique=true)
     * @var string
     */
    private $url;

    /**
     * @Column(type="datetime")
     * @var DateTime
     */
    private $date;

    /**
     * @Column(type="integer")
     * @var int
     */
    private $unique_views;

    /**
     * @Column(type="decimal")
     * @var float
     */
    private $avg_time_on_page;

    /**
     * @Column(type="integer")
     * @var int
     */
    private $tweets;

    /**
     * @Column(type="integer")
     * @var int
     */
    private $likes;

    /**
     * @Column(type="integer")
     * @var int
     */
    private $comments;

    /**
     * @Column(type="decimal")
     * @var float
     */
    private $popularity;


    /**
     * Get article id
     *
     * @return int
     */
    public function getArticleId()
    {
        return (int) $this->article_id;
    }

    /**
     * Set article id
     *
     * @param int $id
     * @return Newscoop\Entity\ArticlePopularity
     */
    public function setArticleId($id)
    {
        $this->article_id = (int) $id;
        return $this;
    }

   /**
    * Get language id
    *
    * @return int
    */
   public function getLanguageId()
   {
       return (int) $this->language_id;
   }

   /**
    * Set language id
    *
    * @param int $id
    * @return Newscoop\Entity\ArticlePopularity
    */
    public function setLanguageId($id)
    {
        $this->language_id = (int) $id;
        return $this;
    }

    /**
     * Get page URL
     *
     * @return string
     */
    public function getURL()
    {
        return (string) $this->url;
    }

    /**
     * Set the url
     *
     * @param string $url
     * @return Newscoop\Entity\ArticlePopularity
     */
    public function setURL($url)
    {
        $this->url = (string) $url;
        return $this;
    }

    /**
     * Get date
     *
     * @return DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date
     *
     * @param DateTime $date
     * @return Newscoop\Entity\ArticlePopularity
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get unique views
     *
     * @return int
     */
    public function getUniqueViews()
    {
        return (int) $this->unique_views;
    }

    /**
     * Set unique views
     *
     * @param int $views
     * @return Newscoop\Entity\ArticlePopularity
     */
    public function setUniqueViews($views)
    {
        $this->unique_views = (int) $views;
        return $this;
    }

    /**
     * Get average time on page
     *
     * @return float
     */
    public function getAvgTimeOnPage()
    {
        return (float) $this->avg_time_on_page;
    }

    /**
     * Set average time on page
     *
     * @param float $time
     * @return Newscoop\Entity\ArticlePopularity
     */
    public function setAvgTimeOnPage($time)
    {
        $this->avg_time_on_page = (int) $time;
        return $this;
    }

    /**
     * Get tweets
     *
     * @return int
     */
    public function getTweets()
    {
        return (int) $this->tweets;
    }

    /**
     * Set tweets
     *
     * @param int $tweets
     * @return Newscoop\Entity\ArticlePopularity
     */
    public function setTweets($tweets)
    {
        $this->tweets = (int) $tweets;
        return $this;
    }

    /**
     * Get Likes
     *
     * @return int
     */
    public function getLikes()
    {
        return (int) $this->likes;
    }

    /**
     * Set likes
     *
     * @param int $likes
     * @return Newscoop\Entity\ArticlePopularity
     */
    public function setLikes($likes)
    {
        $this->likes = (int) $likes;
        return $this;
    }

    /**
     * Get number of comments
     *
     * return int
     */
    public function getComments()
    {
        return (int) $this->comments;
    }

    /**
     * Set number of comments
     *
     * @param int $comments
     * @return Newscoop\Entity\ArticlePopularity
     */
    public function setComments($comments)
    {
        $this->comments = (int) $comments;
        return $this;
    }

    /**
     * Get point value of action
     *
     * @return float
     */
    public function getPopularity()
    {
        return (float) $this->popularity;
    }

    /**
     * Set popularity value
     *
     * @param float $points
     * @return Newscoop\Entity\ArticlePopularity
     */
    public function setPopularity($points)
    {
        $this->popularity = (float) $points;
        return $this;
    }
}
