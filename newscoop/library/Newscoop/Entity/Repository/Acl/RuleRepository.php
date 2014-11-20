<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository\Acl;

use Doctrine\ORM\EntityRepository;
use Newscoop\Entity\Acl\Rule;

/**
 * Acl Rule repository
 */
class RuleRepository extends EntityRepository
{
    /**
     * Save rule
     *
     * @param array $values
     * @param bool $isUser
     * @param \Newscoop\Entity\Acl\Rule|null $rule
     * @return \Newscoop\Entity\Acl\Rule|null
     */
    public function save(array $values, $isUser = false, Rule $rule = null)
    {
        $role = $this->getEntityManager()->getReference('Newscoop\Entity\Acl\Role', (int) $values['role']);
        $resource = (string) $values['resource'];
        $action = (string) $values['action'];
        $type = array_key_exists('type', $values) && strtolower($values['type']) == 'allow' ? 'allow' : 'deny';

        $conflicts = $this->findBy(array(
            'role' => (int) $values['role'],
            'resource' => $resource,
            'action' => $action,
        ));

        foreach ($conflicts as $conflict) {
            $this->getEntityManager()->remove($conflict);
        }

        $this->getEntityManager()->flush();

        if ('deny' == $type && !$isUser) { // don't add deny rules for user groups
            return;
        }

        if (null === $rule) {
            $rule = new Rule();
        }

        $rule->setType($values['type']);
        $rule->setRole($role);
        $rule->setResource($resource);
        $rule->setAction($action);

        $this->getEntityManager()->persist($rule);
        $this->getEntityManager()->flush();

        return $rule;
    }

    /**
     * Delete rule
     *
     * @param int $id
     * @return void
     */
    public function delete($id)
    {
        $proxy = $this->getEntityManager()->getReference('Newscoop\Entity\Acl\Rule', $id);
        $this->getEntityManager()->remove($proxy);
    }
}
