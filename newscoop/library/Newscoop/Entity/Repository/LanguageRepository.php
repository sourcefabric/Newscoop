<?php
/**
 * @package Newscoop
 * @subpackage Languages
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository,
    Doctrine\ORM\QueryBuilder,
    Newscoop\Entity\Language;

/**
 * Language repository
 */
class LanguageRepository extends EntityRepository
{
    /**
     * Save language
     *
     * @param Newscoop\Entity\Language $language
     * @param array $values
     * @return void
     */
    public function save(Language $language, array $values)
    {
        $em = $this->getEntityManager();

        $language->setName($values['name']);
        $language->setNativeName($values['native_name']);
        $language->setCodePage($values['code_page']);
        $language->setCode($values['code']);

        $em->persist($language);
        $em->flush();
    }

    /**
     * Delete language
     *
     * @param int $id
     * @return void
     */
    public function delete($id)
    {
        $em = $this->getEntityManager();
        $proxy = $em->getReference('Newscoop\Entity\Language', $id);
        $em->remove($proxy);
        $em->flush();
    }

    /**
     * Get languages
     *
     * @return array
     */
    public function getLanguages()
    {
        $qb = $this->createQueryBuilder('l');

        return $qb->orderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}