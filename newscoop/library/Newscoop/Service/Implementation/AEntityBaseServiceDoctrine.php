<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Implementation;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Newscoop\Service\IEntityService;
use Newscoop\Service\Model\Search\ColumnOrder;
use Newscoop\Service\Model\Search\ColumnOrderLike;
use Newscoop\Utils\Validation;
use Newscoop\Service\Resource\ResourceId;
use Newscoop\Service\Model\Search\Column;
use Newscoop\Service\Model\Search\Search;

/**
 * Provides the base services implementation for the themes.
 */
abstract class AEntityBaseServiceDoctrine
{
    const ALIAS = 'en';

    /* --------------------------------------------------------------- */

    /** @var Newscoop\Service\Resource\ResourceId */
    private $id;
    /** @var Doctrine\ORM\EntityManager */
    private $em = NULL;

    /* ------------------------------- */
    /** @var string */
    protected $entityClassName;

    /* ------------------------------- */

    /**
     * Construct the service base d on the provided resource id.
     * @param ResourceId $id
     * 		The resource id, not null not empty
     */
    function __construct(ResourceId $id)
    {
        Validation::notEmpty($id, 'id');
        $this->id = $id;

        $this->_init_();

        if (is_null($this->entityClassName)) {
            throw  new \Exception("Please provide a entitity class name to be used");
        }
    }

    /* --------------------------------------------------------------- */

    function getById($id)
    {
        Validation::notEmpty($id, 'id');
        $entity = $this->findById($id);
        if ($entity === NULL) {
            throw \Exception("Cannot locate '$this->entityClassName' for id '$id'.");
        }
        return $entity;
    }

    function findById($id)
    {
        Validation::notEmpty($id, 'id');
        $em = $this->getEntityManager();
        return $em->find($this->entityClassName, $id);
    }

    /* --------------------------------------------------------------- */

    /**
     * Provides the resource id.
     *
     * @return Newscoop\Services\Resource\ResourceId
     * 		The resource id.
     */
    protected function getResourceId()
    {
        return $this->id;
    }

    /** Provides the dictrine entity manager.
     *
     * @return Doctrine\ORM\EntityManager
     * 		The doctrine entity manager.
     */
    protected function getEntityManager()
    {
        if ($this->em === NULL) {
            $doctrine = \Zend_Registry::get('doctrine');
            $this->em = $doctrine->getEntityManager();
        }
        return $this->em;
    }

    /* --------------------------------------------------------------- */

    /**
     * Provides aditional initialization for the service.
     */
    protected abstract function _init_();
}