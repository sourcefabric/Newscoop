<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric z.ú.
 * @author Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Snippet;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Newscoop\Entity\Snippet;
use Newscoop\Entity\Snippet\SnippetTemplate\SnippetTemplateField;

/**
 * Snippet Template entity
 *
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\Snippet\SnippetTemplateRepository")
 * @ORM\Table(name="SnippetTemplates")
 */
class SnippetTemplate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="Id", type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Newscoop\Entity\Snippet", cascade={"remove"}, mappedBy="template")
     * @var Doctrine\Common\Collections\ArrayCollection
     */
    protected $snippets;

    /**
     * @ORM\Column(name="Name", type="string")
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(name="Controller", type="string", nullable=true)
     * @var string
     */
    protected $controller;

    /**
     * @ORM\OneToMany(targetEntity="Newscoop\Entity\Snippet\SnippetTemplate\SnippetTemplateField", mappedBy="template", cascade={"persist", "remove"}, indexBy="templateFieldName")
     * @var Doctrine\Common\Collections\ArrayCollection
     */
    protected $fields;

    /**
     * @ORM\Column(name="TemplateCode", type="text")
     * @var text
     */
    protected $templateCode;

    /**
     * @ORM\Column(name="Favourite", type="boolean", nullable=true)
     * @var boolean
     */
    protected $favourite;

    /**
     * @ORM\Column(name="Enabled", type="boolean", nullable=false)
     * @var boolean
     */
    protected $enabled = true;

    /**
     * @ORM\Column(name="IconInactive", type="text", nullable=true)
     * @var text base64 encoded image
     */
    protected $iconInactive;

    /**
     * @ORM\Column(name="IconActive", type="text", nullable=true)
     * @var text base64 encoded image
     */
    protected $iconActive;

    /**
     * @ORM\Column(name="Created", type="datetime", nullable=false)
     * @var string
     */
    protected $created;

    /**
     * @ORM\Column(name="Modified", type="datetime", nullable=false)
     * @var string
     */
    protected $modified;

    /**
     * Constructs the SnippetTemplate
     */
    public function __construct()
    {
        $this->fields = new ArrayCollection();
        $this->setCreated();
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
     * @return SnippetTemplate
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get Snippets using the selected Template
     *
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getSnippets()
    {
        return $this->snippets;
    }

    /**
     * Getter for name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

   public function hasName()
   {
      return !empty($this->name);
   }

    /**
     * Setter for name
     *
     * @param string $name
     *
     * @return SnippetTemplate
     */
    public function setName($name)
    {
        $this->name = $name;
        $this->setModified();

        return $this;
    }

    /**
     * Getter for Controller
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Setter for controller
     *
     * @param string $controller Value to set
     *
     * @return SnippetTemplate
     */
    public function setController($controller)
    {
        $this->controller = $controller;
        $this->setModified();

        return $this;
    }

    public function hasController()
    {
        return !empty($this->controller);
    }

    public function executeController(Snippet $snippet, $update = false)
    {
        return new $this->controller($snippet, $update);
    }

    /**
     * Check if the Template has Fields
     *
     * @return boolean
     */
    public function hasFields()
    {
      if (count($this->fields) >= 1) {
         return true;
      }

      return false;
    }

    /**
     * Getter for field
     *
     * @return mixed
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Add Snippet Template Field
     *
     * @param SnippetTemplateField $field Field to add
     *
     * @return SnippetTemplate
     */
    public function addField(SnippetTemplateField $field)
    {
        $field->setTemplate($this);
        $this->fields->add($field);
        $this->setModified();

        return $this;
    }

    /**
     * Create and add Snippet Template Field
     *
     * @param array  $parameters          Array with Parameters
     *
     * @return SnippetTemplate
     */
    public function createField(array $parameters)
    {
        if (!array_key_exists('name', $parameters)) {
            throw new \InvalidArgumentException("Name is not defined");
        }

        $defaultParams = array(
            'type' => 'string',
            'scope' => 'frontend'
            );

        foreach ($defaultParams as $defaultParam => $defaultValue) {
            if (!array_key_exists($defaultParam, $parameters)) {
                $parameters[$defaultParam] = $defaultValue;
            }
        }

        $snippetTemplateField = new SnippetTemplateField();
        $snippetTemplateField->setName($parameters['name']);
        $snippetTemplateField->setType($parameters['type']);
        $snippetTemplateField->setScope($parameters['scope']);

        return $this->addField($snippetTemplateField);
    }

    /**
     * Getter for template
     *
     * @return string JSON
     */
    public function getTemplateCode()
    {
        return $this->templateCode;
    }

    /**
     * Setter for template code
     *
     * @param string $templateCode Template Code to Set (Twig format)
     *
     * @return SnippetTemplate
     */
    public function setTemplateCode($templateCode)
    {
        $this->templateCode = $templateCode;
        $this->setModified();

        return $this;
    }

   public function hasTemplateCode()
   {
      return !empty($this->templateCode);
   }

    /**
     * Getter for favourite
     *
     * @return boolean
     */
    public function getFavourite()
    {
        return $this->favourite;
    }

    /**
     * Setter for favourite
     *
     * @param boolean $favourite
     *
     * @return SnippetTemplate
     */
    public function setFavourite($favourite)
    {
        $this->favourite = $favourite;
        $this->setModified();

        return $this;
    }

    /**
     * Getter for enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Setter for enabled
     *
     * @param mixed $enabled Value to set
     *
     * @return self
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        $this->setModified();

        return $this;
    }

    /**
     * Getter for iconInactive
     *
     * @return text base64 encoded image
     */
    public function getIconInactive()
    {
        return $this->iconInactive;
    }

    /**
     * Setter for iconInactive
     *
     * @param string $iconInactive base64 encoded image
     *
     * @return SnippetTemplate
     */
    public function setIconInactive($iconInactive)
    {
        $this->iconInactive = $iconInactive;
        $this->setModified();

        return $this;
    }

    /**
     * Getter for iconActive
     *
     * @return text base64 encoded image
     */
    public function getIconActive()
    {
        return $this->iconActive;
    }

    /**
     * Setter for iconActive
     *
     * @param string $iconActive base64 encoded image $iconInactive
     *
     * @return SnippetTemplate
     */
    public function setIconActive($iconActive)
    {
        $this->iconActive = $iconActive;
        $this->setModified();

        return $this;
    }

    /**
     * Getter for created
     *
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }
    
    /**
     * Setter for created
     *
     * @param DateTime $created Value to set
     *
     * @return SnippetTemplate
     */
    public function setCreated($created = null)
    {
        if (!($created instanceof \DateTime)) {
            $created = new \DateTime("now");
        }
        
        $this->created = $created;    
        $this->setModified();
    
        return $this;
    }
    
    /**
     * Getter for modified
     *
     * @return string
     */
    public function getModified()
    {
        return $this->modified;
    }
    
    /**
     * Setter for modified
     *
     * @param mixed $modified Value to set
     *
     * @return self
     */
    public function setModified($modified = null)
    {
        if (!($modified instanceof \DateTime)) {
            $modified = new \DateTime("now");
        }

        $this->modified = $modified;
    
        return $this;
    }

    /**
     * Returns SnippetTemplate name
     *
     * @return string name
     */
    public function __toString()
    {
        return $this->name;
    }
}