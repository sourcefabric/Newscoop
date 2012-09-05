<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

/**
 * ArticleImage entity
 * @Entity
 * @Table(name="ArticleImages")
 */
class ArticleImage
{
    /**
     * @Id @Column(type="integer", name="NrArticle")
     * @var int
     */
    private $articleNumber;

    /**
     * @Id @Column(type="integer", name="IdImage")
     * @var int
     */
    private $imageId;

    /**
     * @Column(type="integer", name="Number", nullable=True)
     * @var int
     */
    private $number;
}
