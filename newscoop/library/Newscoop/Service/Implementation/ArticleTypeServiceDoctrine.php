<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Implementation;

use Newscoop\Service\IArticleTypeService;
use Newscoop\Entity\ArticleType;
use Newscoop\Utils\Validation;
use Newscoop\Service\Resource\ResourceId;
use Newscoop\Entity\ArticleTypeField;

class ArticleTypeServiceDoctrine implements IArticleTypeService
{

    /**
     * query alias for table
     */
    const ALIAS = 'at';

    /**
     * @var Newscoop\Service\Resource\ResourceId
     */
    protected $id;

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em = NULL;

    /**
     * Construct the service base d on the provided resource id.
     * @param ResourceId $id
     * The resource id, not null not empty
     */
    public function __construct(ResourceId $id)
    {
        Validation::notEmpty( $id, 'id' );
        $this->id = $id;

     //$this->_init();
    }

    /**
     * Provides the resource id.
     *
     * @return Newscoop\Services\Resource\ResourceId
     * The resource id.
     */
    protected function getResourceId()
    {
        return $this->id;
    }

    /** Provides the dictrine entity manager.
     *
     * @return Doctrine\ORM\EntityManager
     * The doctrine entity manager.
     */
    protected function getManager()
    {
        if ($this->em === NULL) {
            $this->em = \Zend_Registry::get('container')->getService('em');
        }
        return $this->em;
    }

    public function findAllTypes()
    {
        $query = $this->getManager()->getRepository('Newscoop\Entity\ArticleType')->getAllTypes();
        return $query->getResult();
    }

    public function findFields(ArticleType $type)
    {
        $query = $this->getManager()->getRepository('Newscoop\Entity\ArticleTypeField')->getFieldsForType($type);

        /**
         * @todo at refactor @see hack from \Newscoop\Entity\ArticleTypeField
         */
        foreach(( $results = $query->getResult() ) as $atf ) {
            $atf->setArticleType( $type );
        }
        return $results;
    }

    public function findFieldByName(ArticleType $type, $name)
    {
        $qb = $this->getManager()->createQueryBuilder();
        $qb ->select( self::ALIAS )
            ->from( '\Newscoop\Entity\ArticleTypeField', self::ALIAS )
            ->where
            (
                self::ALIAS . '.typeHack = ?1' . ' AND '
            .   self::ALIAS . '.name IS NOT NULL' . ' AND '
            .   self::ALIAS . ".name <> 'NULL' AND "
            .   self::ALIAS . '.name = ?2'
            )
            ->setParameter( 1, $type )
            ->setParameter( 2, $name );

        /**
         * @todo at refactor @see hack from \Newscoop\Entity\ArticleTypeField
         */
        $atf = current( $qb->getQuery()->getResult() );
        if( !$atf )
            return null;
        $atf->setArticleType( $type );
        return $atf;
    }

    public function findTypeByName($name)
    {
        $qb = $this->getManager()->createQueryBuilder();

        $qb ->select( self::ALIAS )
            ->from( '\Newscoop\Entity\ArticleType', self::ALIAS )
            // TODO get legacy sql thing with string 'null' out when time comes
            ->where( " ( " . self::ALIAS . ".fieldName IS NULL OR " . self::ALIAS . ".fieldName = 'NULL' ) AND " . self::ALIAS . '.name = ?1' )->setParameter( 1, $name );

        if( !( ( $res = @current( $qb->getQuery()->getResult() ) ) instanceof ArticleType ) )
            return null;
        return $res;
    }

    /**
     * Creates an article type
     * @param string $name the name of the new article type, not null
     * @return ArticleType
     * @throws PDOException probably if duplicate values
     */
    public function create( $name )
    {
        $ret = $this->_create( $name );
        $this->getManager()->flush();
        return $ret;
    }

    /**
     * @see Newscoop\Service\Implementation\ArticleTypeServiceDoctrine::create()
     */
    private function _create( $name )
    {
        Validation::notEmpty( $name, 'name' );

        $artType = new ArticleType();
        $artType->setName( $name );

        $em = $this->getManager();
        $em->persist( $artType );

        return $artType;
    }

	/**
     * Creates a field
     *
     * @param string $name the name of the article type field, not null
     *
     * @param Newscoop\Entity\ArticleType $name
     * 		the name of the article type field, not null
     *
     * @param array $props properies of the field
     * 		@see Newscoop\Entity\ArticleType
     *
     * @return Newscoop\Entity\ArticleTypeField
     * @throws PDOException probably if duplicate values
     */
    public function createField( $name, ArticleType $type, $props = null )
    {
        $ret = $this->createField( $name, $type, $props );
        $this->getManager()->flush();
        return $ret;
    }

    /**
     * @see Newscoop\Service\Implementation\ArticleTypeServiceDoctrine::createField()
     */
    private function _createField( $name, ArticleType $type, $props = null )
    {
        Validation::notEmpty( $name, 'name' );

        $artField = new ArticleTypeField();
        // TODO hack hack hack
        $artField->setArticleType($type)->setArticleTypeHack($type)->setName($name);
        if( is_array( $props ) ) {
            foreach( $props as $prop => $val )
            {
                $setProp = "set".ucfirst( $prop );
                $artField->$setProp( $val );
            }
        }

        $em = $this->getManager();
        $em->persist( $artField );

        return $artField;
    }

    /**
     * Creates more article types
     * @param array $articleTypes the array of types, optionally with fields
     * 		[ typeName => [ fields : [ name : fieldName, parentType : typeName, ignore : bool ], [...] ], [...] ]
     * @see self::create()
     */
    public function createMany( $articleTypes )
    {

        Validation::notEmpty( $articleTypes, 'articleTypes' );

        foreach( $articleTypes as $typeName => $type )
        {
            $artType = $this->_create( $typeName );
            if( isset($type['fields']) && is_array($type['fields']) ) {
                foreach( $type['fields'] as $field ) {
                    $this->_createField( $field['name'], $artType );
                }
            }
        }

        try
        {
            $this->getManager()->flush();
        }
        catch( \PDOException $e )
        {
            // duplicate keys, no worries
            if( $e->getCode() == 23000 && strpos( $e->getMessage(), 'Duplicate' ) !== false ) {
                return true;
            }
            throw $e;
        }
        catch( \Exception $e )
        {
            throw $e;
        }
        return true;
    }

    public function getCount(Search $search = NULL)
    {
        return null;
    }
}
