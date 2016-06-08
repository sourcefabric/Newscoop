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
     * @var array
     */
    protected $config = array('theme_path' => '/../themes');

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $orm;

    /**
     * @var Newscoop\Image\ImageService
     */
    protected $imageService;

    /**
     * @var array
     */
    protected $renditions;

    /**
     * @param array $config
     * @param Doctrine\ORM\EntityManager $orm
     * @param Newscoop\Image\ImageService $imageService
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
     * @param string $imageSpecs
     * @return Newscoop\Image\ArticleRendition
     */
    public function setArticleRendition($articleNumber, Rendition $rendition, ImageInterface $image, $imageSpecs = null)
    {
        if ($image->getWidth() < $rendition->getWidth() || $image->getHeight() < $rendition->getHeight()) {
            throw new \InvalidArgumentException("Image too small.");
        }

        $old = $this->getArticleRendition($articleNumber, $rendition);
        if ($old !== null) {
            $this->orm->remove($old);
            $this->orm->flush();
        }

        $articleRendition = new ArticleRendition($articleNumber, $rendition, $image, $imageSpecs);
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
            $this->orm->flush();
        }
    }

    /**
     * Unset article-image rendition
     *
     * @param int $articleNumber
     * @param int $image
     * @return void
     */
    public function unsetArticleImageRenditions($articleNumber, $image)
    {

        $renditions = $this->orm->getRepository('Newscoop\Image\ArticleRendition')->findBy(array(
            'articleNumber' => (int) $articleNumber,
            'image' => (int) $image,
        ));

        foreach ($renditions as $one_rend) {
            $this->orm->remove($one_rend);
            $this->orm->flush();
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
                'rendition' => (string) $rendition,
            ));
        } catch (\Exception $e) {
            $this->createSchemaIfMissing($e);
            return null;
        }
    }

    /**
     * Get article image rendition
     *
     * @param int    $articleNumber
     * @param string $renditionName
     * @param int    $width
     * @param int    $height
     * @return array
     */
    public function getArticleRenditionImage($articleNumber, $renditionName, $width = null, $height = null)
    {
        $renditions = $this->getRenditions();
        if (!array_key_exists($renditionName, $renditions)) {
            return false;
        }

        $articleRenditions = $this->getArticleRenditions($articleNumber);
        $rendition = $articleRenditions[$renditions[$renditionName]];
        if ($rendition === null) {
            return false;
        }

        try {
            if ($width !== null && $height !== null) {
                $preview = $rendition->getRendition()->getPreview($width, $height);
                $thumbnail = $preview->getThumbnail($rendition->getImage(), $this->imageService);
            } else {
                $thumbnail = $rendition->getRendition()->getThumbnail($rendition->getImage(), $this->imageService);
            }
        } catch (\Exception $e) {
            return null;
        }

        $originalRendition = new Rendition($rendition->getImage()->getWidth(), $rendition->getImage()->getHeight());

        return array(
            'id' => $rendition->getImage()->getId(),
            'src' => $thumbnail->src,
            'width' => $thumbnail->width,
            'height' => $thumbnail->height,
            'caption' => $rendition->getImage()->getCaption(),
            'photographer' => $rendition->getImage()->getPhotographer(),
            'photographer_url' => $rendition->getImage()->getPhotographerUrl(),
            'original' => (object) array(
                'width' => $rendition->getImage()->getWidth(),
                'height' => $rendition->getImage()->getHeight(),
                'src' => $originalRendition->getThumbnail($rendition->getImage(), $this->imageService)->src,
            ),
        );
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
     * Get renditions
     *
     * @return array
     */
    public function getRenditions()
    {
        if ($this->renditions === null) {
            $this->renditions = array();
            foreach ($this->orm->getRepository('Newscoop\Image\Rendition')->findBy(array(), array('offset' => 'asc', 'name' => 'asc')) as $rendition) {
                $this->renditions[$rendition->getName()] = $rendition;
            }

            if (empty($this->renditions)) {
                $this->registerRenditions();
            }
        }

        return $this->renditions;
    }

    /**
     * Register renditions
     *
     * @param array $existing
     * @return void
     */
    public function registerRenditions(array $existing = array())
    {
        $this->renditions = array();
        foreach (glob(APPLICATION_PATH . $this->config['theme_path'] . '/publication_*/theme_*/theme.xml') as $themeInfo) {
            $xml = simplexml_load_file($themeInfo);
            if (!$xml->renditions) {
                continue;
            }

            foreach ($xml->renditions->rendition as $rendition) {
                $renditionName = (string) $rendition['name'];
                if (!isset($this->renditions[$renditionName])) {
                    if (array_key_exists($renditionName, $existing)) {
                        $existing[$renditionName]->setWidth($rendition['width']);
                        $existing[$renditionName]->setHeight($rendition['height']);
                        $existing[$renditionName]->setSpecs($rendition['specs']);
                        $existing[$renditionName]->setOffset($rendition['offset']);
                        $existing[$renditionName]->setLabel($rendition['label']);
                        $this->renditions[$renditionName] = $existing[$renditionName];
                    } else {
                        $this->orm->persist($this->renditions[$renditionName] = new Rendition($rendition['width'], $rendition['height'], $rendition['specs'], $rendition['name']));
                    }
                }
            }
        }

        $this->orm->flush();
    }

    /**
     * Get rendition by given name
     *
     * @param string $name
     * @return Newscoop\Image\Rendition
     */
    public function getRendition($name)
    {
        $renditions = $this->getRenditions();
        $rendition = array_key_exists($name, $renditions) ? $renditions[$name] : null;
        if ($rendition !== null) {
            $rendition = $this->orm->getRepository('Newscoop\Image\Rendition')->find($rendition->getName());
            $this->orm->persist($rendition);
            $this->orm->flush($rendition);
        }

        return $rendition;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = array();
        foreach ($this->getRenditions() as $name => $rendition) {
            $specs = explode('_', $rendition->getSpecs());
            $options[$name] = sprintf('%s (%s %dx%d)', $name, array_shift($specs), $rendition->getWidth(), $rendition->getHeight());
        }

        return $options;
    }

    /**
     * Set renditions order
     *
     * @param array $order
     * @return void
     */
    public function setRenditionsOrder(array $order)
    {
        $renditions = $this->getRenditions();
        foreach ($order as $offset => $renditionName) {
            if (array_key_exists($renditionName, $renditions)) {
                $renditions[$renditionName]->setOffset($offset);
            }
        }

        $this->orm->flush();
        $this->renditions = null;
    }

    /**
     * Set renditions labels
     *
     * @param array $labels
     * @return void
     */
    public function setRenditionsLabels(array $labels)
    {
        $renditions = $this->getRenditions();
        foreach ($labels as $renditionName => $label) {
            if (array_key_exists($renditionName, $renditions)) {
                $renditions[$renditionName]->setLabel($label);
            }
        }

        $this->orm->flush();
        $this->renditions = null;
    }

    /**
     * Test if there are renditions defined
     *
     * @return bool
     */
    public function hasRenditions()
    {
        return count($this->getRenditions()) !== 0;
    }

    /**
     * Force reload of rendition specs
     *
     * @return void
     */
    public function reloadRenditions()
    {
        $this->getRenditions();
        $this->registerRenditions($this->renditions);
        $this->removeOrphanedArticleRenditions(array_keys($this->renditions));
        $this->removeOrphanedRenditions(array_keys($this->renditions));
        $this->renditions = null;
    }

    /**
     * Remove orphaned article renditions
     *
     * @param array $names
     * @return void
     */
    private function removeOrphanedArticleRenditions(array $names)
    {
        if (empty($names)) {
            $this->orm->createQuery('DELETE FROM Newscoop\Image\ArticleRendition ar')
                ->execute();
            return;
        }

        $this->orm->createQuery('DELETE FROM Newscoop\Image\ArticleRendition ar WHERE ar.rendition NOT IN (:names)')
            ->setParameter('names', $names)
            ->execute();
    }

    /**
     * Remove orphaned renditions
     *
     * @param array $names
     * @return void
     */
    private function removeOrphanedRenditions(array $names)
    {
        if (empty($names)) {
            $this->orm->createQuery('DELETE FROM Newscoop\Image\Rendition r')
                ->execute();
            return;
        }

        $this->orm->createQuery('DELETE FROM Newscoop\Image\Rendition r WHERE r.name NOT IN (:names)')
            ->setParameter('names', array_merge($names, $this->getPackageRenditionNames()))
            ->execute();
    }

    /**
     * Get renditions names used by packages
     *
     * @return array
     */
    private function getPackageRenditionNames()
    {
        $names = array();
        foreach ($this->orm->createQuery('SELECT p FROM Newscoop\Package\Package p')->getResult() as $package) {
            $id = $this->orm->getUnitOfWork()->getEntityIdentifier($package->getRendition());
            $names[] = $id['name'];
        }

        return $names;
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
