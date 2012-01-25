<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

/**
 * Image Service
 */
class ImageService
{
    /**
     * @var array
     */
    private $config = array();

    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $orm;

    /**
     * @var array
     */
    private $supportedTypes = array(
        'image/jpeg',
        'image/png',
        'image/gif',
    );

    /**
     * @param array $config
     * @param Doctrine\ORM\EntityManager $orm
     */
    public function __construct(array $config, \Doctrine\ORM\EntityManager $orm)
    {
        $this->config = $config;
        $this->orm = $orm;
    }

    /**
     * Get image src
     *
     * @param string $image
     * @param int $width
     * @param int $height
     * @param string $specs
     * @return string
     */
    public function getSrc($image, $width, $height, $specs = 'fit')
    {
        return implode('/', array(
            "{$width}x{$height}",
            $specs,
            $this->encodePath($image),
        ));
    }

    /**
     * Generate image for given src
     *
     * @param string $src
     * @return void
     */
    public function generateFromSrc($src)
    {
        $matches = array();
        if (!preg_match('#^([0-9]+)x([0-9]+)/([_a-z0-9]+)/([-_.~%a-zA-Z0-9]+)$#', $src, $matches)) {
            return;
        }

        list(, $width, $height, $specs, $imagePath) = $matches;

        $destFolder = rtrim($this->config['cache_path'], '/') . '/' . dirname(ltrim($src, './'));
        if (!realpath($destFolder)) {
            mkdir($destFolder, 0755, true);
        }

        if (!is_dir($destFolder)) {
            throw new \RuntimeException("Can't create folder '$destFolder'.");
        }

        $rendition = new Rendition($width, $height, $specs);
        $image = $rendition->generateImage($this->decodePath($imagePath));
        $image->save($destFolder . '/' . $imagePath);
        $image->send();
    }

    /**
     * Save image
     *
     * @param array $info
     * @return string
     */
    public function save(array $info)
    {
        if (!in_array($info['type'], $this->supportedTypes)) {
            throw new \InvalidArgumentException("Unsupported image type '$info[type]'.");
        }

        $name = sha1_file($info['tmp_name']) . '.' . array_pop(explode('.', $info['name']));
        if (!file_exists(APPLICATION_PATH . "/../images/$name")) {
            rename($info['tmp_name'], APPLICATION_PATH . "/../images/$name");
        }

        return $name;
    }

    /**
     * Add article image
     *
     * @param int $articleNumber
     * @param Newscoop\Image\LocalImage $image
     * @param bool $defaultImage
     * @return Newscoop\Image\ArticleImage
     */
    public function addArticleImage($articleNumber, LocalImage $image, $defaultImage = false)
    {
        if ($image->getId() === null) {
            $this->orm->persist($image);
            $this->orm->flush($image);
        }

        $articleImage = new ArticleImage($articleNumber, $image, $defaultImage || $this->getArticleImagesCount($articleNumber) === 0);
        $this->orm->persist($articleImage);
        $this->orm->flush($articleImage);
        return $articleImage;
    }

    /**
     * Get article image
     *
     * @param int $articleNumber
     * @param int $imageId
     * @return Newscoop\Image\ArticleImage
     */
    public function getArticleImage($articleNumber, $imageId)
    {
        return $this->orm->getRepository('Newscoop\Image\ArticleImage')
            ->findOneBy(array(
                'articleNumber' => (int) $articleNumber,
                'image' => $imageId,
            ));
    }

    /**
     * Find images by article
     *
     * @param int $articleNumber
     * @return array
     */
    public function findByArticle($articleNumber)
    {
        $this->updateSchema($articleNumber);

        $images = $this->orm->getRepository('Newscoop\Image\ArticleImage')
            ->findBy(array(
                'articleNumber' => (int) $articleNumber,
            ), array('number' => 'asc'));

        $hasDefault = array_reduce($images, function($hasDefault, $image) {
            return $hasDefault || $image->isDefault();
        }, false);

        if (!empty($images) && $hasDefault === false) {
            $images[0]->setIsDefault(true);
        }

        return $images;
    }

    /**
     * Set default article image
     *
     * @param int $articleNumber
     * @param ImageInterface $image
     * @return void
     */
    public function setDefaultArticleImage($articleNumber, ArticleImage $image)
    {
        $query = $this->orm->createQuery('UPDATE Newscoop\Image\ArticleImage i SET i.isDefault = 0 WHERE i.articleNumber = :articleNumber');
        $query->setParameter('articleNumber', $articleNumber)
            ->execute();

        $image->setIsDefault(true);
        $this->orm->flush($image);
        $this->orm->clear();
    }

    /**
     * Get default article image
     *
     * @param int $articleNumber
     * @return Newscoop\Image\ArticleImage
     */
    public function getDefaultArticleImage($articleNumber)
    {
        $image = $this->orm->getRepository('Newscoop\Image\ArticleImage')
            ->findOneBy(array(
                'articleNumber' => (int) $articleNumber,
                'isDefault' => true,
            ));

        if ($image === null) {
            $image = array_pop($this->orm->getRepository('Newscoop\Image\ArticleImage')
                ->findBy(array(
                    'articleNumber' => (int) $articleNumber,
                ), array('number' => 'asc'), 1));

            if ($image !== null) {
                $image->setIsDefault(true);
                $this->orm->flush($image);
            }
        }

        return $image;
    }

    /**
     * Get thumbnail for given image and rendition
     *
     * @param Newscoop\Image\Rendition $rendition
     * @param Newscoop\Image\ImageInterface $image
     * @return Newscoop\Image\Thumbnail
     */
    public function getThumbnail(Rendition $rendition, ImageInterface $image)
    {
        return $rendition->getThumbnail($image, $this);
    }

    /**
     * Get count of article images
     *
     * @param int $articleNumber
     * @return int
     */
    public function getArticleImagesCount($articleNumber)
    {
        $query = $this->orm->getRepository('Newscoop\Image\ArticleImage')
            ->createQueryBuilder('i')
            ->select('COUNT(i)')
            ->where('i.articleNumber = :articleNumber')
            ->getQuery();

        return $query
            ->setParameter('articleNumber', $articleNumber)
            ->getScalarResult();
    }

    /**
     * Find image
     *
     * @param int $id
     * @return Newscoop\Image\LocalImage
     */
    public function find($id)
    {
        return $this->orm->getRepository('Newscoop\Image\LocalImage')
            ->find($id);
    }

    /**
     * Encode path
     *
     * @param string $path
     * @return string
     */
    private function encodePath($path)
    {
        return rawurlencode(rawurlencode($path)); // must be done twice for apache
    }

    /**
     * Decode path
     *
     * @param string $path
     * @return string
     */
    private function decodePath($path)
    {
        return rawurldecode(rawurldecode($path));
    }

    /**
     * Update schema if needed
     *
     * @return void
     */
    private function updateSchema($articleNumber)
    {
        try {
            $this->orm->getRepository('Newscoop\Image\ArticleImage')
                ->findOneBy(array(
                    'articleNumber' => (int) $articleNumber,
                ));
        } catch (\Exception $e) {
            if ($e->getCode() === '42S22') {
                $this->orm->getConnection()->exec('ALTER TABLE ArticleImages ADD is_default INT(1) DEFAULT NULL');
            }
        }
    }
}
