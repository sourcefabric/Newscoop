<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Package;

/**
 * Package Service
 */
class PackageService
{
    const CODE_UNIQUE_SLUG = 1;

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $orm;

    /**
     * @var Doctrine\ORM\EntityRepository
     */
    protected $repository;

    /**
     * @var Newscoop\Image\ImageService
     */
    protected $imageService;

    /**
     * @param Doctrine\ORM\EntityManager $orm
     */
    public function __construct(\Doctrine\ORM\EntityManager $orm, \Newscoop\Image\ImageService $imageService)
    {
        $this->orm = $orm;
        $this->repository = $this->orm->getRepository('Newscoop\Package\Package');
        $this->imageService = $imageService;
    }

    /**
     * Find package
     *
     * @param int $id
     * @return Newscoop\Package\Package
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Find packages by article
     *
     * @param int $articleNumber
     * @return array
     */
    public function findByArticle($articleNumber)
    {
        $article = $this->getArticle($articleNumber);
        return $article->getPackages()->toArray();
    }

    /**
     * Save package
     *
     * @param array $values
     * @param Package $package
     * @return Package
     */
    public function save(array $values, Package $package = null)
    {
        if ($package === null) {
            $package = new Package();
            $this->orm->persist($package);
        }

        if (array_key_exists('headline', $values)) {
            $package->setHeadline($values['headline']);
        }

        if (array_key_exists('rendition', $values)) {
            $package->setRendition($values['rendition']);
        }

        if (array_key_exists('slug', $values)) {
            $package->setSlug($values['slug']);
        }

        try {
            $this->orm->flush($package);
            return $package;
        } catch (\PDOException $e) {
            if (strpos($e->getMessage(), 'slug is not unique') !== false) {
                throw new \InvalidArgumentException("Slug is not unique", self::CODE_UNIQUE_SLUG);
            }

            throw $e;
        }
    }

    /**
     * Add package item
     *
     * @param Package $package
     * @param mixed $item
     * @return Item
     */
    public function addItem(Package $package, $item)
    {
        if (is_a($item, 'Newscoop\Image\LocalImage') && !$this->orm->contains($item)) {
            $this->orm->persist($item);
            $this->orm->flush($item);
        }

        if ($package->getRendition() !== null && is_a($item, 'Newscoop\Image\LocalImage') && !$package->getRendition()->fits($item)) {
            throw new \InvalidArgumentException("Image too small.");
        }

        $packageItem = new Item($package, $item);
        $this->orm->persist($packageItem);
        $this->orm->flush();
        return $packageItem;
    }

    /**
     * Set order of items for given package
     *
     * @param Package $package
     * @param array $order
     * @return void
     */
    public function setOrder(Package $package, $order)
    {
        $items = array();
        foreach ($package->getItems() as $item) {
            $offset = array_search('item-' . $item->getId(), $order);
            $package->getItems()->set($offset, $item);
            $package->getItems()->get($offset)->setOffset($offset);
        }

        $this->orm->flush();
    }

    /**
     * Remove item from package
     *
     * @param Package $package
     * @param int $itemId
     * @return void
     */
    public function removeItem(Package $package, $itemId)
    {
        foreach ($package->getItems() as $item) {
            if ($item->getId() === (int) $itemId) {
                for ($i = $item->getOffset() + 1; $i < count($package->getItems()); $i++) {
                    $package->getItems()->set($i - 1, $package->getItems()->get($i));
                    $package->getItems()->get($i - 1)->setOffset($i - 1);
                }

                $package->getItems()->remove(count($package->getItems()) - 1);
                $this->orm->remove($item);
                $this->orm->flush();
                return;
            }
        }
    }

    /**
     * Find item by given id
     *
     * @param int $id
     * @return Newscoop\Package\Item
     */
    public function findItem($id)
    {
        return $this->orm->getRepository('Newscoop\Package\Item')->find($id);
    }

    /**
     * Save item
     *
     * @param array $values
     * @param Item $item
     * @return void
     */
    public function saveItem(array $values, Item $item)
    {
        if (array_key_exists('caption', $values)) {
            $item->setCaption($values['caption']);
        }

        if (array_key_exists('coords', $values)) {
            $item->setCoords($values['coords']);
        }

        if (!empty($values['url'])) {
            $item->setVideoUrl($values['url']);
        }

        $this->orm->flush($item);
    }

    /**
     * Find package by slug
     *
     * @param string $slug
     * @return Newscoop\Package\Package
     */
    public function findBySlug($slug)
    {
        return $this->repository->findOneBy(array(
            'slug' => $slug,
        ));
    }

    /**
     * Find package by a set of criteria
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = array(), $limit = 25, $offset = 0)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Get count by a set of criteria
     *
     * @param array $criteria
     * @return int
     */
    public function getCountBy(array $criteria = array())
    {
        return (int) $this->repository->getCountBy($criteria);
    }

    /**
     * Save article packages
     *
     * @param array $articleArray
     * @return void
     */
    public function saveArticle(array $articleArray)
    {
        $article = $this->getArticle($articleArray['id']);
        $article->getPackages()->clear();
        foreach ($articleArray['slideshows'] as $slideshow) {
            $package = $this->orm->find('Newscoop\Package\Package', $slideshow['id']);
            $article->getPackages()->add($package);
        }

        $this->orm->flush();
    }

    /**
     * Remove package from article
     *
     * @param Package $package
     * @param int $articleNumber
     * @return void
     */
    public function removeArticle(Package $package, $articleNumber)
    {
        $article = $this->getArticle($articleNumber);
        $article->getPackages()->removeElement($package);
        $this->orm->flush($article);
    }

    /**
     * Find packages not attached to article
     *
     * @param int $articleNumber
     * @return array
     */
    public function findAvailableForArticle($articleNumber)
    {
        $article = $this->getArticle($articleNumber);
        return $this->orm->getRepository('Newscoop\Package\Package')->findAvailableForArticle($article);
    }

    /**
     * Delete package
     *
     * @param int $id
     * @return void
     */
    public function delete($id)
    {
        $package = $this->orm->getRepository('Newscoop\Package\Package')->find($id);
        $this->orm->remove($package);
        $this->orm->flush();
    }

    /**
     * Get article entity
     *
     * @param int $articleNumber
     * @return Newscoop\Package\Article
     */
    private function getArticle($articleNumber)
    {
        try {
            $article = $this->orm->getRepository('Newscoop\Package\Article')
                ->findOneBy(array('id' => $articleNumber));
        } catch (\Exception $e) {
            if ($e->getCode() === '42S02') {
                $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->orm);
                try {
                    $schemaTool->createSchema(array(
                        $this->orm->getClassMetadata('Newscoop\Package\Article'),
                    ));
                } catch (\Exception $e) {
                }
                $article = null;
            } else {
                throw $e;
            }
        }

        if ($article === null) {
            $article = new Article($articleNumber);
            $this->orm->persist($article);
            $this->orm->flush($article);
        }

        return $article;
    }
}
