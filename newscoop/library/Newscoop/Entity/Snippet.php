<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @author Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Newscoop\Entity\Snippet\SnippetTemplate;
use Newscoop\Entity\Snippet\SnippetField;

/**
 * Snippet entity
 *
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
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Snippet\SnippetTemplate", cascade={"persist"}, inversedBy="snippets")
     * @ORM\JoinColumn(name="TemplateId", referencedColumnName="Id")
     * @var Newscoop\Entity\Snippet\SnippetTemplate
     */
    protected $template;

    /**
     * @ORM\Column(name="Name", type="string")
     * @var string
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="Newscoop\Entity\Snippet\SnippetField", mappedBy="snippet", cascade={"persist", "remove"}, indexBy="fieldName")
     * @var Doctrine\Common\Collections\ArrayCollection
     */
    protected $fields;

    /**
     * @ORM\ManyToMany(targetEntity="Newscoop\Entity\Article", mappedBy="snippets")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="ArticleNr", referencedColumnName="Number"),
     *      @ORM\JoinColumn(name="LanguageId", referencedColumnName="IdLanguage")
     *      })
     * @var Newscoop\Entity\Article
     */
    protected $articles;

    /**
     * @ORM\Column(name="Enabled", type="boolean", nullable=false)
     * @var boolean
     */
    protected $enabled = true;

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
     * Constructs the Snippet
     *
     * @param SnippetTemplate $template SnippetTemplate to set for this Snippet
     */
    public function __construct(SnippetTemplate $template)
    {
        if (!$template->hasFields()) {
            throw new \Exception('SnippetTemplate should have fields');
        }
        if (!$template->getEnabled()) {
            throw new \Exception('SnippetTemplate should be enabled');
        }
        $this->fields = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->setTemplate($template);
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
     * @return Snippet
     */
    public function setId($id)
    {
        $this->id = $id;
    
        return $this;
    }

    /**
     * Getter for Template
     *
     * @return Newscoop\Entity\Snippet\SnippetTemplate
     */
    public function getTemplate()
    {
        return $this->template;
    }
    
    /**
     * Setter for Template
     *
     * @param SnippetTemplate $template
     *
     * @return Snippet
     */
    public function setTemplate(SnippetTemplate $template)
    {
        $this->template = $template;

        foreach ($template->getFields() as $templateField) {
            $field = new SnippetField($templateField);
            $field->setSnippet($this);
            $this->addField($field);
        }

        $this->setModified();
    
        return $this;
    }

    /**
     * Returns the Template Name
     *
     * @return string template name
     **/
    public function getTemplateName()
    {
        return $this->template->getName();
    }

    /**
     * Returns the Template id
     *
     * @return int template id
     **/
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
     * @return Snippet
     */
    public function setName($name)
    {
        $this->name = $name;
        $this->setModified();
    
        return $this;
    }
    
    /**
     * Check if Snippet has a name
     *
     * @return boolean
     */
    public function hasName()
    {
        return !empty($this->name);
    }

    /**
     * Getter for fields
     *
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Getter for fields without the Backend scope
     *
     * @return Doctrine\Common\Collections\ArrayCollection
     */
     public function getFieldsWithoutBackend() {
        return $this->fields->filter(
            function ($field) {
                if ($field->getFieldScope() == 'frontend') {
                    return $field;
                }
            }
        );
     }
    
    /**
     * Add Snippet fields
     *
     * @param SnippetField $field SnippetField to add
     *
     * @return Snippet
     */
    private function addField(SnippetField $field)
    {
        $this->fields->set($field->getFieldName(), $field);
    
        return $this;
    }

    /**
     * Set Snippet Data to the appropriate fields
     * 
     * @param string $fieldName the name of the SnippetField
     * @param string $fieldData the data of the SnippetField
     *
     * @return Snippet
     */
    public function setData($fieldName, $fieldData = null)
    {
        if ($this->fields->containsKey($fieldName)) {
            $this->fields->get($fieldName)->setData($fieldData);
        } else {
            throw new \Exception('Snippet: "'.$this->name.'" does not have Field: "'.$fieldName.'"');
        }

        $this->setModified();

        return $this;
    }

    /**
     * Return the attached Articles
     * 
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Add an Article to the Snippet
     * 
     * @param Article $article the Article to attach
     *
     * @return Snippet
     */
    public function addArticle(Article $article)
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->addSnippet($this);
        }

        return $this;
    }

    /**
     * Remove an Article from the Snippet
     * 
     * @param Article $article the Article to remove
     *
     * @return Snippet
     */
    public function removeArticle(Article $article)
    {
        if ($this->articles->contains($article)) {
            $article->removeSnippet($this);
            $this->articles->removeElement($article);
        }

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
     * @return Snippet
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
     * Renders the Snippet with the Data into the Template
     *
     * @return string rendered Snippet
     */
    public function render()
    {
        $twig = new \Twig_Environment(new \Twig_Loader_String());
        $fieldsToRender = array();

        foreach ($this->fields as $field) {
            $fieldsToRender[$field->getFieldName()] = $field->getData();   
        }

        $rendered = $twig->render(
            $this->getTemplate()->getTemplateCode(),
            $fieldsToRender
        );

        return $rendered;
    }
}