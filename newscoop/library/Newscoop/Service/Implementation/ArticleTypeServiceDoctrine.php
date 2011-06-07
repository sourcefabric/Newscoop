<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Implementation;

use Newscoop\Service\IArticleTypeService,
    Newscoop\Entity\ArticleType,
    Newscoop\Utils\Validation,
    Newscoop\Service\Resource\ResourceId,
    Newscoop\Entity\ArticleTypeField;

class ArticleTypeServiceDoctrine implements IArticleTypeService
{

    /**
     * query alias for table
     */
    const ALIAS = 'at';

    /**
     * @var Newscoop\Service\Resource\ResourceId
     */
    private $id;

    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em = NULL;

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
    protected function getEntityManager()
    {
        if ($this->em === NULL) {
            $doctrine = \Zend_Registry::get( 'doctrine' );
            $this->em = $doctrine->getEntityManager();
        }
        return $this->em;
    }

    public function findAllTypes()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb ->select( self::ALIAS )
            ->from( '\Newscoop\Entity\ArticleType', self::ALIAS )
            // TODO get legacy sql thing with string 'null' out when time comes
            ->where( self::ALIAS . ".fieldName IS NULL " . ' OR ' . self::ALIAS . ".fieldName = 'NULL'" );

        return $qb->getQuery()->getResult();
    }

    public function findFields(ArticleType $type)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb ->select( self::ALIAS )
            ->from( '\Newscoop\Entity\ArticleTypeField', self::ALIAS )
            ->where( self::ALIAS . '.typeHack = ?1' . ' AND ' . self::ALIAS . ".name IS NOT NULL" . ' AND ' . self::ALIAS . ".name <> 'NULL'" )->setParameter( 1, $type );

        /**
         * @todo at refactor @see hack from \Newscoop\Entity\ArticleTypeField
         */
        foreach(( $results = $qb->getQuery()->getResult() ) as $atf) {
            $atf->setArticleType( $type );
        }
        return $results;
    }

    public function findFieldByName(ArticleType $type, $name)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
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
        $qb = $this->getEntityManager()->createQueryBuilder();

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
     * @return Newscoop\Entity\ArticleType
     * @throws PDOException probably if duplicate values
     */
    public function create( $name )
    {
        $ret = $this->_create( $name );
        $this->getEntityManager()->flush();
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

        $em = $this->getEntityManager();
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
        $this->getEntityManager()->flush();
        return $ret;
    }

    /**
     * @see Newscoop\Service\Implementation\ArticleTypeServiceDoctrine::createField()
     */
    private function _createField( $name, ArticleType $type, $props = null )
    {
        Validation::notEmpty( $name, 'name' );

        $artField = new ArticleTypeField();
        $artField->setArticleType($type)->setName($name);
        if( is_array( $props ) ) {
            foreach( $props as $prop => $val )
            {
                $setProp = "set".ucfirst( $prop );
                $artField->$setProp( $val );
            }
        }

        $em = $this->getEntityManager();
        $em->persist( $artField );

        return $artField;
    }

    /**
     * Creates more article types
     * @param array $articleTypes the array of types, optionally with fields
     * 		[ [ name : typeName, fields : [ name : fieldName, parentType : typeName, ignore : bool ], [...] ], [...] ]
     * @see self::create()
     */
    public function createMany( $articleTypes )
    {

        Validation::notEmpty( $articleTypes, 'articleTypes' );

        foreach( $articleTypes as $type )
        {
            $artType = $this->_create( $type['name'] );
            if( is_array( $type['fields'] ) ) {
                foreach( $type['fields'] as $field ) {
                    $this->_createField( $field['name'], $artType );
                }
            }
        }

        try
        {
            $this->getEntityManager()->flush();
        }
        catch( \PDOException $e )
        {
            if( $e->getCode( ) == 23000 ) // duplicate keys
                return false;
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
