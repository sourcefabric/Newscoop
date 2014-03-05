<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Serializer\Author;  

use JMS\Serializer\JsonSerializationVisitor;

class ImageUriHandler
{
    protected $imageService;
    protected $zendRouter;
    protected $publicationAliasName;

    public function __construct($imageService, $zendRouter, $publicationService)
    {
        $this->imageService = $imageService;
        $this->zendRouter = $zendRouter;
        $this->publicationAliasName = $publicationService->getPublicationAlias()->getName();
    }

    public function serializeToJson(JsonSerializationVisitor $visitor, $data, array $type)
    {
        if (!$data->imageId) {
            return;
        }

        $image = $this->imageService->find($data->imageId);
        $imageSrc = $this->imageService->getSrc($image->getPath(), $image->getWidth(), $image->getHeight());
        $imageUri = $this->publicationAliasName . $this->zendRouter->assemble(array(
            'src' => $imageSrc
        ), 'image');

        return $imageUri;
    }
}