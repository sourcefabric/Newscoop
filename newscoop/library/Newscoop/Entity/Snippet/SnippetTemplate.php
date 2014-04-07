<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @author Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Snippet;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Newscoop\Entity\Snippet\SnippetTemplate\SnippetTemplateField;

/**
 * Snippet Template entity
 *
 * @ORM\Entity
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
     * @ORM\OneToMany(targetEntity="Newscoop\Entity\Snippet\SnippetTemplate\SnippetTemplateField", mappedBy="template", cascade={"persist"})
     * @var Newscoop\Entity\Snippet\SnippetTemplate\SnippetTemplateField
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
     * Constructs the SnippetTemplate
     */
    public function __construct()
    {
        $this->fields = new ArrayCollection();
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
     * @return Newscoop\Entity\Snippet\SnippetTemplate
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
        return $this->name;
    }
    
    /**
     * Setter for name
     *
     * @param string $name
     *
     * @return Newscoop\Entity\Snippet\SnippetTemplate
     */
    public function setName($name)
    {
        $this->name = $name;
    
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
     * @return Newscoop\Entity\Snippet\SnippetTemplate
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    
        return $this;
    }

    /**
     * Check if the Template has Fields
     *
     * @return mixed
     */
    public function hasFields()
    {
        return !is_null($this->fields);
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
     * @param Newscoop\Entity\Snippet\SnippetTemplate\SnippetTemplateField $field Field to add
     *
     * @return Newscoop\Entity\Snippet\SnippetTemplate
     */
    public function addField(SnippetTemplateField $field)
    {
        $this->fields->add($field);
    
        return $this;
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
     * @return Newscoop\Entity\Snippet\SnippetTemplate
     */
    public function setTemplateCode($templateCode)
    {
        $this->templateCode = $templateCode;
    
        return $this;
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
     * @return Newscoop\Entity\Snippet\SnippetTemplate
     */
    public function setFavourite($favourite)
    {
        $this->favourite = $favourite;
    
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
     * @return Newscoop\Entity\Snippet\SnippetTemplate
     */
    public function setIconInactive($iconInactive)
    {
        $this->iconInactive = $iconInactive;
    
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
     * @return Newscoop\Entity\Snippet\SnippetTemplate
     */
    public function setIconActive($iconActive)
    {
        $this->iconActive = $iconActive;
    
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