<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @author Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Snippet;

use Doctrine\ORM\Mapping AS ORM;

/**
 * Snippet Template entity
 * @ORM\Entity
 * @ORM\Table(name="SnippetTemplates")
 */
class Template
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
     * @ORM\Column(name="Controller", type="string")
     * @var string
     */
    protected $controller;

    /**
     * @ORM\OneToMany(targetEntity="Newscoop\Entity\Snippet\Template\Field", mappedBy="template")
     * @var Newscoop\Entity\Snippet\Template\Field
     */
    protected $fields;

    /**
     * @ORM\Column(name="Template", type="text")
     * @var text
     */
    protected $template;

    /**
     * @ORM\Column(name="Favourite", type="boolean")
     * @var boolean
     */
    protected $favourite;

    /**
     * @ORM\Column(name="IconInactive", type="text")
     * @var text base64 encoded image
     */
    protected $iconInactive;

    /**
     * @ORM\Column(name="IconActive", type="text")
     * @var text base64 encoded image
     */
    protected $iconActive;

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
     * @return Newscoop\Entity\Snippet\Template
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
     * @return Newscoop\Entity\Snippet\Template
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
     * @return Newscoop\Entity\Snippet\Template
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    
        return $this;
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
     * @param Newscoop\Entity\Snippet\Template\Field $field Field to add
     *
     * @return Newscoop\Entity\Snippet\Template
     */
    public function addField($field)
    {
        $this->field = $field;
    
        return $this;
    }
    
    /**
     * Getter for template
     *
     * @return string JSON
     */
    public function getTemplate()
    {
        return $this->template;
    }
    
    /**
     * Setter for template
     *
     * @param string JSON $template
     *
     * @return Newscoop\Entity\Snippet\Template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    
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
     * @return Newscoop\Entity\Snippet\Template
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
     * @param text base64 encoded image $iconInactive
     *
     * @return Newscoop\Entity\Snippet\Template
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
     * @param text base64 encoded image $iconInactive
     *
     * @return Newscoop\Entity\Snippet\Template
     */
    public function setIconActive($iconActive)
    {
        $this->iconActive = $iconActive;
    
        return $this;
    }
}