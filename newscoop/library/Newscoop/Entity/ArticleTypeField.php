<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Collections\ArrayCollection;

/**
 * Issue entity
 * @Entity
 * @Table(name="ArticleTypeMetadata",{@UniqueConstraint(name="PRIMARY",columns={"type_name","filed_name"})})
 * 
 * @todo check for performance issues
 */
class ArticleTypeField
{
    /** 
     * @Id 
     * @Column(type="string",name="field_name") 
     */
    private $name;
    
    /**
	 * @Transient
	 * @var Newscoop\Entity\ArticleType
	 * (at) ManyToOne( targetEntity="Newscoop\Entity\ArticleType" )
	 * (at) JoinColumn( name="type_id", referencedColumnName="id", nullable=FALSE )
	 * @todo hack
     */
    private $type;

    /**
     * @Column(type="string",name="type_name")
     * @todo hack used for getting the parent article type from same table, which we will set later on into self::$type.
     */
    private $typeHack;
    
    /**
     * @return \Newscoop\Entity\ArticleType
     */
    public function getType()
    {
        return $this->type;
    }
    
	/**
     * @return \Newscoop\Entity\ArticleType
     */
    public function setType( ArticleType $type )
    {
        $this->type = $type;
        return $this;
    }
    
	/**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}

