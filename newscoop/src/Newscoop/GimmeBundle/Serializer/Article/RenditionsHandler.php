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
    protected $imageService;
    protected $zendRouter;
    protected $publicationAliasName;
    protected $renditionService;

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
            $articleRenditionImage = $this->renditionService->getArticleRenditionImage($data->number, $renditionName);

            $articleRenditionImage['original']->src = $this->publicationAliasName . $this->zendRouter->assemble(array(
                'src' => $articleRenditionImage['original']->src
            ), 'image');

            $media[] = array(
                'caption' => $renditionName,
                'type' => 'image',
                'link' => $this->publicationAliasName . $this->zendRouter->assemble(array(
                    'src' => $articleRenditionImage['src']
                ), 'image'),
                'details' => array(
                    'width' => $articleRenditionImage['width'],
                    'height' => $articleRenditionImage['height'],
                    'caption' => $articleRenditionImage['caption'],
                    'photographer' => $articleRenditionImage['photographer'],
                    'photographer_url' => $articleRenditionImage['photographer_url'],
                    'original' => $articleRenditionImage['original']
                )
            );
        }

        return $media;
    }
}
