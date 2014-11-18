<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric z.ú.
 * @author Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Snippet;

use Doctrine\ORM\Mapping as ORM;
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
     * @ORM\JoinColumn(name="SnippetId", referencedColumnName="Id", nullable=false)
     * @var Newscoop\Entity\Snippet
     */
    protected $snippet;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Snippet\SnippetTemplate\SnippetTemplateField")
     * @ORM\JoinColumn(name="TemplateFieldId", referencedColumnName="Id", nullable=false)
     * @var Newscoop\Entity\Snippet\SnippetTemplate\SnippetTemplateField
     */
    protected $templateField;

    /**
     * @ORM\Column(name="Data", type="text", nullable=true)
     * @var text
     */
    protected $data;

    /**
     * Doctrine is not able to read the Name of the SnippetField without a real property
     * 
     * @ORM\Column(name="Name", type="string", length=255, nullable=false)
     * @var text
     */
    protected $fieldName;

    public function __construct(SnippetTemplateField $templateField)
    {
        $this->setTemplateField($templateField);
        $this->fieldName = $this->getFieldName();
    }

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
     * @return SnippetField
     */
    public function setId($id)
    {
        $this->id = $id;
    
        return $this;
    }

    /**
     * Getter for snippet
     *
     * @return mixed
     */
    public function getSnippet()
    {
        return $this->snippet;
    }
    
    /**
     * Setter for snippet
     *
     * @param  $snippet Value to set
     *
     * @return self
     */
    public function setSnippet($snippet)
    {
        $this->snippet = $snippet;
    
        return $this;
    }
    

    /**
     * Getter for templateField name
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->templateField->getName();
    }

    /**
     * Getter for templateField type
     *
     * @return string
     */
    public function getFieldType()
    {
        return $this->templateField->getType();
    }

    /**
     * Getter for templateField scope
     *
     * @return string
     */
    public function getFieldScope()
    {
        return $this->templateField->getScope();
    }

    /**
     * Getter for templateField required
     *
     * @return string
     */
    public function isRequired()
    {
        return $this->templateField->getRequired();
    }

    /**
     * Getter for templateField
     *
     * @return Newscoop\Entity\Snippet\SnippetTemplate\SnippetTemplateField
     */
    public function getTemplateField()
    {
        return $this->templateField;
    }
    
    /**
     * Setter for templateField
     *
     * @param SnippetTemplateField $templateField Value to set
     *
     * @return SnippetField
     */
    public function setTemplateField(SnippetTemplateField $templateField)
    {
        $this->templateField = $templateField;
    
        return $this;
    }

    /**
     * Getter for data
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->typeJuggle($this->data);
    }
    
    /**
     * Setter for data
     *
     * @param mixed $data Value to set
     *
     * @return SnippetField
     */
    public function setData($data)
    {
        $this->data = $this->typeJuggle($data);
    
        return $this;
    }

    /**
     * Check if Field has Data set
     *
     * @return boolean
     */
    public function hasData()
    {
        $data = $this->typeJuggle($this->data);
        return !empty($data);
    }

    private function typeJuggle($data = null)
    {
        if ($this->getFieldType() === 'integer') {
            $data = intval($data);
        }
        
        if ($this->getFieldType() === 'bool') {
            $data = boolval($data);
        }

        if ($this->getFieldType() === 'string') {
            $data = strval($data);
        }

        return $data;
    }
    
    /**
     * SnippetField name
     *
     * @return string name
     */
    public function __toString()
    {
        return $this->getFieldName();
    }
}