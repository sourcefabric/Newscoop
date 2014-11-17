<?php
/**
 * @package Newscoop\Gimme
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Serializer\Article;

use JMS\Serializer\JsonSerializationVisitor;
use Newscoop\Image\ImageService;
use Newscoop\Services\PublicationService;

/**
 * Create simple user array from Newscoop\Entity\User object.
 */
class UserHandler
{
    /**
     * Image Service
     * @var ImageService
     */
    protected $imagesService;

    /**
     * Publicatio Service
     * @var PublicatioService
     */
    protected $publicationService;

    /**
     * Images config
     * @var array
     */
    protected $imageConfig;

    /**
     * Construct
     *
     * @param ImageService       $imagesService      Image service
     * @param PublicationService $publicationService Publication service
     * @param array              $imageConfig        Images config
     */
    public function __construct(ImageService $imagesService, PublicationService $publicationService, $imageConfig)
    {
        $this->imagesService = $imagesService;
        $this->publicationService = $publicationService;
        $this->imageConfig = $imageConfig;
    }

    /**
     * Serialize object to JSON
     *
     * @param JsonSerializationVisitor $visitor JsonSerializationVisitor object
     * @param Newscoop\Entity\User     $user    User object
     * @param array                    $type    Serialized object type
     *
     * @return array Simple array with data of user
     */
    public function serializeToJson(JsonSerializationVisitor $visitor, $user, $type)
    {
        if (!$user) {
            return null;
        }

        return array(
            'id' => $user->getId(),
            'realname' => $user->getRealName(),
            'username' => $user->getUsername(),
            'image' => $this->publicationService->getPublicationAlias()->getName() . '/' . $this->imageConfig['cache_url'] . '/' . $this->imagesService->getSrc('images/' . $user->getImage(), 120, 120),
            'email' => $user->getEmail(),
        );
    }
}
