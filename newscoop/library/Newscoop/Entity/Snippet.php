<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @author Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * Snippet entity
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\SnippetRepository")
 * @ORM\Table(name="Snippets")
 */
class Snippet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="Id", type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Newscoop\Entity\Snippet\Template")
     * @ORM\JoinColumn(name="TemplateId", referencedColumnName="Id")
     * @var Newscoop\Entity\Snippet\Template
     */
    protected $template;

    /**
     * @ORM\Column(name="Name", type="string")
     * @var string
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="Newscoop\Entity\Snippet\SnippetField", mappedBy="snippet")
     * @ORM\JoinColumn(name="FieldId", referencedColumnName="Id")
     * @var Newscoop\Entity\Snippet\SnippetField
     */
    protected $fields;

    /**
     * @ORM\OneToMany(targetEntity="Newscoop\Entity\Article", mappedBy="snippets")
     * @var Newscoop\Entity\Article
     */
    protected $articles;

    public function __construct()
    {
        $this->fields = new ArrayCollection();
        $this->articles = new ArrayCollection();
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
     * @return Newscoop\Entity\Snippet
     */
    public function setId($id)
    {
        $this->id = $id;
    
        return $this;
    }

    /**
     * Getter for Template
     *
     * @return Newscoop\Entity\Snippet\Template
     */
    public function getTemplate()
    {
        return $this->template;
    }
    
    /**
     * Setter for Template
     *
     * @param Newscoop\Entity\Snippet\Template $template
     *
     * @return Newscoop\Entity\Snippet
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    
        return $this;
    }

    public function getTemplateName()
    {
        return $this->template->getName();
    }

    public function getTemplateId()
    {
        return $this->template->getId();
    }
    
    /**
     * Getter for Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Setter for Name
     *
     * @param string $name
     *
     * @return Newscoop\Entity\Snippet
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Getter for fields
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }
    
    /**
     * Setter for fields
     *
     * @param mixed $fields Value to set
     *
     * @return Newscoop\Entity\Snippet
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    
        return $this;
    }
            
}