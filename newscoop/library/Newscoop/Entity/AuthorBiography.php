<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

/**
 * @Entity
 * @Table(name="AuthorBiographies")
 */
class AuthorBiography
{
    /**
     * @Id @ManyToOne(targetEntity="Newscoop\Entity\Author", inversedBy="biographies")
     * @JoinColumn(name="fk_author_id")
     * @var Newscoop\Entity\Author
     */
    private $author;

    /**
     * @Id @Column(type="integer", name="fk_language_id")
     * @var int
     */
    private $languageId;

    /**
     * @Column(length=255)
     * @var string
     */
    private $biography;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->biography;
    }
}
