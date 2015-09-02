<?php

/**
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Serializer\Image;

use JMS\Serializer\JsonSerializationVisitor;

class ThumbnailUriHandler
{
    protected $linkService;

    public function __construct($linkService)
    {
        $this->linkService = $linkService;
    }

    public function serializeToJson(JsonSerializationVisitor $visitor, $item, $type)
    {
        if ($item->getThumbnailPath()) {
            return $this->linkService->getBaseUrl('/'.$item->getThumbnailPath());
        }
    }
}
