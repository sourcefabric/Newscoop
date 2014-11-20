<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

use Imagine\Image\Box;
use Imagine\Gd\Imagine;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Newscoop\Image\LocalImage;
use Newscoop\Entity\User;
use Newscoop\Exception\ResourcesConflictException;
use Doctrine\ORM\NoResultException;

/**
 * Image Service
 */
class ImageService
{
    /**
     * @var array
     */
    protected $config = array();

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $orm;

    protected $cacheService;

    /**
     * @var array
     */
    protected $supportedTypes = array(
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
    );

    /**
     * @param array                      $config
     * @param Doctrine\ORM\EntityManager $orm
     */
    public function __construct(array $config, \Doctrine\ORM\EntityManager $orm, $cacheService)
    {
        $this->config = $config;
        $this->orm = $orm;
        $this->cacheService = $cacheService;
    }

    /**
     * Upload image and create entity
     *
     * @param UploadedFile   $file
     * @param array          $attributes
     * @param ImageInterface $image
     *
     * @return LocalImage
     */
    public function upload(UploadedFile $file, array $attributes, ImageInterface $image = null)
    {
        $filesystem = new Filesystem();
        $imagine = new Imagine();

        $mimeType = $file->getClientMimeType();
        if (!in_array($mimeType, $this->supportedTypes)) {
            throw new \InvalidArgumentException('Unsupported image type '.$mimeType.'.');
        }

        if (!file_exists($this->config['image_path']) || !is_writable($this->config['image_path'])) {
            throw new FileException('Directory '.$this->config['image_path'].' is not writable');
        }

        if (!file_exists($this->config['thumbnail_path']) || !is_writable($this->config['thumbnail_path'])) {
            throw new FileException('Directory '.$this->config['thumbnail_path'].' is not writable');
        }

        $attributes = array_merge(array(
            'content_type' => $mimeType,
        ), $attributes);

        if (!is_null($image)) {
            if (file_exists($image->getPath())) {
                $filesystem->remove($image->getPath());
            }

            if (file_exists($image->getThumbnailPath())) {
                unlink($this->config['thumbnail_path'] . $image->getThumbnailPath(true));
            }
        } else {
            $image = new LocalImage($file->getClientOriginalName());
            $image->setCreated(new \DateTime());
            $this->orm->persist($image);
        }

        list($width, $height) = getimagesize($file->getRealPath());
        $image->setWidth($width);
        $image->setHeight($height);

        $this->fillImage($image, $attributes);
        $this->orm->flush();

        $imagePath = $this->generateImagePath($image->getId(), $file->getClientOriginalExtension());
        $thumbnailPath = $this->generateThumbnailPath($image->getId(), $file->getClientOriginalExtension());

        $image->setBasename($this->generateImagePath($image->getId(), $file->getClientOriginalExtension(), true));
        $image->setThumbnailPath($this->generateThumbnailPath($image->getId(), $file->getClientOriginalExtension(), true));
        $this->orm->flush();

        try {
            $file->move($this->config['image_path'], $this->generateImagePath($image->getId(), $file->getClientOriginalExtension(), true));
            $filesystem->chmod($imagePath, 0644);

            $imagine->open($imagePath)
                ->resize(new Box($this->config['thumbnail_max_size'], $this->config['thumbnail_max_size']))
                ->save($thumbnailPath, array());
            $filesystem->chmod($thumbnailPath, 0644);
        } catch (\Exceptiom $e) {
            $filesystem->remove($imagePath);
            $filesystem->remove($thumbnailPath);
            $this->orm->remove($image);
            $this->orm->flush();

            throw new \Exception($e->getMessage(), $e->getCode());
        }

        $this->cacheService->clearNamespace('image');

        return $image;
    }

