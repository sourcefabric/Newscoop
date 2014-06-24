<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Serializer\Image;

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
        if (!property_exists($data, 'imageId') && !property_exists($data, 'image')) {
            return;
        }

        if (property_exists($data, 'image') && is_string($data->image)) {
            $imageUri = $this->publicationAliasName . $this->zendRouter->assemble(array(
                'controller' => 'images',
                'action' => null
            )).'/'.$data->image;

            return $imageUri;
        } elseif (property_exists($data, 'imageId')) {
            $image = $this->imageService->find($data->imageId);
            $imageSrc = $this->imageService->getSrc($image->getPath(), $image->getWidth(), $image->getHeight());
            $imageUri = $this->publicationAliasName . $this->zendRouter->assemble(array(
                'src' => $imageSrc
            ), 'image');

            return $imageUri;
        } elseif (is_object($data->image)) {
            $image = $data->image;
            $imageSrc = $this->imageService->getSrc($image->getPath(), $image->getWidth(), $image->getHeight());
            $imageUri = $this->publicationAliasName . $this->zendRouter->assemble(array(
                'src' => $imageSrc
            ), 'image');

            return $imageUri;
        }
    }
}
