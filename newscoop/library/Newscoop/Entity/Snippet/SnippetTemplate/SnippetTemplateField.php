<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric z.ú.
 * @author Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Snippet\SnippetTemplate;

use Doctrine\ORM\Mapping as ORM;
use Newscoop\Entity\Snippet\SnippetTemplate;

/**
 * Snippet Template Field entity
 *
 * @ORM\Entity
 * @ORM\Table(name="SnippetTemplateField")
 */
class SnippetTemplateField
{
    const TYPE_INT = 'integer';
    const TYPE_TEXT = 'text';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_URL = 'url';
    const TYPE_BOOL = 'boolean';
    const SCOPE_FRONTEND = 'frontend';
    const SCOPE_BACKEND = 'backend';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="Id", type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(name="Name", type="string")
     * @var string
     */
    protected $templateFieldName;

    /**
     * @ORM\Column(name="Type", type="string")
     * @var string
     */
    protected $type;

    /**
     * @ORM\Column(name="Scope", type="string")
     * @var string
     */
    protected $scope;

    /**
     * @ORM\Column(name="Required", type="boolean")
     * @var boolean
     */
    protected $required;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Snippet\SnippetTemplate", inversedBy="fields")
     * @ORM\JoinColumn(name="TemplateId", referencedColumnName="Id", nullable=false)
     * @var Newscoop\Entity\Snippet\SnippetTemplate
     */
    protected $template;

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
     * @return SnippetTemplateField
     */
    public function setId($id)
    {
        $this->id = $id;
    
        return $this;
    }

    /**
     * Getter for name
     *
     * @return string
     */
    public function getName()
    {
        return $this->templateFieldName;
    }

   public function hasName()
   {
      return !empty($this->templateFieldName);
   }
    
    /**
     * Setter for name
     *
     * @param string $templateFieldName
     *
     * @return SnippetTemplateField
     */
    public function setName($templateFieldName)
    {
        $this->templateFieldName = $templateFieldName;
    
        return $this;
    }

    /**
     * Getter for type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Setter for type
     *
     * @param string $type Value to set
     *
     * @return SnippetTemplateField
     */
    public function setType($type)
    {
        if (!in_array($type, array(self::TYPE_INT, self::TYPE_BOOL, self::TYPE_TEXT, self::TYPE_TEXTAREA, self::TYPE_URL))) {
            throw new \InvalidArgumentException("Invalid type");
        }
        $this->type = $type;
    
        return $this;
    }
    
    /**
     * Getter for scope
     *
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }
    
    /**
     * Setter for scope
     *
     * @param string $scope Value to set
     *
     * @return SnippetTemplateField
     */
    public function setScope($scope)
    {
        if (!in_array($scope, array(self::SCOPE_BACKEND, self::SCOPE_FRONTEND))) {
            throw new \InvalidArgumentException("Invalid scope");
        }
        $this->scope = $scope;
    
        return $this;
    }
    
    /**
     * Getter for template
     *
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }
    
    /**
     * Setter for template
     *
     * @param  $template Value to set
     *
     * @return self
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    
        return $this;
    }
    
    /**
     * Getter for required
     *
     * @return boolean
     */
    public function getRequired()
    {
        return $this->required;
    }
    
    /**
     * Setter for Required
     *
     * @param boolean $required Value to set
     *
     * @return self
     */
    public function setRequired($required)
    {
        $this->required = $required;
    
        return $this;
    }
}