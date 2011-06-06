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
    Newscoop\Service\Resource\ResourceId;

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
            $atf->setType( $type );
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
        $atf->setType( $type );
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

    public function getCount(Search $search = NULL)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select( 'COUNT(' . self::ALIAS . ')' )->from( $this->entityClassName, self::ALIAS );

        if ($search !== NULL) {
            if (get_class( $search ) !== $this->searchClassName) {
                throw new \Exception( "The search needs to be a ' . $this->searchClassName . ' instance." );
            }
            $this->processInterogation( $search, $qb );
        }

        $result = $qb->getQuery()->getResult();
        return (int) $result[0][1];
    }
}
