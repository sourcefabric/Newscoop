<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * ArticleImage entity
 * @ORM\Entity
 * @ORM\Table(name="ArticleImages")
 */
class ArticleImage
{
    /**
     * @ORM\Id 
     * @ORM\Column(type="integer", name="NrArticle")
     * @var int
     */
    protected $articleNumber;

    /**
     * @ORM\Id 
     * @ORM\Column(type="integer", name="IdImage")
     * @var int
     */
    protected $imageId;

    /**
     * @ORM\Column(type="integer", name="Number", nullable=True)
     * @var int
     */
    protected $number;

    /**
     * Gets the value of number.
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Sets the value of number.
     *
     * @param int $number the number
     *
     * @return self
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }
}
