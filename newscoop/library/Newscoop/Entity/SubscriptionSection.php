<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Newscoop\Entity\Subscription;

/**
 * Subscription Section relation entity
 * @Entity(repositoryClass="Newscoop\Entity\Repository\SubscriptionSectionRepository")
 * @Table(name="SubsSections")
 */
class SubscriptionSection
{
    /**
     * @Id @GeneratedValue
     * @Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\Subscription")
     * @JoinColumn(name="IdSubscription", referencedColumnName="Id")
     * @var Newscoop\Entity\Subscription
     */
    private $subscription;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\Section")
     * @JoinColumn(name="SectionNumber", referencedColumnName="Number")
     * @var Newscoop\Entity\Section
     */
    private $section;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\Language")
     * @JoinColumn(name="IdLanguage", referencedColumnName="Id")
     * @var Newscoop\Entity\Language
     */
    private $language;

    /**
     * @Column(type="date", name="StartDate")
     * @var DateTime
     */
    private $startDate;

    /**
     * @Column(type="integer", name="Days")
     * @var int
     */
    private $days;

    /**
     * @Column(type="integer", name="PaidDays")
     * @var int
     */
    private $paidDays;

    /**
     * @Column(name="NoticeSent")
     * @var string
     */
    private $noticeSent = 'N';

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set subscription
     *
     * @param Newscoop\Entity\Subscription
     * @return Newscoop\Entity\SubscriptionSection
     */
    public function setSubscription(Subscription $subscription)
    {
        $this->subscription = $subscription;
        return $this;
    }

    /**
     * Set section
     *
     * @param Newscoop\Entity\Section $section
     * @return Newscoop\Entity\SubscriptionSection
     */
    public function setSection(Section $section)
    {
        $this->section = $section;
        return $this;
    }

    /**
     * Get section number
     *
     * @return int
     */
    public function getSectionNumber()
    {
        return $this->section->getNumber();
    }

    /**
     * Get section name
     *
     * @return string
     */
    public function getSectionName()
    {
        return $this->section->getName();
    }

    /**
     * Set language
     *
     * @param Newscoop\Entity\Language $language
     * @return Newscoop\Entity\SubscriptionSection
     */
    public function setLanguage(Language $language)
    {
        $this->language = $language;
        return $this;
    }

    /**
     * Get language id
     *
     * @return int
     */
    public function getLanguageId()
    {
        return $this->language ? $this->language->getId() : 0;
    }

    /**
     * Get language name
     *
     * @return string
     */
    public function getLanguageName()
    {
        return $this->language ? $this->language->getName() : '';
    }

    /**
     * Set start date
     *
     * @param DateTime $date
     * @return Newscoop\Entity\SubscriptionSection
     */
    public function setStartDate(\DateTime $date)
    {
        $this->startDate = $date;
        return $this;
    }

    /**
     * Get start date
     *
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set days
     *
     * @param int $days
     * @return Newscoop\Entity\SubscriptionSection
     */
    public function setDays($days)
    {
        $this->days = abs($days);
        return $this;
    }

    /**
     * Get days
     *
     * @return int
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * Set paid days
     *
     * @param int $paidDays
     * @return Newscoop\Entity\SubscriptionSection
     */
    public function setPaidDays($paidDays)
    {
        $this->paidDays = abs($paidDays);
    }

    /**
     * Get paid days
     *
     * @return int
     */
    public function getPaidDays()
    {
        return $this->paidDays;
    }
}

