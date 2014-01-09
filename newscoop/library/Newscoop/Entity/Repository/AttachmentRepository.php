<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Attachment repository
 */
class AttachmentRepository extends EntityRepository
{
    public function getDescription($attachmentId)
    {
        $em = $this->getEntityManager();

        $attachment = $em->getRepository('Newscoop\Entity\Attachment')
            ->findOneById($attachmentId);

        $description = $em->getRepository('Newscoop\Entity\Translation')
            ->findBy(array(
                'phraseId' => $attachment->getDescriptionId(),
                'language' => $attachment->getLanguage()
            ));

        return $description;
    }
}
