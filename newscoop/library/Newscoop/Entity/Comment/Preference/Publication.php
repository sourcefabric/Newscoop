<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Comment\Preference;

use Doctrine\ORM\Mapping as ORM;
/**
 * Publication entity
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\Comment\Preference\PublicationRepository")
 * @ORM\Table(name="comment_preference_publication")
 */
class Publication
{
    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Newscoop\Entity\Publication")
     * @ORM\JoinColumn(name="id", referencedColumnName="Id")
     * @var Newscoop\Entity\Publication
     */
    protected $id;

    /**
     * @ORM\Column(name="enabled")
     * @var bool
     */
    protected $enabled;

    /**
     * @ORM\Column(name="article_default_enabled")
     * @var bool
     */
    protected $article_default_enabled;

    /**
     * @ORM\Column(name="subscribers_moderated")
     * @var bool
     */
    protected $subscribers_moderated;

    /**
     * @ORM\Column(name="public_moderated")
     * @var bool
     */
    protected $public_moderated;

    /**
     * @ORM\Column(name="public_enabled")
     * @var bool
     */
    protected $public_enabled;

    /**
     * @ORM\Column(name="captcha_enabled")
     * @var bool
     */
    protected $captcha_enabled;

    /**
     * @ORM\Column(name="spam_blocking_enabled")
     * @var bool
     */
    protected $spam_blocking_enabled;

    /**
     * @ORM\Column(name="moderator_to")
     * @var string
     */
    protected $moderator_to;

    /**
     * @ORM\Column(name="moderator_from")
     * @var string
     */
    protected $moderator_from;

    /**
     * Setting the the id
     *
     * @param \Newscoop\Entity\Publication $p_id
     * @return Publication
     */
    public function setId(\Newscoop\Entity\Publication $p_id)
    {
        $this->id = $p_id;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Getting the the id
     *
     * @return unknown_type
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set enabled id
     *
     * @param bool $p_enabled
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
     * @return string
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
     * @return string
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
