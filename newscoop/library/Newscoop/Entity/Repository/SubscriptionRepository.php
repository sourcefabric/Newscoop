<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository,
    Newscoop\Entity\Subscription,
    Newscoop\Entity\User\Subscriber,
    Newscoop\Entity\SubscriptionSection;

/**
 * Subscription repository
 */
class SubscriptionRepository extends EntityRepository
{
    /**
     * Save subscription
     *
     * @param Newscoop\Entity\Subscription $subscription
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
            foreach ($publication->getSections() as $section) {
                if (!empty($languages) && !in_array($section->getLanguageId(), $languages)) {
                    continue; // ignore by language if any
                }

                $subscriptionSection = new SubscriptionSection;
                $subscriptionSection
                        ->setSubscription($subscription)
                        ->setSection($section)
                        ->setStartDate(new \DateTime($values['start_date']))
                        ->setDays((int) $values['days'])
                        ->setPaidDays(in_array($values['type'], array('PN', 'T')) ? (int) $values['days'] : 0);

                if (!empty($langauges)) {
                    $subscriptionSection->setLanguage($section->getLanguage());
                }

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
            foreach ($values['sections_select'] as $num_lang) {
                list($num, $lang) = explode('_', $num_lang);

                $subscriptionSection = new SubscriptionSection;
                $subscriptionSection
                    ->setSubscription($subscription)
                    ->setSection($em->getReference('Newscoop\Entity\Section', $num))
                    ->setLanguage($em->getReference('Newscoop\Entity\Language', $lang))
                    ->setStartDate(new \DateTime($values['start_date']))
                    ->setDays($values['days'])
                    ->setPaidDays($values['days']);

                $em->persist($subscriptionSection);
            }
        } else {
            foreach ($values['sections_all'] as $num) {
                $subscriptionSection = new SubscriptionSection;
                $subscriptionSection
                    ->setSubscription($subscription)
                    ->setSection($em->getReference('Newscoop\Entity\Section', $num))
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
     * @param Newscoop\Entity\Subscription $subscription
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
}
