<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Implementation;

use Doctrine\ORM\EntityManager;
use Newscoop\Utils\Validation;
use Newscoop\Service\Resource\ResourceId;

/**
 * Provides the base services implementation for the themes.
 */
abstract class AEntityBaseServiceDoctrine
{
    const ALIAS = 'en';

    /* --------------------------------------------------------------- */

    /** @var Newscoop\Service\Resource\ResourceId */
    protected $id;
    /** @var Doctrine\ORM\EntityManager */
    protected $em = NULL;

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
            throw new \Exception("Cannot locate '$this->entityClassName' for id '$id'.");
        }
        return $entity;
    }

    function findById($id)
    {
        Validation::notEmpty($id, 'id');
        $em = $this->getManager();
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
    protected function getManager()
    {
        if ($this->em === NULL) {
            $this->em = \Zend_Registry::get('container')->getService('em');
        }
        return $this->em;
    }

    /* --------------------------------------------------------------- */

    /**
     * Provides aditional initialization for the service.
     */
    protected abstract function _init_();
}