<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Subscription;

/**
 * Section Facade
 */
class SectionFacade
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var Newscoop\Subscription\SectionRepository
     */
    protected $repository;

    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
        $this->repository = $this->em->getRepository('Newscoop\Subscription\Section');
    }

    /**
     * Find section
     *
     * @param int $id
     * @return Newscoop\Subscription\Section
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Save section
     *
     * @param array $values
     * @param null|Section $section
     * @return Section
     */
    public function save(array $values, Section $section = null)
    {
        if ($section === null) {
            if (!array_key_exists('subscription', $values)) {
                throw new \InvalidArgumentException("Subscription must be provided.");
            }

            if (!array_key_exists('section', $values)) {
                throw new \InvalidArgumentException("Section must be provided.");
            }

            $section = new Section($this->getSubscription($values), $this->getSectionNumber($values));
            $this->em->persist($section);

            if (!empty($values['language'])) {
                $section->setLanguage($this->getLanguage($values));
            }
        }

        $section->setStartDate(new \DateTime($values['startDate']));
        $section->setDays($values['days']);
        $section->setPaidDays($values['paidDays']);

        $this->em->flush();
        return $section;
    }

    /**
     * Get subscription
     *
     * @param array $values
     * @return Subscription
     */
    private function getSubscription(array $values)
    {
        return is_numeric($values['subscription']) ? $this->em->find('Newscoop\Subscription\Subscription', $values['subscription']) : $values['subscription'];
    }

    /**
     * Get section number
     *
     * @param array $values
     * @return int
     */
    private function getSectionNumber(array $values)
    {
        return is_array($values['section']) ? $values['section']['number'] : $values['section']->getNumber();
    }

    /**
     * Get language
     *
     * @param array $values
     * @return \Newscoop\Entity\Language
     */
    private function getLanguage(array $values)
    {
        return is_array($values['language']) ? $this->em->find('Newscoop\Entity\Language', $values['language']['id']) : $values['language'];
    }

    /**
     * Delete section
     *
     * @param int $id
     * @return void
     */
    public function delete($id)
    {
        $this->em->remove($this->em->getReference('Newscoop\Subscription\Section', $id));
        $this->em->flush();
    }
}
