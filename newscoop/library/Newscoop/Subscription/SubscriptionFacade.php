<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Subscription;

use Newscoop\Entity\User;

/**
 * Subscription Facade
 */
class SubscriptionFacade
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var Newscoop\Subscription\SubscriptionRepository
     */
    protected $repository;

    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
        $this->repository = $this->em->getRepository('Newscoop\Subscription\Subscription');
    }

    /**
     * Find subscriptions by user
     *
     * @param Newscoop\Entity\User|int $user
     * @return array
     */
    public function findByUser($user)
    {
        return $this->repository->findByUser($user);
    }

    /**
     * Save subscription
     *
     * @param array $values
     * @param null|Subscription $subscription
     * @return Subscription
     */
    public function save(array $values, Subscription $subscription = null)
    {
        if ($subscription === null) {
            $subscription = new Subscription();
            $this->em->persist($subscription);
        }

        if (array_key_exists('user', $values)) {
            $subscription->setUser(is_numeric($values['user']) ? $this->em->getReference('Newscoop\Entity\User', $values['user']) : $values['user']);
        }

        if (array_key_exists('publication', $values)) {
            $subscription->setPublication(is_numeric($values['publication']) ? $this->em->getReference('Newscoop\Entity\Publication', $values['publication']) : $values['publication']);
        }

        if (array_key_exists('type', $values)) {
            $subscription->setType($values['type']);
        }

        if (array_key_exists('active', $values)) {
            $subscription->setActive($values['active']);
        }

        if (array_key_exists('currency', $values)) {
            $subscription->setCurrency($values['currency']);
        }

        if (array_key_exists('toPay', $values)) {
            $subscription->setToPay($values['toPay']);
        }

        if (array_key_exists('add_sections', $values) && $values['add_sections']) {
            if (!array_key_exists('publication', $values)) {
                throw new \InvalidArgumentException("No publication provided for adding sections");
            }

            $publication = is_numeric($values['publication']) ? $this->em->getRepository('Newscoop\Entity\Publication')->find($values['publication']) : $values['publication'];
            $subscription->addSections($values, $publication);
        }

        $this->em->flush();
        return $subscription;
    }

    /**
     * Delete subscription
     *
     * @return void
     */
    public function delete($id)
    {
        $this->em->remove($this->repository->find((int) $id));
        $this->em->flush();
    }

    /**
     * Find subscription by given id
     *
     * @param int $id
     * @return Newscoop\Subscription\Subscription
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }
}
