<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository,
    Newscoop\Entity\AuditEvent;

/**
 * Audit repository.
 */
class AuditRepository extends EntityRepository
{
    public function save(AuditEvent $event, array $values)
    {
        $em = $this->getEntityManager();

        $event->setResourceId($values['resource_id']);
        $event->setResourceType($values['resource_type']);
        $event->setResourceTitle($values['resource_title']);
        $event->setResourceDiff($values['resource_diff']);
        $event->setAction($values['action']);

        if (!empty($values['user'])) {
            $user = is_int($values['user']) ? $em->getReference('Newscoop\Entity\User', $values['user']) : $values['user'];
            $event->setUser($user);
        }

        $em->persist($event);
        $em->flush();
    }
}
