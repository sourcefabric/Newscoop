<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Serializer\Article;  

use JMS\Serializer\JsonSerializationVisitor;

/**
 * Create Renditions array for Newscoop\Entity\Article resource.
 */
class RenditionsHandler
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

    public function serializeToJson(JsonSerializationVisitor $visitor, $data, $type)
    {
        $articleRenditions = $this->renditionService->getArticleRenditions($data->number);
        $renditions = $this->renditionService->getRenditions();
        $media = array();

        if (count($renditions) == 0) {
            return null;
        }

        foreach ($renditions as $renditionName => $rendition) {
            if (!$articleRenditions->offsetExists($rendition)) {
                continue;
            }

            $image = $this->imageService->find($articleRenditions[$rendition]->getImage()->getId());
            $imageSrc = $this->imageService->getSrc($image->getPath(), $rendition->getWidth(), $rendition->getHeight(), 'crop');
            $imageUri = $this->publicationAliasName . $this->zendRouter->assemble(array(
                'src' => $imageSrc
            ), 'image');

            $media[] = array(
                'caption' => $renditionName,
                'type' => 'image',
                'link' => $imageUri
            );
        }

        return $media;
    }
}
