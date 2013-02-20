<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Subscription;

use Newscoop\Subscription\Subscription;
use Newscoop\Entity\Article;
use Newscoop\Entity\Section;

/**
 * Subscription Data holder
 */
class SubscriptionData
{   
    /**
     * Subscription Class
     * @var Subscription
     */
    public $subscription;

    /**
     * Array of SubscriptionSections 
     * @var array
     */
    public $sections = array();

    /**
     * Array of SubscriptionArticles
     * @var array
     */
    public $articles = array();

    /**
     * User id
     * @var int
     */
    public $userId;

    /**
     * Publication Id
     * @var int
     */
    public $publicationId;

    /**
     * To pay value
     * @var decimal
     */
    public $toPay;

    /**
     * Subscription start date
     * @var \DateTime
     */
    public $startDate;
    
    /**
     * How long subscription should be valid
     * @var int
     */
    public $days;

    /**
     * How long subscription will be valid
     * @var int
     */
    public $paidDays;

    /**
     * Currency
     * @var string
     */
    public $currency;

    /**
     * Subscription status 
     * @var boolean
     */
    public $active;

    /**
     * Subscription type.
     * 'T' for Trial subscription, 'P' for paid subscription or 'PN' for paid now subscriptions.
     * 
     * @var string
     */
    public $type = 'P';

    public function __construct(array $data, Subscription $subscription = null) {
        // process data array
        $this->startDate = new \DateTime();
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

        // fill paidDays with days value
        if (!$this->paidDays) {
            $this->paidDays = $this->days;
        }

        if (!$subscription) {
            $this->subscription = new Subscription();
        } else {
            $this->subscription = $subscription;
        }

        return $this;
    }

    public function addSection(Section $section, $language) {
        $section = new \Newscoop\Subscription\Section($this->subscription, $section->getNumber());
        $section->setStartDate($this->startDate);
        $section->setDays($this->days);
        $section->setPaidDays($this->paidDays);
        $section->setLanguage($language);

        $this->sections[$section->getId()] = $section;
    }

    public function addArticle(Article $article, $language) {
        $article = new \Newscoop\Subscription\Article($this->subscription, $article);
        $article->setStartDate($this->startDate);
        $article->setDays($this->days);
        $article->setPaidDays($this->paidDays);
        $article->setLanguage($language);

        $this->articles[$article->getArticleNumber()] = $article;
    }
}