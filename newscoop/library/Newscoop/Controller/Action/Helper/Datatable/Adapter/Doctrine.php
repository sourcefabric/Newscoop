<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Controller\Action\Helper\Datatable\Adapter;

use Doctrine\ORM\QueryBuilder,
    Doctrine\ORM\EntityManager;

/**
 * Doctrine adapter for datatable
 * copied from DatatableRepository
 */
class Doctrine extends AAdapter
{
    /** 
     * @var Doctrine\ORM\EntityManager 
     */
    private $_entityManager;

    /**
     * @var Doctrine\ORM\EntityRepository 
     */
    private $_repository;

    /** 
     * @var string
     */
    private $_entityName;

    /**
     * @var QueryBuilder
     */
    private $_queryObject;
    
    /**
     * @param Doctrine\ORM\EntityRepository $repository
     */
    public function __construct(  )
    {
    }
    
    public function setEntityManager( EntityManager $p_entityManager, $p_entityName = null )
    {
        $this->_entityManager = $p_entityManager;
        return $this;   
    }
    
    public function setEntityName( $p_entityName )
    {
        $this->_entityName = (string) $p_entityName;
        $this->_repository = $this->_entityManager->getRepository( $this->_entityName );
        return $this;
    }
    

    public function getData( array $p_params, array $p_cols )
    {
        $this->_queryObject = $this->_repository->createQueryBuilder( 'e' );
        
        // search
        if( !empty( $p_params['search'] ) ) {
            $this->search( $p_params['search'], $p_cols );
        }
        
        // sort
        if( @count( $p_params['sortCol'] ) ) {
            $this->sort( $p_params, $p_cols );
        }
        
        // limit
        $this->_queryObject
            ->setFirstResult( (int) $p_params['displayStart'] )
            ->setMaxResults( (int) $p_params['displayLength'] );
        
        return $this->_queryObject->getQuery()->getResult();
    }

    public function search( $query, array $cols = null )
    {
        $this->_queryObject->where( $this->buildWhere( $cols, $query ) );
    }
    
    public function sort( array $p_cols )
    {
        foreach( array_keys( $p_cols ) as $id => $property ) {
            if( !is_string( $property ) ) { // not sortable
                continue;
            }
            
            if(  @in_array( $id, $p_params['sortCol'] ) ) {
                $dir = @in_array( $id, $p_params['sortDir'] ) ?  : 'asc';
                $this->_queryObject->orderBy( "e.$property", $dir );
            }
        }
    }
    
    /**
     * Get filtered count
     *
     * @param array $params
     * @param array $cols
     * @return int
     */
    public function getCount( array $params = array(), array $cols = array() )
    {
        if( empty( $params['search'] ) ) {
            return $this->getCount();
        }
        
        return $this->_entityManager->createQueryBuilder()->select( 'COUNT(e)' )->from( $this->_entityName, 'e' )
            ->where( $this->buildWhere( $cols, $params['sSearch'] ) )
            ->getQuery()->getSingleScalarResult();
    }

    /**
     * Build where condition
     *
     * @param array $cols
     * @param string $search
     * @return Doctrine\ORM\Query\Expr
     */
    private function buildWhere( array $cols, $search )
    {
        $qb = $this->_repository->createQueryBuilder( 'e' );
        $or = $qb->expr()->orx();
        foreach( array_keys( $cols ) as $i => $property ) {
            if( !is_string( $property ) ) { // not searchable
                continue;
            }
            
            $or->add( $qb->expr()->like( "e.$property", $qb->expr()->literal( "%{$search}%" ) ) );
        }
        
        return $or;
    }
}