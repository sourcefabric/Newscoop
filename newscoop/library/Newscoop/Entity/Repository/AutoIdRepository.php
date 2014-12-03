<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Author repository
 */
class AutoIdRepository extends EntityRepository
{

    /**
     * Get next translations phrase Id.
     *
     * @return integer
     */
    public function getNextTranslationPhraseId()
    {
        $em = $this->getEntityManager();
        $result = $em->getRepository('Newscoop\Entity\AutoId')->findAll();
        $autoId = $result[0];

        $autoId->setTranslationPhraseId($autoId->getTranslationPhraseId()+1);
        $em->flush();

        return $autoId->getTranslationPhraseId();
    }

    /**
     * Get next article number
     *
     * @return integer
     */
    public function getNextArticleNumber()
    {
        $em = $this->getEntityManager();
        $result = $em->getRepository('Newscoop\Entity\AutoId')->findAll();
        $autoId = $result[0];

        $autoId->setArticleId($autoId->getArticleId()+1);
        $em->flush();

        return $autoId->getArticleId();
    }
}
