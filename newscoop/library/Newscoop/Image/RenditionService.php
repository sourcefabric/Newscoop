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
    protected $config;

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
     * @param Doctrine\ORM\EntityManager $orm
     */
    public function __construct(array $config, \Doctrine\ORM\EntityManager $orm, ImageService $imageService)
    {
        $this->config = $config;
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
            $this->orm->flush($old);
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
                'rendition' => (string) $rendition,
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
     * @return void
     */
    public function registerRenditions()
    {
        $this->renditions = array();
        foreach (glob($this->config['theme_path'] . '/publication_*/theme_*/theme.xml') as $themeInfo) {
            $xml = simplexml_load_file($themeInfo);
            if (!$xml->renditions) {
                continue;
            }

            foreach ($xml->renditions->rendition as $rendition) {
                if (!isset($this->renditions[(string) $rendition['name']])) {
                    $this->orm->persist($this->renditions[(string) $rendition['name']] = new Rendition($rendition['width'], $rendition['height'], $rendition['specs'], $rendition['name']));
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
            $options[$name] = sprintf('%s (%s %dx%d)', $name, array_shift(explode('_', $rendition->getSpecs())), $rendition->getWidth(), $rendition->getHeight());
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
