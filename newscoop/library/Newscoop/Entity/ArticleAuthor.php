<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

/**
 * Article Author Association class.
 *
 * @Entity
 * @Table(name="ArticleAuthors")
 */
class ArticleAuthor
{
    /**
     * @Id
     * @Column(type="integer", name="fk_article_number")
     * @var int
     */
    private $articleNumber;

    /**
     * @Id
     * @Column(type="integer", name="fk_language_id")
     * @var int
     */
    private $languageId;

    /**
     * @Id
     * @Column(type="integer", name="fk_author_id")
     * @var int
     */
    private $authorId;

    /**
     * @Column(type="integer", nullable=true, name="fk_type_id")
     * @var int
     */
    private $typeId;

    /**
     * @Column(type="integer", nullable=true, name="`order`")
     * @var int
     */
    private $order;

    /**
     * @param int $articleNumber
     * @param int $languageId
     * @param int $authorId
     * @param int $typeId
     * @param int $order
     */
    public function __construct($articleNumber, $languageId, $authorId, $typeId, $order)
    {
        $this->articleNumber = (int) $articleNumber;
        $this->languageId = (int) $languageId;
        $this->authorId = (int) $authorId;
        $this->typeId = (int) $typeId;
        $this->order = (int) $order;
    }
}
