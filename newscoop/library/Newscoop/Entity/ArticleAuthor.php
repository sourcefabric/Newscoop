<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Newscoop\Utils\Validation;

/**
 * Issue entity
 * @ORM\Entity
 * @ORM\Table(name="ArticleAuthors")
 */
class ArticleAuthor
{
    /**
     * @ORM\ManyToOne(targetEntity="Article", inversedBy="articleAuthorTypes")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="article_number", referencedColumnName="Number"),
     *      @ORM\JoinColumn(name="language_id", referencedColumnName="IdLanguage")
     *  })
     */
    private $article;

    /**
     * @ORM\OneToMany(targetEntity="AuthorType", mappedBy="id")
     * @var Newscoop\Entity\AuthorTypes
     */
    private $type;
    
    /**
     * Get article
     *
     * @return Newscoop\Entity\Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Get type
     *
     * @return Newscoop\Entity\AuthorTypes
     */
    public function getType()
    {
        return $this->type;
    }
}

