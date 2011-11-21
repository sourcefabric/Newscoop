<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop;

use Doctrine\Common\EventSubscriber,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Events,
    Doctrine\ORM\Event\LifecycleEventArgs,
    Doctrine\ORM\Event\PreUpdateEventArgs;

/**
 * Doctrine Event Dispatcher Proxy dispatches sfEvents on certain doctrine events.
 */
class DoctrineEventDispatcherProxy implements EventSubscriber
{
    /** @var sfEventDispatcher */
    private $dispatcher;

    /** @var array */
    private $events = array();

    /**
     * @param sfEventDispatcher $dispatcher
     */
    public function __construct(\sfEventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Get subscribed doctrine orm events.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::postPersist,
            //Events::preUpdate, @todo temporary fix for CS-3817
            Events::postUpdate,
            Events::preRemove,
        );
    }

    /**
     * Dispatch event.create on postPersist.
     *
     * @param Doctrine\ORM\Event\LifecycleEventArgs $args
     * @return void
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entityName = $this->getEntityName($args->getEntity());
        $this->dispatcher->notify(new \sfEvent($this, "{$entityName}.create", array(
            'id' => $this->getEntityId($args->getEntity(), $args->getEntityManager()),
            'title' => $this->getEntityTitle($args->getEntity()),
        )));
    }

    /**
     * Dispatch entity.update on preUpdate.
     *
     * @param Doctrine\ORM\Event\PreUpdateEventArgs $args
     * @return void
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entityName = $this->getEntityName($args->getEntity());
        $this->events[] = new \sfEvent($args->getEntity(), "{$entityName}.update", array(
            'id' => $this->getEntityId($args->getEntity(), $args->getEntityManager()),
            'diff' => $args->getEntityChangeSet(),
            'title' => $this->getEntityTitle($args->getEntity()),
        ));
    }

    /**
     * Dispatch entity.update on preUpdate.
     *
     * @param Doctrine\ORM\Event\LifecycleEventArgs $args
     * @return void
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        foreach ($this->events as $event) {
            $this->dispatcher->notify($event);
        }
    }

    /**
     * Dispatch entity.delete on preRemove.
     *
     * @param Doctrine\ORM\Event\LifecycleEventArgs $args
     * @return void
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entityName = $this->getEntityName($args->getEntity());
        $this->dispatcher->notify(new \sfEvent($this, "{$entityName}.delete", array(
            'id' => $this->getEntityId($args->getEntity(), $args->getEntityManager()),
            'diff' => $this->getEntityProperties($args->getEntity(), $args->getEntityManager()),
            'title' => $this->getEntityTitle($args->getEntity()),
        )));
    }

    /**
     * Get entity name.
     *
     * @param object $entity
     * @return string
     */
    private function getEntityName($entity)
    {
        $class = str_replace('Newscoop\Entity\\', '', get_class($entity));
        return strtolower(implode('-', explode('\\', $class)));
    }

    /**
     * Get entity properties.
     *
     * @param object $entity
     * @param Doctrine\ORM\EntityManager $em
     * @return array
     */
    private function getEntityProperties($entity, EntityManager $em)
    {
        $properties = array();
        $meta = $em->getClassMetadata(get_class($entity));
        foreach ($meta->getReflectionProperties() as $property) {
            $value = $meta->getFieldValue($entity, $property->name);
            if (!empty($value)) {
                $properties[$property->name] = $value;
            }
        }

        return $properties;
    }

    /**
     * Get entity id.
     *
     * @param object $entity
     * @param Doctrine\ORM\EntityManager $em
     * @return mixed
     */
    private function getEntityId($entity, EntityManager $em)
    {
        $meta = $em->getClassMetadata(get_class($entity));
        return $meta->getIdentifierValues($entity);
    }

    /**
     * Get entity title.
     *
     * @param object $entity
     * @return string
     */
    private function getEntityTitle($entity)
    {
        static $nameMethods = array('__toString', 'getTitle', 'getName');
        foreach ($nameMethods as $method) {
            if (method_exists($entity, $method)) {
                return $entity->$method();
            }
        }

        return '';
    }
}
