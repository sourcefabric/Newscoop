<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Publication entity
 *
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\PublicationRepository")
 * @ORM\Table(name="Publications", indexes={
 *     @ORM\Index(name="Name", columns={"Name"}),
 *     @ORM\Index(name="Alias", columns={"IdDefaultAlias"}),
 * })
 */
class Publication
{
    /**
     * Provides the class name as a constant.
     */
    const NAME = __CLASS__;

    /* --------------------------------------------------------------- */

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="Id", type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(name="Name", nullable=false)
     * @var string
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Language")
     * @ORM\JoinColumn(name="IdDefaultLanguage", referencedColumnName="Id", columnDefinition="int(10)")
     * @var Newscoop\Entity\Language
     */
    protected $language;

    /**
     * @ORM\OneToMany(targetEntity="Newscoop\Entity\Issue", mappedBy="publication")
     * @var array
     */
    protected $issues;

    /**
     * @ORM\Column(name="comments_public_enabled", nullable=True)
     * @var bool
     */
    protected $public_enabled;

    /**
     * @ORM\Column(name="comments_moderator_to", nullable=True)
     * @var string
     */
    protected $moderator_to;

    /**
     * @ORM\Column(name="comments_moderator_from", nullable=True)
     * @var string
     */
    protected $moderator_from;

    /**
     * @ORM\Column(type="integer", name="IdDefaultAlias", nullable=True)
     * @var int
     */
    protected $defaultAliasId;

    /**
     * @ORM\ManyToOne(targetEntity="Aliases")
     * @ORM\JoinColumn(name="IdDefaultAlias", referencedColumnName="Id")
     */
    protected $defaultAlias;

    /**
     * @ORM\Column(type="integer", name="IdURLType", nullable=True)
     * @var int
     */
    protected $urlTypeId;

    /**
     * @ORM\Column(type="integer", name="fk_forum_id", nullable=True)
     * @var int
     */
    protected $forumId;

    /**
     * @ORM\Column(type="boolean", name="comments_enabled", nullable=True)
     * @var bool
     */
    protected $commentsEnabled;

    /**
     * @ORM\Column(type="boolean", name="comments_article_default_enabled", nullable=True)
     * @var bool
     */
    protected $commentsArticleDefaultEnabled;

    /**
     * @ORM\Column(type="boolean", name="comments_subscribers_moderated", nullable=True)
     * @var bool
     */
    protected $commentsSubscribersModerated;

    /**
     * @ORM\Column(type="boolean", name="comments_public_moderated", nullable=True)
     * @var bool
     */
    protected $commentsPublicModerated;

    /**
     * @ORM\Column(type="boolean", name="comments_captcha_enabled", nullable=True)
     * @var bool
     */
    protected $commentsCaptchaEnabled;

    /**
     * @ORM\Column(type="boolean", name="comments_spam_blocking_enabled", nullable=True)
     * @var bool
     */
    protected $commentsSpamBlockingEnabled;

    /**
     * @ORM\Column(type="integer", name="url_error_tpl_id", nullable=True)
     * @var int
     */
    protected $urlErrorTemplateId;

    /**
     * @ORM\Column(nullable=True)
     * @var string
     */
    protected $seo;

    /**
     * @ORM\Column(name="meta_title", nullable=true)
     * @var string
     */
    protected $metaTitle;

    /**
     * @ORM\Column(name="meta_keywords", nullable=true)
     * @var string
     */
    protected $metaKeywords;

    /**
     * @ORM\Column(name="meta_description", nullable=true)
     * @var string
     */
    protected $metaDescription;

    /**
     * @ORM\OneToMany(targetEntity="Newscoop\Entity\Output\OutputSettingsPublication", mappedBy="publication")
     * @var Doctrine\Common\Collections\ArrayCollection
     */
    protected $outputSettingsPublication;

