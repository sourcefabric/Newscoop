<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;
use Newscoop\Entity\Acl\Rule;

/**
 * Notification Service
 */
class NotificationService
{
    const GROUP_DQL = "
        SELECT user.email
        FROM Newscoop\Entity\User user
            JOIN user.groups group
            JOIN group.role role
            JOIN role.rules rule
        WHERE rule.type = :type
            AND rule.resource = :resource
            AND rule.action = :action
    ";

    const USER_DQL = "
        SELECT user.email
        FROM Newscoop\Entity\User user
            JOIN user.groups group
            JOIN group.role role
            JOIN role.rules rule
        WHERE rule.type = :type
            AND rule.resource = :resource
            AND rule.action = :action
    ";

    /**
     * @var array
     */
    protected $params = array(
        'resource' => 'notification',
        'action' => 'get',
    );

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Find emails from users who should recieve notification
     *
     * @return array
     */
    public function findRecipients()
    {
        $emails = array_merge($this->findByGroup(), $this->findByUser());
        return array_unique($emails);
    }

    /**
     * Find emails allowed by group role but those denied by user role
     *
     * @return array
     */
    private function findByGroup()
    {
        return array_diff(
            $this->executeQuery(self::GROUP_DQL, Rule::ALLOW),
            $this->executeQuery(self::USER_DQL, Rule::DENY)
        );
    }

    /**
     * Find emails allowed by user role
     *
     * @return array
     */
    private function findByUser()
    {
        return $this->executeQuery(self::USER_DQL, Rule::ALLOW);
    }

    /**
     * Execute given dql query
     *
     * @param string $dql
     * @param string $type
     * @return array
     */
    private function executeQuery($dql, $type)
    {
        $query = $this->em->createQuery($dql);
        $query->setParameters($this->params);
        $query->setParameter('type', $type);
        return array_map(function (array $row) {
            return $row['email'];
        }, $query->getResult());
    }
}

