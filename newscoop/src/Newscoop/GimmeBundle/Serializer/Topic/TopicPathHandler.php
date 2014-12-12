<?php
/**
 * @package Newscoop\Gimme
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Serializer\Topic;

use JMS\Serializer\JsonSerializationVisitor;
use Doctrine\ORM\EntityManager;

/**
 * Generates path for given topic and display it in Topic object in API.
 * e.g. / root topic / subtopic 1 / subtopic 1-1
 */
class TopicPathHandler
{
    /**
     * Entity Manager
     * @var EntityManager
     */
    protected $em;

    /**
     * Construct
     * @param EntityManager $em Entity Manager
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function serializeToJson(JsonSerializationVisitor $visitor, $topic, $type)
    {
        if (!$topic) {
            return null;
        }

        $path = $this->em->getRepository('Newscoop\NewscoopBundle\Entity\Topic')->getReadablePath($topic);

        return $path;
    }
}