    /**
     */
    public function __construct()
    {
        $this->issues = new ArrayCollection();
        $this->outputSettingsPublication = new ArrayCollection();
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

    public function setName($name)
    {
        $this->name = $name;

        return $this;
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

    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Add issue
     *
     * @param  Newscoop\Entity\Issue $issue
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
     * @param  Newscoop\Entity\Language $language
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
     * @param  int  $id
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
     * @param  string      $p_moderator_to
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
     * @param  string      $p_moderator_from
     * @return Publication
     */
    public function setModeratorFrom($p_moderator_from)
    {
        return $this->moderator_from = $p_moderator_from;
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

    /**
     * Get defaultAlias
     *
     * @return Aliases
     */
    public function getDefaultAlias()
    {
        return $this->defaultAlias;
    }

    public function setDefaultAlias($alias)
    {
        $this->defaultAlias = $alias;

        return $this;
    }

    public function getCaptchaEnabled()
    {
        return $this->commentsCaptchaEnabled;
    }

    public function getCommentsSubscribersModerated()
    {
        return $this->commentsSubscribersModerated;
    }

    public function setCommentsSubscribersModerated($commentsSubscribersModerated)
    {
        $this->commentsSubscribersModerated = $commentsSubscribersModerated;

        return $this;
    }

    public function getCommentsPublicModerated()
    {
        return $this->commentsPublicModerated;
    }

    public function setCommentsPublicModerated($commentsPublicModerated)
    {
        $this->commentsPublicModerated = $commentsPublicModerated;

        return $this;
    }

    /**
     * Gets the value of public_enabled.
     *
     * @return bool
     */
    public function getPublicCommentsEnabled()
    {
        return (boolean) $this->public_enabled;
    }

    /**
     * Sets the value of public_enabled.
     *
     * @param bool $public_enabled the public_enabled
     *
     * @return self
     */
    public function setPublicCommentsEnabled($public_enabled)
    {
        $this->public_enabled = $public_enabled;

        return $this;
    }

    /**
     * Getter for defaultAliasId
     *
     * @return mixed
     */
    public function getDefaultAliasId()
    {
        return $this->defaultAliasId;
    }

    /**
     * Setter for defaultAliasId
     *
     * @param mixed $defaultAliasId Value to set
     *
     * @return self
     */
    public function setDefaultAliasId($defaultAliasId)
    {
        $this->defaultAliasId = $defaultAliasId;

        return $this;
    }

    /**
     * Set seo
     *
     * @param array $seo
     *
     * @return self
     */
    public function setSeo(array $seo)
    {
        $this->seo = serialize($seo);

        return $this;
    }

    /**
     * Get seo
     *
     * @return array
     */
    public function getSeo()
    {
        return (array) unserialize($this->seo);
    }

    public function getSeoChoices()
    {
        $choices = array();
        foreach ($this->getSeo() as $key => $value) {
            if ($value == 'on') {
                $choices[] = $key;
            }
        }

        return $choices;
    }

    public function setSeoChoices($data)
    {
        $seo = array();
        foreach ($data as $value) {
            $seo[$value] = 'on';
        }

        $this->setSeo($seo);
    }

    /**
     * Gets the value of urlTypeId.
     *
     * @return int
     */
    public function getUrlTypeId()
    {
        return $this->urlTypeId;
    }

    /**
     * Sets the value of urlTypeId.
     *
     * @param int $urlTypeId the url type id
     *
     * @return self
     */
    public function setUrlTypeId($urlTypeId)
    {
        $this->urlTypeId = $urlTypeId;

        return $this;
    }

    /**
     * Gets the value of metaTitle.
     *
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * Sets the value of metaTitle.
     *
     * @param string $metaTitle the meta title
     *
     * @return self
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    /**
     * Gets the value of metaKeywords.
     *
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * Sets the value of metaKeywords.
     *
     * @param string $metaKeywords the meta keywords
     *
     * @return self
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    /**
     * Gets the value of metaDescription.
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Sets the value of metaDescription.
     *
     * @param string $metaDescription the meta description
     *
     * @return self
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * Getter for outputSettingsPublication
     *
     * @return mixed
     */
    public function getOutputSettingsPublication()
    {
        return $this->outputSettingsPublication;
    }

    /**
     * Setter for outputSettingsPublication
     *
     * @return self
     */
    public function setOutputSettingsPublication($outputSettingsPublication)
    {
        $this->outputSettingsPublication = $outputSettingsPublication;

        return $this;
    }

    /**
     * Setter for outputSettingsPublication
     *
     * @param Newscoop\Entity\OutputSettingsPublication $outputSettingsPublication
     *
     * @return self
     */
    public function addOutputSettingsPublication($outputSettingsPublication)
    {
        if (!$this->outputSettingsPublication->contains($outputSettingsPublication)) {
            $this->outputSettingsPublication->add($outputSettingsPublication);
        }

        return $this;
    }


    /**
     * Gets the value of commentsArticleDefaultEnabled.
     *
     * @return bool
     */
    public function getCommentsArticleDefaultEnabled()
    {
        return $this->commentsArticleDefaultEnabled;
    }

    /**
     * Sets the value of commentsArticleDefaultEnabled.
     *
     * @param bool $commentsArticleDefaultEnabled the comments article default enabled
     *
     * @return self
     */
    public function setCommentsArticleDefaultEnabled($commentsArticleDefaultEnabled)
    {
        $this->commentsArticleDefaultEnabled = $commentsArticleDefaultEnabled;

        return $this;
    }

    /**
     * Gets the value of commentsCaptchaEnabled.
     *
     * @return bool
     */
    public function getCommentsCaptchaEnabled()
    {
        return $this->commentsCaptchaEnabled;
    }

    /**
     * Sets the value of commentsCaptchaEnabled.
     *
     * @param bool $commentsCaptchaEnabled the comments captcha enabled
     *
     * @return self
     */
    public function setCommentsCaptchaEnabled($commentsCaptchaEnabled)
    {
        $this->commentsCaptchaEnabled = $commentsCaptchaEnabled;

        return $this;
    }

    /**
     * Gets the value of commentsSpamBlockingEnabled.
     *
     * @return bool
     */
    public function getCommentsSpamBlockingEnabled()
    {
        return $this->commentsSpamBlockingEnabled;
    }

    /**
     * Sets the value of commentsSpamBlockingEnabled.
     *
     * @param bool $commentsSpamBlockingEnabled the comments spam blocking enabled
     *
     * @return self
     */
    public function setCommentsSpamBlockingEnabled($commentsSpamBlockingEnabled)
    {
        $this->commentsSpamBlockingEnabled = $commentsSpamBlockingEnabled;

        return $this;
    }

    /**
     * Gets the value of commentsEnabled.
     *
     * @return bool
     */
    public function getCommentsEnabled()
    {
        return $this->commentsEnabled;
    }

    /**
     * Sets the value of commentsEnabled.
     *
     * @param bool $commentsEnabled the comments enabled
     *
     * @return self
     */
    public function setCommentsEnabled($commentsEnabled)
    {
        $this->commentsEnabled = $commentsEnabled;

        return $this;
    }

    /**
     * Gets the value of public_enabled.
     *
     * @return bool
     */
    public function getPublicEnabled()
    {
        return (boolean) $this->public_enabled;
    }

    /**
     * Sets the value of public_enabled.
     *
     * @param bool $public_enabled the public enabled
     *
     * @return self
     */
    public function setPublicEnabled($public_enabled)
    {
        $this->public_enabled = $public_enabled;

        return $this;
    }
}