    /**
     * Remove image (files and entity)
     *
     * @param LocalImage $image
     *
     * @return boolean
     */
    public function remove(LocalImage $image)
    {
        $filesystem = new Filesystem();

        if (file_exists($image->getPath())) {
            $filesystem->remove($image->getPath());
        }

        if (file_exists($image->getThumbnailPath())) {
            unlink($this->config['thumbnail_path'] . $image->getThumbnailPath(true));
        }

        $articleImages = $this->orm->getRepository('Newscoop\Image\ArticleImage')
            ->getArticleImagesForImage($image)
            ->getResult();

        foreach ($articleImages as $articleImage) {
            \ArticleImage::RemoveImageTagsFromArticleText($articleImage->getArticleNumber(), $articleImage->getNumber());
            $this->orm->remove($articleImage);
        }

        $this->orm->remove($image);
        $this->orm->flush();

        $this->cacheService->clearNamespace('article_image');
        $this->cacheService->clearNamespace('image');

        return true;
    }

    /**
     * Save image
     *
     * @param array $info
     *
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
     * Get image src
     *
     * @param string $image
     * @param int    $width
     * @param int    $height
     * @param string $specs
     *
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
     *
     * @return null|\Nette\Image
     */
    public function generateFromSrc($src)
    {
        $matches = array();
        if (!preg_match('#^([0-9]+)x([0-9]+)/([_a-z0-9]+)/([-_.:~%|a-zA-Z0-9]+)$#', $src, $matches)) {
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

        return $image;
    }

    /**
     * Generate file path for thumbnail
     *
     * @param int     $imageId
     * @param string  $extension
     * @param boolean $olnyFileName
     *
     * @return string
     */
    private function generateThumbnailPath($imageId, $extension, $olnyFileName = false)
    {
        if ($olnyFileName) {
            return $this->config['thumbnail_prefix'] . sprintf('%09d', $imageId) .'.'. $extension;
        }

        return $this->config['thumbnail_path'] . $this->config['thumbnail_prefix'] . sprintf('%09d', $imageId) .'.'. $extension;
    }

    /**
     * Generate file path for image
     *
     * @param int     $imageId
     * @param string  $extension
     * @param boolean $olnyFileName
     *
     * @return string
     */
    private function generateImagePath($imageId, $extension, $olnyFileName = false)
    {
        if ($olnyFileName) {
            return $this->config['image_prefix'] . sprintf('%09d', $imageId) .'.'. $extension;
        }

        return $this->config['image_path'] . $this->config['image_prefix'] . sprintf('%09d', $imageId) .'.'. $extension;
    }

    /**
     * Fill image with custom/default arttributes
     *
     * @param LocalImage $image
     * @param array      $attributes
     *
     * @return LocalImage
     */
    public function fillImage($image, $attributes)
    {
        $attributes = array_merge(array(
            'description' => '',
            'photographer' => '',
            'photographer_url' => '',
            'place' => '',
            'date' => date('Y-m-d'),
            'content_type' => 'image/jeg',
            'user' => null,
            'updated' => new \DateTime(),
            'status' => 'unapproved',
            'source' => 'local',
            'url' => ''
        ), $attributes);

        $image->setDescription($attributes['description']);
        $image->setPhotographer($attributes['photographer']);
        $image->setPhotographerUrl($attributes['photographer_url']);
        $image->setPlace($attributes['place']);
        $image->setDate($attributes['date']);
        $image->setContentType($attributes['content_type']);
        $image->setUser($attributes['user']);
        $image->setUpdated($attributes['updated']);
        $image->setSource($attributes['source']);
        $image->setUrl($attributes['url']);

        if ($image->getUser() && $image->getUser()->isAdmin() == true) {
            $image->setStatus('approved');
        } else {
            $image->setStatus($attributes['status']);
        }

        return $image;
    }

    /**
     * Save article image
     *
     * @param ArticleImage $articleImage
     * @param array $values
     * @return void
     */
    public function saveArticleImage(ArticleImage $articleImage, array $values)
    {
        $language = $this->orm->getReference('Newscoop\Entity\Language', $values['language']);
        $articleImage->setNumber($values['number']);
        $articleImage->setCaption($values['caption'], $language);
        $this->orm->flush();
    }

    /**
     * Add article image
     *
     * @param int                       $articleNumber
     * @param LocalImage $image
     * @param bool                      $defaultImage
     *
     * @return ArticleImage
     */
    public function addArticleImage($articleNumber, LocalImage $image, $defaultImage = false)
    {
        if ($image->getId() === null) {
            $this->orm->persist($image);
            $this->orm->flush($image);
        }

        if ($this->getArticleImage($articleNumber, $image->getId())) {
            throw new ResourcesConflictException("Image already attached to article", 409);
        }

        $imagesCount = $this->getArticleImagesCount($articleNumber);
        $articleImage = new ArticleImage(
            $articleNumber,
            $image,
            $defaultImage || $imagesCount === 0,
            $imagesCount+1
        );
        $this->orm->persist($articleImage);
        $this->orm->flush($articleImage);

        return $articleImage;
    }

    /**
     * Remove image from article
     *
     * @param ArticleImage $articleImage
     */
    public function removeArticleImage(ArticleImage $articleImage)
    {
        \ArticleImage::RemoveImageTagsFromArticleText($articleImage->getArticleNumber(), $articleImage->getNumber());

        $this->orm->remove($articleImage);
        $this->orm->flush();
    }

    /**
     * Get article image
     *
     * @param int $articleNumber
     * @param int $imageId
     *
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
     *
     * @return array
     */
    public function findByArticle($articleNumber)
    {
        $this->updateSchema($articleNumber);

        $images = $this->orm->getRepository('Newscoop\Image\ArticleImage')
            ->findBy(array(
                'articleNumber' => (int) $articleNumber,
            ), array('number' => 'asc'));

        $hasDefault = array_reduce($images, function ($hasDefault, $image) {
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
     * @param int            $articleNumber
     * @param ImageInterface $image
     *
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
     *
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
            $image = array_pop(
                $this->orm->getRepository('Newscoop\Image\ArticleImage')->findBy(
                    array('articleNumber' => (int) $articleNumber),
                    array('number' => 'asc'),
                    1
                )
            );

            if ($image !== null) {
                $image->setIsDefault(true);
                $this->orm->flush($image);
            }
        }

        return $image;
    }

    /**
     * Get thumbnail for given image
     *
     * @param string $image
     * @param int    $width
     * @param int    $height
     * @param string $specs
     *
     * @return Thumbnail
     */
    public function thumbnail($image, $width, $height, $specs)
    {
        if (is_string($image)) {
            $image = new \Newscoop\Image\LocalImage($image);
        }

        return $this->getThumbnail(new \Newscoop\Image\Rendition($width, $height, $specs), $image);
    }

    /**
     * Get thumbnail for given image and rendition
     *
     * @param Rendition      $rendition
     * @param ImageInterface $image
     *
     * @return Thumbnail
     */
    public function getThumbnail(Rendition $rendition, ImageInterface $image)
    {
        return $rendition->getThumbnail($image, $this);
    }

    /**
     * Get count of article images
     *
     * @param int $articleNumber
     *
     * @return int
     */
    public function getArticleImagesCount($articleNumber)
    {
        $query = $this->orm->getRepository('Newscoop\Image\ArticleImage')
            ->createQueryBuilder('i')
            ->select('MAX(i.number)')
            ->where('i.articleNumber = :articleNumber')
            ->getQuery();

        return $query
            ->setParameter('articleNumber', $articleNumber)
            ->getSingleScalarResult();
    }

    /**
     * Find image
     *
     * @param int $id
     *
     * @return Newscoop\Image\LocalImage
     */
    public function find($id)
    {
        return $this->orm->getRepository('Newscoop\Image\LocalImage')
            ->find($id);
    }

    /**
     * Find images by a set of criteria
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int   $limit
     * @param int   $offset
     *
     * @return array
     */
    public function findBy(array $criteria, $orderBy = null, $limit = 25, $offset = 0)
    {
        return $this->orm->getRepository('Newscoop\Image\LocalImage')
            ->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Get count of images for a set of criteria
     *
     * @param array $criteria
     *
     * @return int
     */
    public function getCountBy(array $criteria)
    {
        $qb = $this->orm->getRepository('Newscoop\Image\LocalImage')
            ->createQueryBuilder('i')
            ->select('COUNT(i)');

        if (isset($criteria['source']) && is_array($criteria['source']) && (!empty($criteria['source']))) {
            $sourceCases = array();
            foreach ($criteria['source'] as $oneSource) {
                $sourceCases[] = $qb->expr()->literal($oneSource);
            }

            $qb->andwhere('i.source IN (:source)');
            $qb->setParameter('source', $sourceCases);
        }

        return (int) $qb->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Encode path
     *
     * @param string $path
     *
     * @return string
     */
    private function encodePath($path)
    {
        return rawurlencode(str_replace('/', '|', $path));
    }

    /**
     * Decode path
     *
     * @param string $path
     *
     * @return string
     */
    private function decodePath($path)
    {
        return str_replace('|', '/', rawurldecode($path));
    }

    /**
     * Get user image
     *
     * @param Newscoop\Entity\User $user
     * @param int $width
     * @param int $height
     * @return string
     */
    public function getUserImage(User $user, $width = 65, $height = 65)
    {
        if ($user->getImage() !== null) {
            return $this->getSrc('images/' . $user->getImage(), $width, $height, 'crop');
        }

        return null;
    }

    /**
     * Update schema if needed
     *
     * @param integer $articleNumber
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

    /**
     * Gets path of local images
     *
     * @return string
     */
    public function getImagePath()
    {
        return $this->config['image_path'];
    }

    /**
     * Return true if the image is being used by an article.
     *
     * @param LocalImage $image Local image
     *
     * @return boolean
     */
    public function inUse($image)
    {
        $imageArticle = $this->orm->getRepository('Newscoop\Image\ArticleImage')->findOneBy(array(
            'image' => $image,
        ));

        if ($imageArticle) {
            $imagesCount = $this->orm->getRepository('Newscoop\Entity\Article')
                ->createQueryBuilder('a')
                ->select('count(a)')
                ->where('number = :articleNumber')
                ->andWhere('images = :image')
                ->setParameter('image', $imageArticle)
                ->setParameter('articleNumber', $imageArticle->getArticleNumber())
                ->getQuery()
                ->getSingleScalarResult();

            if ((int) $imagesCount > 0) {
                return true;
            }
        }

        return false;
    }

    /**
    * Get image caption
    *
    * @param LocalImage $image
    * @param int $articleNumber
    * @param int $languageId
    *
    * @return string
    */
    public function getCaption(\Newscoop\Image\LocalImage $image, $articleNumber, $languageId)
    {
        $caption = $this->getArticleImageCaption($image->getId(), $articleNumber, $languageId);

        if (!empty($caption)) {
            return $caption;
        }

        return $image->getDescription();
    }

    /**
    * Get article specific image caption
    *
    * @param int $imageId
    * @param int $articleNumber
    * @param int $languageId
    *
    * @return string
    */
    public function getArticleImageCaption($imageId, $articleNumber, $languageId)
    {
        $query = $this->orm->getRepository('Newscoop\Image\ArticleImageCaption')->createQueryBuilder('c')
            ->select('c.caption')
            ->where('c.articleNumber = :article')
            ->andWhere('c.image = :image')
            ->andWhere('c.languageId = :language')
            ->getQuery();

        $query->setParameters(array(
            'article' => $articleNumber,
            'image' => $imageId,
            'language' => $languageId,
        ));

        try {
            return $query->getSingleScalarResult();
        } catch (NoResultException $e) {}
    }
}
