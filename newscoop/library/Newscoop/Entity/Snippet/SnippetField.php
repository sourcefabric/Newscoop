<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @author Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Snippet;

use Doctrine\ORM\Mapping AS ORM;
use Newscoop\Entity\Snippet\SnippetTemplate\SnippetTemplateField;

/**
 * Snippet Template Field entity
 *
 * @ORM\Entity
 * @ORM\Table(name="SnippetFields")
 */
class SnippetField
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="Id", type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Snippet", inversedBy="fields")
     * @ORM\JoinColumn(name="SnippetId", referencedColumnName="Id")
     * @var Newscoop\Entity\Snippet
     */
    protected $snippet;

    /**
     * @ORM\OneToOne(targetEntity="Newscoop\Entity\Snippet\SnippetTemplate\SnippetTemplateField")
     * @ORM\JoinColumn(name="TemplateId", referencedColumnName="Id")
     * @var Newscoop\Entity\Snippet\SnippetTemplate\SnippetTemplateField
     */
    protected $field;

    /**
     * @ORM\Column(name="Data", type="text", nullable=true)
     * @var text
     */
    protected $data;

    /**
     * Getter for id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Setter for id
     *
     * @param int $id
     *
     * @return Newscoop\Entity\Snippet\SnippetField
     */
    public function setId($id)
    {
        $this->id = $id;
    
        return $this;
    }

    /**
     * Getter for field name
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->field->getName();
    }

    /**
     * Getter for field
     *
     * @return Newscoop\Entity\Snippet\SnippetTemplate\SnippetTemplateField
     */
    public function getTemplateField()
    {
        return $this->field;
    }
    
    /**
     * Setter for field
     *
     * @param mixed $field Value to set
     *
     * @return Newscoop\Entity\Snippet\SnippetField
     */
    public function setTemplateField(SnippetTemplateField $field)
    {
        $this->field = $field;
    
        return $this;
    }

    /**
     * Getter for data
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * Setter for data
     *
     * @param mixed $data Value to set
     *
     * @return Newscoop\Entity\Snippet\SnippetField
     */
    public function setData($data)
    {
        $this->data = $data;
    
        return $this;
    }
    
    /**
     * SnippetField name
     *
     * @return string name
     */
    public function __toString()
    {
        return $this->field->getName();
    }
}