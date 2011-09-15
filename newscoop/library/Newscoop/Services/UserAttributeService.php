<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager,
    Newscoop\Entity\UserAttribute;

/**
 * User service
 */
class UserAttributeService
{
    /** @var Doctrine\ORM\EntityManager */
    private $em;

    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    /**
     * Receives notifications of points events.
     *
     * @param sfEvent $event
     * @return void
     */
    public function update(\sfEvent $event)
    {
        $params = $event->getParameters();

        $attribute_name =  str_replace(".", "_", $event->getName());
        $user = $params['user'];

        $attribute_value = $user->getAttribute($attribute_name);
        $attribute_value = isset($attribute_value) ? ($attribute_value+1) : 1;

        $user->addAttribute($attribute_name, $attribute_value);

        $this->em->flush();
    }
}