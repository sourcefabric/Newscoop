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
    private $sections = array();

    /**
     * Array of SubscriptionArticles
     * @var array
     */
    private $articles = array();

    /**
     * User id
     * @var int
     */
    private $userId;

    /**
     * Publication Id
     * @var int
     */
    private $publicationId;

    /**
     * To pay value
     * @var decimal
     */
    private $toPay;

    /**
     * Currency
     * @var string
     */
    private $currency;

    /**
     * Subscription type.
     * 'T' for Trial subscription or 'P' for paid subscription.
     * 
     * @var string
     */
    private $type = 'P';

}