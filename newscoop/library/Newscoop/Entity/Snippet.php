<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @author Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping AS ORM;
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
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Snippet\SnippetTemplate", cascade={"persist"})
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
     * @ORM\OneToMany(targetEntity="Newscoop\Entity\Snippet\SnippetField", mappedBy="snippet", cascade={"persist"})
     * @ORM\JoinColumn(name="FieldId", referencedColumnName="Id")
     * @var Newscoop\Entity\Snippet\SnippetField
     */
    protected $fields;

    /**
     * @ORM\OneToMany(targetEntity="Newscoop\Entity\Article", mappedBy="snippets")
     * @var Newscoop\Entity\Article
     */
    protected $articles;

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
        $this->fields = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->setTemplate($template);
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
     * @return Newscoop\Entity\Snippet\SnippetTemplate
     */
    public function getTemplate()
    {
        return $this->template;
    }
    
    /**
     * Setter for Template
     *
     * @param Newscoop\Entity\Snippet\SnippetTemplate $template
     *
     * @return Newscoop\Entity\Snippet
     */
    public function setTemplate(SnippetTemplate $template)
    {
        $this->template = $template;

        foreach ($template->getFields() as $templateField) {
            $field = new SnippetField();
            $field->setTemplateField($templateField);
            $this->addField($field);
        }
    
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
     * Add Snippet fields
     *
     * @param Newscoop\Entity\Snippet\SnippetField $field SnippetField to add
     *
     * @return Newscoop\Entity\Snippet
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
     * @return Newscoop\Entity\Snippet
     */
    public function setData($fieldName, $fieldData = null)
    {
        if ($this->fields->containsKey($fieldName)) {
            $this->fields->get($fieldName)->setData($fieldData);
        } else {
            throw new \Exception('Snippet: "'.$this->name.'" does not have Field: "'.$fieldName.'"');
        }

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