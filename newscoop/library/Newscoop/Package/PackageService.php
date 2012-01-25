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
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $orm;

    /**
     * @param Doctrine\ORM\EntityManager $orm
     */
    public function __construct(\Doctrine\ORM\EntityManager $orm)
    {
        $this->orm = $orm;
    }

    /**
     * Find package
     *
     * @param int $id
     * @return Newscoop\Package\Package
     */
    public function find($id)
    {
        return $this->orm->getRepository('Newscoop\Package\Package')->find($id);
    }

    /**
     * Find packages by article
     *
     * @param int $articleNumber
     * @return array
     */
    public function findByArticle($articleNumber)
    {
        try {
            return $this->orm->getRepository('Newscoop\Package\Package')->findBy(array(
                'articleNumber' => $articleNumber,
            ), array('id' => 'asc'));
        } catch (\Exception $e) {
            if ($e->getCode() === '42S02') {
                $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->orm);
                try {
                    $schemaTool->createSchema(array(
                        $this->orm->getClassMetadata('Newscoop\Package\Package'),
                        $this->orm->getClassMetadata('Newscoop\Package\Item'),
                    ));
                } catch (\Exception $e) {
                }
                return array();
            } else {
                throw $e;
            }
        }
    }

    /**
     * Save package
     *
     * @param array $values
     * @param Newscoop\Package\Package $package
     * @return Newscoop\Package\Package
     */
    public function save(array $values, Package $package = null)
    {
        if ($package === null) {
            $package = new Package();
            $this->orm->persist($package);
        }

        if (array_key_exists('article', $values)) {
            $package->setArticleNumber($values['article']);
        }

        $this->orm->flush($package);
        return $package;
    }

    /**
     * Add package item
     *
     * @param Newscoop\Package\Package $package
     * @param mixed $item
     * @param int $offset
     * @return Newscoop\Package\Item
     */
    public function addItem(Package $package, $item)
    {
        if (!$this->orm->contains($item)) {
            $this->orm->persist($item);
            $this->orm->flush($item);
        }

        $packageItem = new Item($package, $item);
        $this->orm->persist($packageItem);
        $this->orm->flush();
        return $packageItem;
    }

    /**
     * Set order of items for given package
     *
     * @param Newscoop\Package\Package $package
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
     * @param Newscoop\Package\Package $package
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
}
