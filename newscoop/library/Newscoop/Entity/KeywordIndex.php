<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * KeywordIndex entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="KeywordIndex")
 */
class KeywordIndex
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", name="Keyword")
     * @var string
     */
    protected $keyword;

    /**
     * @ORM\Column(name="Id", type="integer")
     * @var int
     */
    protected $id;
}