<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @author Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Snippet\Template;

use Doctrine\ORM\Mapping AS ORM;

/**
 * Snippet Template Field entity
 * @ORM\Entity
 * @ORM\Table(name="SnippetTemplateField")
 */
class TemplateField
{
    const TYPE_INT = 'int';
    const TYPE_STRING = 'string';
    const TYPE_BOOL = 'bool';
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
    protected $name;

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
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Snippet\Template", inversedBy="fields")
     * @ORM\JoinColumn(name="TemplateId", referencedColumnName="Id")
     * @var Newscoop\Entity\Snippet\Template
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
     * @return Newscoop\Entity\Snippet\Template\TemplateField
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
     * @return Newscoop\Entity\Snippet\Template\TemplateField
     */
    public function setName($name)
    {
        $this->name = $name;
    
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
     * @return Newscoop\Entity\Snippet\Template\TemplateField
     */
    public function setType($type)
    {
        if (!in_array($type, array(self::TYPE_INT, self::TYPE_BOOL, self::TYPE_STRING))) {
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
     * @return Newscoop\Entity\Snippet\Template\TemplateField
     */
    public function setScope($scope)
    {
        if (!in_array($scope, array(self::SCOPE_BACKEND, self::SCOPE_FRONTEND))) {
            throw new \InvalidArgumentException("Invalid scope");
        }
        $this->scope = $scope;
    
        return $this;
    }
    
}