<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Subscription;

use Doctrine\ORM\EntityRepository;
use Newscoop\Entity\User;

/**
 * Subscription repository
 */
class SubscriptionRepository extends EntityRepository
{
    /**
     * Save subscription
     *
     * @param Subscription $subscription
     * @param Newscoop\Entity\User\Subscriber $subscriber
     * @param array $values
     * @return void
     */
    public function save(Subscription $subscription, Subscriber $subscriber, array $values)
    {
        $em = $this->getEntityManager();

        $publication = $em->find('Newscoop\Entity\Publication', $values['publication']);

        $subscription->setType($values['type']);
        $subscription->setActive(!empty($values['active']));
        $subscription->setPublication($publication);
        $subscription->setSubscriber($subscriber);

        $em->persist($subscription);

        if (strtolower($values['sections']) == 'y') { // add sections
            $languages = array_map('intval', (array) $values['languages']);
            if ($values['language_set'] == 'select' && empty($languages)) {
                throw new \InvalidArgumentException('No languages specified');
            }

            foreach ($publication->getSections() as $section) {
                $subscriptionSection = new SubscriptionSection;

                if ($values['language_set'] == 'select' && !in_array($section->getLanguageId(), $languages)) {
                    continue; // ignore by language if any
                } elseif ($values['language_set'] == 'select') {
                    $subscriptionSection->setLanguage($section->getLanguage());
                }

                $subscriptionSection
                        ->setSubscription($subscription)
                        ->setSectionNumber($section->getNumber())
                        ->setStartDate(new \DateTime($values['start_date']))
                        ->setDays((int) $values['days'])
                        ->setPaidDays(in_array($values['type'], array('PN', 'T')) ? (int) $values['days'] : 0);

                $em->persist($subscriptionSection);
            }
        }
    }

    /**
     * Add section to subscription
     *
     * @param Newscoop\Entity\Subscription
     * @param array $values
     * @return void
     */
    public function addSections(Subscription $subscription, array $values)
    {
        $em = $this->getEntityManager();

        if ($values['language'] == 'select') {
            if (empty($values['sections_select'])) {
                throw new \InvalidArgumentException('No sections specified');
            }

            foreach ($values['sections_select'] as $num_lang) {
                list($num, $lang) = explode('_', $num_lang);

                $subscriptionSection = new SubscriptionSection;
                $subscriptionSection
                    ->setSubscription($subscription)
                    ->setSectionNumber($num)
                    ->setLanguage($em->getReference('Newscoop\Entity\Language', $lang))
                    ->setStartDate(new \DateTime($values['start_date']))
                    ->setDays($values['days'])
                    ->setPaidDays($values['days']);

                $em->persist($subscriptionSection);
            }
        } else {
            if (empty($values['sections_all'])) {
                throw new \InvalidArgumentException('No sections specified');
            }

            foreach ($values['sections_all'] as $num) {
                $subscriptionSection = new SubscriptionSection;
                $subscriptionSection
                    ->setSubscription($subscription)
                    ->setSectionNumber($num)
                    ->setStartDate(new \DateTime($values['start_date']))
                    ->setDays($values['days'])
                    ->setPaidDays($values['days']);

                $em->persist($subscriptionSection);
            }
        }
    }

    /**
     * Delete subscription
     *
     * @param Subscription $subscription
     * @return void
     */
    public function delete(Subscription $subscription)
    {
        $em = $this->getEntityManager();

        foreach ($subscription->getSections() as $section) {
            $em->remove($section);
        }

        $em->remove($subscription);
    }

    /**
     * Find by user
     *
     * @param Newscoop\Entity\User|int $user
     * @return array
     */
    public function findByUser($user)
    {
        if (empty($user)) {
            return array();
        }

        return $this->findBy(array(
            'user' => is_numeric($user) ? $user : $user->getId(),
        ), array('id' => 'desc'), 1000);
    }
}
