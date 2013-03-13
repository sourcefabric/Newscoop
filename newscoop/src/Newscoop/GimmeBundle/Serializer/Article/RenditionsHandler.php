<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Serializer\Article;  

use JMS\SerializerBundle\Serializer\VisitorInterface;
use JMS\SerializerBundle\Serializer\Handler\SerializationHandlerInterface;

/**
 * Create Renditions array for Newscoop\Entity\Article resource.
 */
class RenditionsHandler implements SerializationHandlerInterface
{
    private $imageService;
    private $zendRouter;
    private $publicationAliasName;
    private $renditionService;

    public function __construct($imageService, $zendRouter, $publicationService, $renditionService)
    {
        $this->imageService = $imageService;
        $this->zendRouter = $zendRouter;
        $this->publicationAliasName = $publicationService->getPublicationAlias()->getName();
        $this->renditionService = $renditionService;
    }

    public function serialize(VisitorInterface $visitor, $data, $type, &$visited)
    {   
        if ($type != 'Newscoop\\Entity\\Article') {
            return;
        }

        $articleRenditions = $this->renditionService->getArticleRenditions($data->getNumber());
        $renditions = $this->renditionService->getRenditions();
        $media = array();
        
        foreach ($renditions as $renditionName => $rendition) {

            if (!$articleRenditions->offsetExists($rendition, true)) {
                continue;
            }

            $image = $this->imageService->find($articleRenditions[$rendition]->getImage()->getId());
            $imageSrc = $this->imageService->getSrc($image->getPath(), $rendition->getWidth(), $rendition->getHeight());
            $imageUri = $this->publicationAliasName . $this->zendRouter->assemble(array(
                'src' => $imageSrc
            ), 'image');

            $media[] = array(
                'caption' => $renditionName,
                'type' => 'image',
                'link' => $imageUri
            );
        }

        $data->setRenditions($media);
    }
}