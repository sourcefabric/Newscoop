<?php
/**
 * @package Newscoop\Gimme
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Serializer\Article;

use JMS\Serializer\JsonSerializationVisitor;

/**
 * Create simple publication array from Newscoop\Entity\Publication object.
 */
class PublicationHandler
{
    public function serializeToJson(JsonSerializationVisitor $visitor, $publication, $type)
    {
        if (!$publication) {
            return null;
        }

        return array(
            'id' => $publication->getId(),
            'name' => $publication->getName()
        );
    }
}
