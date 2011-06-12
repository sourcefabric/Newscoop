<?php

use Doctrine\ORM\EntityManager;

/**
 * Entity manager action helper
 */
class Action_Helper_Em extends Zend_Controller_Action_Helper_Abstract
{
    /** @var Doctrine\ORM\EntityManager */
    private $em = NULL;

    /**
     * Init
     */
    public function init()
    {
        return $this->getEntityManager();
    }

    /**
     * Set entity manager
     *
     * @param Doctrine\ORM\EntityManager
     * @return Action_Helper_Em
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
        return $this;
    }

    /**
     * Get entity manager
     *
     * @return Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if ($this->em === NULL) {
            $controller = $this->getActionController();
            $bootstrap = $controller->getInvokeArg('bootstrap');
            $doctrine = $bootstrap->getResource('doctrine');
            $this->setEntityManager($doctrine->getEntityManager());
        }

        return $this->em;
    }

    /**
     * Get entity repository
     *
     * @params string $entity
     * @return Doctrine\ORM\EntityRepository
     */
    public function getRepository($entity)
    {
        return $this->getEntityManager()->getRepository((string) $entity);
    }
}
