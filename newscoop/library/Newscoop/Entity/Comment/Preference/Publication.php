<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Comment\Preference;

/**
 * Publication entity
 * @Entity(repositoryClass="Newscoop\Entity\Repository\Comment\Preference\PublicationRepository")
 * @Table(name="comment_preference_publication")
 */
class Publication
{
    /**
     * @oneToOne(targetEntity="Newscoop\Entity\Publication")
     * @joinColumn(name="id", referencedColumnName="Id")
     * @var Newscoop\Entity\Publication
     */
    private $id;

    /**
     * @column(name="enabled")
     * @var bool
     */
    private $enabled;

    /**
     * @column(name="article_default_enabled")
     * @var bool
     */
    private $article_default_enabled;

    /**
     * @column(name="subscribers_moderated")
     * @var bool
     */
    private $subscribers_moderated;

    /**
     * @column(name="public_moderated")
     * @var bool
     */
    private $public_moderated;

    /**
     * @column(name="public_enabled")
     * @var bool
     */
    private $public_enabled;

    /**
     * @column(name="captcha_enabled")
     * @var bool
     */
    private $captcha_enabled;

    /**
     * @column(name="spam_blocking_enabled")
     * @var bool
     */
    private $spam_blocking_enabled;

    /**
     * @column(name="moderator_to")
     * @var string
     */
    private $moderator_to;

    /**
     * @column(name="moderator_from")
     * @var string
     */
    private $moderator_from;

    /**
     * Setting the the id
     *
     * @param Newscoop\Entity\Publication $p_id
     * @return unknown_type
     */
    public function setId(Newscoop\Entity\Publication $p_id)
    {
        $this->id = $p_id;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Getting the the id
     *
     * @param Newscoop\Entity\Publication $p_id
     * @return unknown_type
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set enabled id
     *
     * @param bool $p_enab;ed
     * @return Publication
     */
    public function setEnabled($p_enabled)
    {
        $this->enabled = $p_enabled;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get enabled id
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set article default enabled
     *
     * @param bool $p_article_default_enabled
     * @return Publication
     */
    public function setArticleDefaultEnabled($p_article_default_enabled)
    {
        $this->article_default_enabled = $p_article_default_enabled;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get enabled id
     *
     * @return bool
     */
    public function getArticleDefaultEnabled()
    {
        return $this->article_default_enabled;
    }

    /**
     * Set subscribers moderated
     *
     * @param bool $p_enabled
     * @return Publication
     */
    public function setSubscribersModerated($p_subscribers_moderated)
    {
        $this->subscribers_moderated = $p_subscribers_moderated;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get subscribers moderated
     *
     * @return bool
     */
    public function getSubscribersModerated()
    {
        return $this->subscribers_moderated;
    }

    /**
     * Set public moderated
     *
     * @param bool $p_public_moderated
     * @return Publication
     */
    public function setPublicModerated($p_public_moderated)
    {
        $this->public_moderated = $p_public_moderated;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get public moderated
     *
     * @return bool
     */
    public function getPublicModerated()
    {
        return $this->public_moderated;
    }

    /**
     * Set public moderated
     *
     * @param bool $p_public_moderated
     * @return Publication
     */
    public function setCaptchaEnabled($p_captcha_enabled)
    {
        $this->captcha_enabled = $p_captcha_enabled;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get public moderated
     *
     * @return bool
     */
    public function getCaptchaEnabled()
    {
        return $this->captcha_enabled;
    }

    /**
     * Set spam blocking enabled
     *
     * @param bool $p_spam_blocking_enabled
     * @return Publication
     */
    public function setSpamBlockingEnabled($p_spam_blocking_enabled)
    {
        $this->spam_blocking_enabled = $p_spam_blocking_enabled;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get spam blocking enabled
     *
     * @return bool
     */
    public function getSpamBlockingEnabled()
    {
        return $this->spam_blocking_enabled;
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

}