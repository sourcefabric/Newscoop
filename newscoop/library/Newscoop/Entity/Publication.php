<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Publication entity
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\PublicationRepository")
 * @ORM\Table(name="Publications")
 */
class Publication
{
    /**
     * Provides the class name as a constant.
     */
    const NAME = __CLASS__;

    /* --------------------------------------------------------------- */

    /**
     * @ORM\Id @ORM\GeneratedValue
     * @ORM\Column(name="Id", type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(name="Name", nullable=True)
     * @var string
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity="Newscoop\Entity\Language")
     * @ORM\JoinColumn(name="IdDefaultLanguage", referencedColumnName="Id")
     * @var Newscoop\Entity\Language
     */
    private $language;

    /**
     * @ORM\OneToMany(targetEntity="Newscoop\Entity\Issue", mappedBy="publication")
     * @var array
     */
    private $issues;

    /**
     * @ORM\Column(name="comments_public_enabled", nullable=True)
     * @var bool
     */
    private $public_enabled;

    /**
     * @ORM\Column(name="comments_moderator_to", nullable=True)
     * @var string
     */
    private $moderator_to;

    /**
     * @ORM\Column(name="comments_moderator_from", nullable=True)
     * @var string
     */
    private $moderator_from;

    /**
     * @ORM\Column(name="TimeUnit", nullable=True)
     * @var string
     */
    private $timeUnit;

    /**
     * @ORM\Column(type="decimal", name="UnitCost", nullable=True)
     * @var float
     */
    private $unitCost;

    /**
     * @ORM\Column(type="decimal", name="UnitCostAllLang", nullable=True)
     * @var float
     */
    private $unitCostAll;

    /**
     * @ORM\Column(name="Currency", nullable=True)
     * @var string
     */
    private $currency;

    /**
     * @ORM\Column(type="integer", name="TrialTime", nullable=True)
     * @var int
     */
    private $trialTime;

    /**
     * @ORM\Column(type="integer", name="PaidTime", nullable=True)
     * @var int
     */
    private $paidTime;

    /**
     * @ORM\Column(type="integer", name="IdDefaultAlias", nullable=True)
     * @var int
     */
    private $defaultAliasId;

    /**
     * @ORM\Column(type="integer", name="IdURLType", nullable=True)
     * @var int
     */
    private $urlTypeId;

    /**
     * @ORM\Column(type="integer", name="fk_forum_id", nullable=True)
     * @var int
     */
    private $forumId;

    /**
     * @ORM\Column(type="boolean", name="comments_enabled", nullable=True)
     * @var bool
     */
    private $commentsEnabled;

    /**
     * @ORM\Column(type="boolean", name="comments_article_default_enabled", nullable=True)
     * @var bool
     */
    private $commentsArticleDefaultEnabled;

    /**
     * @ORM\Column(type="boolean", name="comments_subscribers_moderated", nullable=True)
     * @var bool
     */
    private $commentsSubscribersModerated;

    /**
     * @ORM\Column(type="boolean", name="comments_public_moderated", nullable=True)
     * @var bool
     */
    private $commentsPublicModerated;

    /**
     * @ORM\Column(type="boolean", name="comments_captcha_enabled", nullable=True)
     * @var bool
     */
    private $commentsCaptchaEnabled;

    /**
     * @ORM\Column(type="boolean", name="comments_spam_blocking_enabled", nullable=True)
     * @var bool
     */
    private $commentsSpamBlockingEnabled;

    /**
     * @ORM\Column(type="integer", name="url_error_tpl_id", nullable=True)
     * @var int
     */
    private $urlErrorTemplateId;

    /**
     * @ORM\Column(nullable=True)
     * @var int
     */
    private $seo;

    /**
     */
    public function __construct()
    {
        $this->issues = new ArrayCollection();
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get language
     *
     * @return Newscoop\Entity\Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Add issue
     *
     * @param Newscoop\Entity\Issue $issue
     * @return void
     */
    public function addIssue(Issue $issue)
    {
        if (!$this->issues->contains($issue)) {
            $this->issues->add($issue);
        }
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
     * Get languages
     *
     * @return array
     */
    public function getLanguages()
    {
        $languages = array();
        foreach ($this->issues as $issue) {
            $languages[$issue->getLanguage()->getId()] = $issue->getLanguage();
        }

        return array_values($languages);
    }

    /**
     * Set default language
     *
     * @param Newscoop\Entity\Language $language
     * @return void
     */
    public function setDefaultLanguage(Language $language)
    {
        $this->language = $language;
    }

    /**
     * Get default language of the publication
     *
     * @return Newscoop\Entity\Language
     */
    public function getDefaultLanguage()
    {
        return $this->language;
    }

    /**
     * Get default language name of the publication
     *
     * @return string
     */
    public function getDefaultLanguageName()
    {
        return $this->default_language->getName();
    }

    /*
     * Get sections
     *
     * @return array
     */
    public function getSections()
    {
        $added = array();
        $sections = array();
        foreach ($this->issues as $issue) {
            foreach ($issue->getSections() as $section) {
                if (in_array($section->getNumber(), $added)) { // @todo handle within repository
                    continue;
                }

                $sections[] = $section;
                $added[] = $section->getNumber();
            }
        }

        return $sections;
    }

    /**
     * Set id
     *
     * @param int $id
     * @return void
     */
    public function setId($id)
    {
        $this->id = (int) $id;
    }

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
     * Set moderator to email address
     *
     * @param string $p_moderator_to
     * @return Publication
     */
    public function setModeratorTo($p_moderator_to)
    {
        return $this->moderator_to = $p_moderator_to;
    }

    /**
     * Get moderator to email address
     *
     * @return string
     */
    public function getModeratorTo()
    {
        return $this->moderator_to;
    }

    /**
     * Set moderator from email address
     *
     * @param string $p_moderator_from
     * @return Publication
     */
    public function setModeratorFrom($p_moderator_from)
    {
        return $this->moderator_to = $p_moderator_from;
    }

    /**
     * Get moderator from email address
     *
     * @return string
     */
    public function getModeratorFrom()
    {
        return $this->moderator_from;
    }

    public function getCaptchaEnabled()
    {
        return $this->commentsCaptchaEnabled;
    }

    public function getCommentsSubscribersModerated()
    {
        return $this->commentsSubscribersModerated;
    }

    public function getCommentsPublicModerated()
    {
        return $this->commentsPublicModerated;
    }
}

