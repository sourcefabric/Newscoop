<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Subscription;

use Newscoop\Entity\Publication;
use Newscoop\Entity\User;
use Doctrine\ORM\Mapping AS ORM;
use Newscoop\Subscription\Article;

/**
 * Subscription entity
 * @ORM\Entity(repositoryClass="Newscoop\Subscription\SubscriptionRepository")
 * @ORM\Table(name="Subscriptions")
 */
class Subscription
{
    const TYPE_PAID = 'P';
    const TYPE_PAID_NOW = 'PN';
    const TYPE_TRIAL = 'T';

    /**
     * @ORM\Id 
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="Id")
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\User")
     * @ORM\JoinColumn(name="IdUser", referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Publication")
     * @ORM\JoinColumn(name="IdPublication", referencedColumnName="Id")
     * @var Newscoop\Entity\Publication
     */
    private $publication;

    /**
     * @ORM\Column(type="decimal", name="ToPay")
     * @var float
     */
    private $toPay = 0.0;

    /**
     * @ORM\Column(name="Type")
     * @var string
     */
    private $type;

    /**
     * @ORM\Column(name="Currency")
     * @var string
     */
    private $currency;

    /**
     * @ORM\Column(name="Active")
     * @var string
     */
    private $active;

    /**
     * @ORM\OneToMany(targetEntity="Newscoop\Subscription\Section", mappedBy="subscription", cascade={"persist", "remove"})
     * @var array
     */
    private $sections;

    /**
     * @ORM\OneToMany(targetEntity="Newscoop\Subscription\Article", mappedBy="subscription", cascade={"persist", "remove"})
     * @var array
     */
    private $articles;

    public function __construct()
    {
        $this->sections = new \Doctrine\Common\Collections\ArrayCollection();
        $this->currency = '';
        $this->active = false;
        $this->type = self::TYPE_PAID;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return (int) $this->id;
    }

    /**
     * Set user
     *
     * @param Newscoop\Entity\User $user
     * @return void
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return Newscoop\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set publication
     *
     * @param Newscoop\Entity\Publication $publication
     * @return Newscoop\Entity\Subscription
     */
    public function setPublication(Publication $publication)
    {
        $this->publication = $publication;
        return $this;
    }

    /**
     * Get publication
     *
     * @return Newscoop\Entity\Publication
     */
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     * Get publication name
     *
     * @return string
     */
    public function getPublicationName()
    {
        return $this->publication->getName();
    }

    /**
     * Get publication id
     *
     * @return int
     */
    public function getPublicationId()
    {
        return $this->publication->getId();
    }

    /**
     * Set to pay
     *
     * @param float $toPay
     * @return Newscoop\Entity\Subscription
     */
    public function setToPay($toPay)
    {
        $this->toPay = (float) $toPay;
        return $this;
    }

    /**
     * Get to pay
     *
     * @return float
     */
    public function getToPay()
    {
        return (float) $this->toPay;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Newscoop\Entity\Subscription
     */
    public function setType($type)
    {
        $this->type = strtoupper($type) === self::TYPE_TRIAL ? self::TYPE_TRIAL : self::TYPE_PAID;
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Test if is trial
     *
     * @return bool
     */
    public function isTrial()
    {
        return $this->type === self::TYPE_TRIAL;
    }

    /**
     * Set active
     *
     * @param bool $active
     * @return Newscoop\Entity\Subscription
     */
    public function setActive($active)
    {
        $this->active = (bool) $active ? 'Y' : 'N';
        return $this;
    }

    /**
     * Is active
     *
     * @return bool
     */
    public function isActive()
    {
        return strtoupper($this->active) === 'Y';
    }

    /**
     * Add sections
     *
     * @param array $values
     * @param Newscoop\Entity\Publication $publication
     * @return void
     */
    public function addSections(array $values, \Newscoop\Entity\Publication $publication)
    {
        $languages = array();
        if (!empty($values['individual_languages']) && $values['individual_languages']) {
            $languages = $values['languages'];
            if (empty($languages)) {
                throw new \InvalidArgumentException("No languages set for individual languages");
            }
        }

        foreach ($publication->getIssues() as $issue) {
            if (!empty($languages) && !in_array($issue->getLanguageId(), $languages)) {
                continue;
            }

            foreach ($issue->getSections() as $section) {
                if ($this->hasSection($section, $languages)) {
                    continue;
                }

                $subSection = new Section($this, $section->getNumber());
                $subSection->setStartDate(new \DateTime($values['start_date']));
                $subSection->setDays($values['days']);

                if ($this->isTrial() || $values['type'] === self::TYPE_PAID_NOW) {
                    $subSection->setPaidDays($values['days']);
                }

                if (!empty($languages)) {
                    $subSection->setLanguage($issue->getLanguage());
                }
            }
        }
    }

    /**
     * Set sections
     *
     * @param array $values
     * @return void
     */
    public function setSections(array $values)
    {
        $ids = array_map(function($section) {
            return !empty($section['id']) ? $section['id'] : null;
        }, $values);

        foreach ($this->sections as $key => $section) {
            if (!in_array($section->getId(), $ids)) {
                $this->sections->remove($key);
            }
        }
    }

    /**
     * Add section
     *
     * @param Newscoop\Subscription\Section $section
     * @return void
     */
    public function addSection(Section $section)
    {
        if (!$this->sections->contains($section)) {
            $this->sections->add($section);
        }
    }

    /**
     * Set articles
     *
     * @param array $values
     * @return void
     */
    public function setArticles(array $values)
    {
        $ids = array_map(function($article) {
            return !empty($article['id']) ? $article['id'] : null;
        }, $values);

        foreach ($this->articles as $key => $article) {
            if (!in_array($article->getId(), $ids)) {
                $this->sections->remove($key);
            }
        }
    }

    /**
     * Add article
     * 
     * @param Newscoop\Subscription\Article $section
     * @return void
     */
    public function addArticle(Article $article)
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
        }
    }

    /**
     * Test if has given section
     *
     * @param Newscoop\Subscription\Section $section
     * @param array $languages
     * @return bool
     */
    private function hasSection(\Newscoop\Entity\Section $section, array $languages)
    {
        foreach ($this->sections as $s) {
            if ($s->getSectionNumber() == $section->getNumber()) {
                if (!$s->hasLanguage()) {
                    return true;
                } else if (empty($languages)) {
                    $s->setLanguage(null);
                    return true;
                } else if ($s->getLanguage() == $section->getLanguage()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get sections
     *
     * @return array
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * Get articles
     *
     * @return array
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Get currency
     * @return string
     */
    public function getCurrency() {
        return $this->currency;
    }

    /**
     * Set currency
     * @return string
     */
    public function setCurrency($currency) {
        $this->currency = $currency;

        return $this;
    }
}
