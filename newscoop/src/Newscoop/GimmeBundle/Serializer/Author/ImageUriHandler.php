<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Serializer\Author;  

use JMS\SerializerBundle\Serializer\VisitorInterface;
use JMS\SerializerBundle\Serializer\Handler\SerializationHandlerInterface;

/**
 * Create simple Author object from Newscoop\Entity\Author object.
 */
class ImageUriHandler implements SerializationHandlerInterface
{
    private $imageService;
    private $zendRouter;
    private $publicationAliasName;

    public function __construct($imageService, $zendRouter, $publicationService)
    {
        $this->imageService = $imageService;
        $this->zendRouter = $zendRouter;
        $this->publicationAliasName = $publicationService->getPublicationAlias()->getName();
    }

    public function serialize(VisitorInterface $visitor, $data, $type, &$visited)
    {   
        if ($type != 'Newscoop\\Entity\\Author') {
            return;
        }

        if (!$data->getImage()) {
            return;
        }

        $image = $this->imageService->find($data->getImage());
        $imageSrc = $this->imageService->getSrc($image->getPath(), $image->getWidth(), $image->getHeight());
        $imageUri = $this->publicationAliasName . $this->zendRouter->assemble(array(
            'src' => $imageSrc
        ), 'image');

        $data->setImage($imageUri);
    }
}