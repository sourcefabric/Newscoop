<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;
use Newscoop\Entity\User;
use Newscoop\EventDispatcher\Events\GenericEvent;

/**
 * User service
 */
class UserAttributeService
{
    /** @var Doctrine\ORM\EntityManager */
    protected $em;

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
     * @param GenericEvent $event
     * @return void
     */
    public function update(GenericEvent $event)
    {
        $params = $event->getArguments();

        $attribute_name =  str_replace(".", "_", $event->getName());
        $user = $params['user'];

        if (is_int($user)) {
            $user_repo = $this->em->getRepository('Newscoop\Entity\User');
            $user = $user_repo->find($user);
        }

        if (empty($user)) {
            return;
        }

        $attribute_value = $user->getAttribute($attribute_name);
        $attribute_value = isset($attribute_value) ? ($attribute_value+1) : 1;

        $user->addAttribute($attribute_name, $attribute_value);

        $this->em->flush();
    }

    /**
     * Remove user attributes
     *
     * @param Newscoop\Entity\User $user
     * @param array $attributes
     *
     * @return void
     */
    public function removeAttributes(User $user, array $attributes)
    {
        foreach ($attributes as $attribute) {
            if ($entity = $user->removeAttribute($attribute)) {
                $this->em->remove($entity);
            }
        }

        $this->em->flush();
    }
}
