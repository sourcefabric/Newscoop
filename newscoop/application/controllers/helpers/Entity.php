<?php

use Doctrine\ORM\EntityManager;

/**
 * Entity action helper
 */
class Action_Helper_Entity extends Zend_Controller_Action_Helper_Abstract
{
    /** @var Doctrine\ORM\EntityManager */
    private $em;

    /**
     * Init Entity manager
     */
    public function init()
    {
        return $this->getManager();
    }

    /**
     * Set entity manager
     *
     * @param Doctrine\ORM\EntityManager
     * @return Action_Helper_Entity
     */
    public function setManager(EntityManager $em)
    {
        $this->em = $em;
        return $this;
    }

    /**
     * Get entity manager
     *
     * @return Doctrine\ORM\EntityManager
     */
    public function getManager()
    {
        if ($this->em === NULL) {
            $controller = $this->getActionController();
            $bootstrap = $controller->getInvokeArg('bootstrap');
            $doctrine = $bootstrap->getResource('doctrine');
            $this->setManager($doctrine->getEntityManager());
        }

        return $this->em;
    }

    /**
     * Get entity repository
     *
     * @params mixed $entity
     * @return Doctrine\ORM\EntityRepository
     */
    public function getRepository($entity)
    {
        return $this->getManager()->getRepository($this->getClassName($entity));
    }

    /**
     * Flush entity manager
     *
     * @return void
     */
    public function flushManager()
    {
        $this->getManager()->flush();
    }

    /**
     * Get entity by parameter
     *
     * @param mixed $entity
     * @param string $key
     * @param bool $throw
     * @return object|NULL
     */
    public function get($entity, $key = 'id', $throw = TRUE)
    {
        $params = $this->getRequest()->getParams();
        if (!isset($params[$key])) {
            if ($throw) {
                throw new InvalidArgumentException;
            }

            return NULL;
        }

        $match = $this->getManager()->find($this->getClassName($entity), $params[$key]);
        if (!$match) {
            if ($throw) {
                throw new InvalidArgumentException;
            }

            return NULL;
        }

        return $match;
    }

    /**
     * Get entity name
     *
     * @param mixed $entity
     * @return string
     */
    private function getClassName($entity)
    {
        return is_object($entity) ?
            get_class($entity) : (string) $entity;
    }
}
