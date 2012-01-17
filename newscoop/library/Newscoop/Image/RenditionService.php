<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

/**
 * Rendition Service
 */
class RenditionService
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $orm;

    /**
     * @var Newscoop\Image\ImageService
     */
    protected $imageService;

    /**
     * @param Doctrine\ORM\EntityManager $orm
     */
    public function __construct(\Doctrine\ORM\EntityManager $orm, ImageService $imageService)
    {
        $this->orm = $orm;
        $this->imageService = $imageService;
    }

    /**
     * Set article rendition
     *
     * @param Newscoop\Image\Rendition $rendition
     * @param Newscoop\Image\ArticleImage $image
     * @return Newscoop\Image\ArticleImageRendition
     */
    public function setRenditionImage(Rendition $rendition, ArticleImage $image)
    {
        $old = $this->getRenditionImage($rendition, $image->getArticleNumber());
        if ($old !== null) {
            $this->orm->remove($old);
            $this->orm->flush($old);
        }

        $articleImageRendition = new ArticleImageRendition($image, $rendition);
        $this->orm->persist($articleImageRendition);
        $this->orm->flush($articleImageRendition);
        return $articleImageRendition;
    }

    /**
     * Get article rendition
     *
     * @param Newscoop\Image\Rendition $rendition
     * @param int $articleNumber
     * @return Newscoop\Image\ArticleImageRendition
     */
    private function getRenditionImage(Rendition $rendition, $articleNumber)
    {
        try {
            return $this->orm->getRepository('Newscoop\Image\ArticleImageRendition')->findOneBy(array(
                'rendition' => (string) $rendition,
                'articleNumber' => (int) $articleNumber,
            ));
        } catch (\Exception $e) {
            $this->createSchemaIfMissing($e);
            return null;
        }
    }

    /**
     * Get article renditions
     *
     * @param int $articleNumber
     * @return array
     */
    public function getArticleRenditions($articleNumber)
    {
        try {
            $articleRenditions = $this->orm->getRepository('Newscoop\Image\ArticleImageRendition')->findBy(array(
                'articleNumber' => (int) $articleNumber,
            ));
        } catch (\Exception $e) {
            $this->createSchemaIfMissing($e);
            $articleRenditions = array();
        }

        return new ArticleRenditionCollection($articleNumber, $articleRenditions, $this->imageService->getDefaultArticleImage($articleNumber));
    }


    /**
     * Create schema for article rendition
     *
     * @param Exception $e
     * @return void
     */
    private function createSchemaIfMissing(\Exception $e)
    {
        if ($e->getCode() === '42S02') {
            try {
                $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->orm);
                $schemaTool->createSchema(array(
                    $this->orm->getClassMetadata('Newscoop\Image\ArticleImageRendition'),
                ));
            } catch (\Exception $e) { // ignore possible errors - foreign key to Images table
            }
        }
    }
}
