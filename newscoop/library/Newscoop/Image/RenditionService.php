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
     * @param int $articleNumber
     * @param Newscoop\Image\Rendition $rendition
     * @param Newscoop\Image\ImageInterface $image
     * @return Newscoop\Image\ArticleRendition
     */
    public function setArticleRendition($articleNumber, Rendition $rendition, ImageInterface $image)
    {
        if ($image->getWidth() < $rendition->getWidth() || $image->getHeight() < $rendition->getHeight()) {
            throw new \InvalidArgumentException("Image too small.");
        }

        $old = $this->getArticleRendition($articleNumber, $rendition);
        if ($old !== null) {
            $this->orm->remove($old);
            $this->orm->flush($old);
        }

        $articleRendition = new ArticleRendition($articleNumber, $rendition, $image);
        $this->orm->persist($articleRendition);
        $this->orm->flush($articleRendition);
        return $articleRendition;
    }

    /**
     * Unset article rendition
     *
     * @param int $articleNumber
     * @param string $rendition
     * @return void
     */
    public function unsetArticleRendition($articleNumber, $rendition)
    {
        $articleRendition = $this->getArticleRendition($articleNumber, $rendition);
        if ($articleRendition !== null) {
            $this->orm->remove($articleRendition);
            $this->orm->flush($articleRendition);
        }
    }

    /**
     * Get article rendition
     *
     * @param int $articleNumber
     * @param string $rendition
     * @return Newscoop\Image\ArticleRendition
     */
    private function getArticleRendition($articleNumber, $rendition)
    {
        try {
            return $this->orm->getRepository('Newscoop\Image\ArticleRendition')->findOneBy(array(
                'articleNumber' => (int) $articleNumber,
                'renditionName' => (string) $rendition,
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
            $articleRenditions = $this->orm->getRepository('Newscoop\Image\ArticleRendition')->findBy(array(
                'articleNumber' => (int) $articleNumber,
            ));
        } catch (\Exception $e) {
            $this->createSchemaIfMissing($e);
            $articleRenditions = array();
        }

        $defaultArticleImage = $this->imageService->getDefaultArticleImage($articleNumber);
        return new ArticleRenditionCollection($articleNumber, $articleRenditions, $defaultArticleImage ? $defaultArticleImage->getImage() : null);
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
                    $this->orm->getClassMetadata('Newscoop\Image\ArticleRendition'),
                ));
            } catch (\Exception $e) { // ignore possible errors - foreign key to Images table
            }
        }
    }
}
