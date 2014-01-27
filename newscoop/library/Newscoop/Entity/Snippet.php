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
 * @ORM\Entity
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
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Newscoop\Entity\Snippet\Template")
     * @ORM\JoinColumn(name="TemplateId", referencedColumnName="Id")
     * @var Newscoop\Entity\Snippet\Template
     */
    private $template;

    /**
     * @ORM\Column(name="Name", type="string")
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(name="Parameters", type="text")
     * @var text
     */
    private $parameters;

    /**
     * @ORM\Column(name="Snippet", type="text")
     * @var text
     */
    private $snippet;

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
     * Getter for Parameters
     *
     * @return string JSON
     */
    public function getParameters()
    {
        return $this->parameters;
    }
    
    /**
     * Setter for Parameters
     *
     * @param string JSON $parameters
     *
     * @return Newscoop\Entity\Snippet
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    
        return $this;
    }
    
    
    /**
     * Getter for Snippet
     *
     * @return string JSON
     */
    public function getSnippet()
    {
        return $this->snippet;
    }
    
    /**
     * Setter for Snippet
     *
     * @param string JSON $snippet
     *
     * @return Newscoop\Entity\Snippet
     */
    public function setSnippet($snippet)
    {
        $this->snippet = $snippet;
    
        return $this;
    }
    
}