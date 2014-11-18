<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Author repository
 */
class AutoIdRepository extends EntityRepository
{

    public function getNextTranslationPhraseId()
    {
        $em = $this->getEntityManager();
        $result = $em->getRepository('Newscoop\Entity\AutoId')->findAll();
        $autoId = $result[0];

        $autoId->setTranslationPhraseId($autoId->getTranslationPhraseId()+1);
        $em->flush();

        return $autoId->getTranslationPhraseId();
    }
}
