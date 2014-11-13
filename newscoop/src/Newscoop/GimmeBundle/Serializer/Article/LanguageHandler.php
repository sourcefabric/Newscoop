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
 * Create simple language array from Newscoop\Entity\Language object.
 */
class LanguageHandler
{
    public function serializeToJson(JsonSerializationVisitor $visitor, $language, $type)
    {
        if (!$language) {
            return null;
        }

        return array(
            'id' => $language->getId(),
            'name' => $language->getName(),
            'code' => $language->getCode(),
            'RFC3066bis' => $language->getRFC3066bis(),
        );
    }
}
