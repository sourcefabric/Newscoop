<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Subscription;

use Newscoop\Entity\Publication;
use Newscoop\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Newscoop\Subscription\Article;
use Newscoop\Subscription\Issue;

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
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\User")
     * @ORM\JoinColumn(name="IdUser", referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Publication")
     * @ORM\JoinColumn(name="IdPublication", referencedColumnName="Id")
     * @var Newscoop\Entity\Publication
     */
    protected $publication;

    /**
     * @ORM\Column(type="decimal", name="ToPay")
     * @var float
     */
    protected $toPay = 0.0;

    /**
     * @ORM\Column(name="Type")
     * @var string
     */
    protected $type;

    /**
     * @ORM\Column(name="Currency")
     * @var string
     */
    protected $currency;

    /**
     * @ORM\Column(name="Active")
     * @var string
     */
    protected $active;

    /**
     * @ORM\OneToMany(targetEntity="Newscoop\Subscription\Section", mappedBy="subscription", cascade={"persist", "remove"})
     * @var array
     */
    protected $sections;

    /**
     * @ORM\OneToMany(targetEntity="Newscoop\Subscription\Article", mappedBy="subscription", cascade={"persist", "remove"})
     * @var array
     */
    protected $articles;

    /**
     * @ORM\OneToMany(targetEntity="Newscoop\Subscription\Issue", mappedBy="subscription", cascade={"persist", "remove"})
     * @var array
     */
    public $issues;

    public function __construct()
    {
        $this->sections = new \Doctrine\Common\Collections\ArrayCollection();
        $this->articles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->issues = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Subscription
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
     * @param Publication $publication
     * @return Subscription
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
     * @return Subscription
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
     * @return Subscription
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
     * @return Subscription
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
     * @param Publication $publication
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
     * @param Section $section
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
                $this->articles->remove($key);
            }
        }
    }

    /**
     * Add article
     * 
     * @param Article $article
     * @return void
     */
    public function addArticle(Article $article)
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
        }
    }

    /**
     * Set issues
     *
     * @param array $values
     * @return void
     */
    public function setIssues(array $values)
    {
        $ids = array_map(function($issue) {
            return !empty($issue['id']) ? $issue['id'] : null;
        }, $values);

        foreach ($this->issues as $key => $issue) {
            if (!in_array($issue->getId(), $ids)) {
                $this->issues->remove($key);
            }
        }
    }

    /**
     * Add issue
     * 
     * @param Issue $issue
     * @return void
     */
    public function addIssue(Issue $issue)
    {
        if (!$this->issues->contains($issue)) {
            $this->issues->add($issue);
        }
    }

    /**
     * Test if has given section
     *
     * @param \Newscoop\Entity\Section $section
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
     * Get issues
     *
     * @return array
     */
    public function getIssues()
    {
        return $this->issues;
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
     * @return Subscription
     */
    public function setCurrency($currency) {
        $this->currency = $currency;

        return $this;
    }
}
