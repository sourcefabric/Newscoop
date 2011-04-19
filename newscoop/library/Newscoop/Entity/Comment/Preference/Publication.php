<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Comment\Prefence;

/**
 * Publication entity
 * @Entity(repositoryClass="Newscoop\Entity\Repository\PublicationRepository")
 * @Table(name="Publications")
 */
class Publication
{
    /**
     * @Id @generatedValue
     * @Column(type="integer", name="Id")
     * @var int
     */
    private $id;

    /**
     * @column(name="comments_enabled")
     * @var bool
     */
    private $enabled;

    /**
     * @column(name="comments_article_default_enabled")
     * @var bool
     */
    private $article_default_enabled;

    /**
     * @column(name="comments_subscribers_moderated")
     * @var bool
     */
    private $subscribers_moderated;

     /**
     * @column(name="comments_public_moderated")
     * @var bool
     */
    private $public_moderated;
     /**
     * @column(name="comments_captcha_enabled")
     * @var bool
     */
    private $captcha_enabled;

    /**
     * @column(name="comments_spam_blocking_enabled")
     * @var bool
     */
    private $spam_blocking_enabled;



}

