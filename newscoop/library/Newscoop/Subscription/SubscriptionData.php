<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Subscription;

/**
 * Subscription Data holder
 */
class SubscriptionData
{
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
     * Currency
     * @var string
     */
    public $currency;

    /**
     * Subscription type.
     * 'T' for Trial subscription or 'P' for paid subscription.
     * 
     * @var string
     */
    public $type = 'P';

    public function __construct(array $data) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

        return $this;
    }
}