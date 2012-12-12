<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * Section entity
 * @ORM\Entity()
 * @ORM\Table(name="SubsSections")
 */
class SubscriptionsSection
{
    /**
     * @ORM\Id 
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Subscriptions")
     * @ORM\JoinColumn(name="IdSubscription", referencedColumnName="Id")
     * @var Newscoop\Entity\Subscriptions
     */
    private $subscription;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Section")
     * @ORM\JoinColumn(name="SectionNumber", referencedColumnName="number")
     * @var Newscoop\Entity\Section
     */
    private $section;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Language")
     * @ORM\JoinColumn(name="IdLanguage", referencedColumnName="Id")
     * @var Newscoop\Entity\Language
     */
    private $language;

    /**
     * @ORM\Column(type="date", name="StartDate")
     * @var datetime
     */
    private $startDate;

    /**
     * @ORM\Column(type="integer", name="Days")
     * @var integer
     */
    private $days;

    /**
     * @ORM\Column(type="integer", name="PaidDays")
     * @var integer
     */
    private $paidDays;

    /**
     * @ORM\Column(type="string", name="NoticeSent")
     * @var string
     */
    private $noticeSent;

    /**
     * Get startDate
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Get paidDays
     * @return integer
     */
    public function getPaidDays()
    {
        return $this->paidDays;
    }

    /**
     * Set noticeSent
     * @param string $noticeSent
     */
    public function setNoticeSent($noticeSent)
    {
        $this->noticeSent = $noticeSent;

        return $this;
    }
}
