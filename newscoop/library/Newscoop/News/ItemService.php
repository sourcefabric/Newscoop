<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 * Item Service
 */
class ItemService
{
    /**
     * @var Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $odm;

    /**
     * @var Doctrine\ODM\MongoDB\DocumentRepository
     */
    protected $repository;

    /**
     * @var array
     */
    protected $settings = array(
        'article_type' => 'newsml',
        'publication' => 1,
    );

    /**
     * @var Doctrine\Common\Persistence\ObjectManager $odm
     */
    public function __construct(\Doctrine\ODM\MongoDB\DocumentManager $odm)
    {
        $this->odm = $odm;
        $this->repository = $this->odm->getRepository('Newscoop\News\Item');
    }

    /**
     * Find item by given id
     *
     * @param string $id
     * @return Newscoop\News\Item
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Find items by set of criteria
     *
     * @param array $criteria
     * @param mixed $orderBy
     * @param int $limit
     * @param int $offset
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = 25, $offset = 0)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Save item
     *
     * @param Newscoop\News\Item $item
     * @return void
     */
    public function save(Item $item)
    {
        $persisted = $this->repository->find($item->getId());
        if ($persisted !== null) {
            if ($item->getVersion() < $persisted->getVersion()) {
                return;
            } else { // @todo handle append signal
                $this->odm->remove($persisted);
                $this->odm->flush();
            }
        }

        if ($item->isCanceled()) {
            return;
        }

        $this->odm->persist($item);
        $this->odm->flush();
    }

    /**
     * Publish item
     *
     * @param Newscoop\News\Item $item
     * @return void
     */
    public function publish(Item $item)
    {
        switch ($item->getItemMeta()->getItemClass()) {
            case 'icls:text':
                $this->publishArticle($item);
                break;

            default:
                throw new \InvalidArgumentException("Can't publish {$item->getItemMeta()->getItemClass()}");
                break;
        }

        $item->setPublished(new \DateTime());
        $this->odm->flush();
    }

    /**
     * Publish item as article
     *
     * @param Newscoop\News\Item $item
     * @return void
     */
    private function publishArticle(Item $item)
    {
        $article = new \Article($this->findLanguageId($item->getContentMeta()->getLanguage()));
        $article->create($this->settings['article_type'], $item->getContentMeta()->getHeadline());
        $article->setKeywords($item->getContentMeta()->getSlugline());
        $article->setCreationDate($item->getItemMeta()->getFirstCreated()->format('Y-m-d H:i:s'));
        $this->setArticleData($article, $item);
        $article->commit();
    }

    /**
     * Find language id
     *
     * @param string $language
     * @return int
     */
    private function findLanguageId($language)
    {
        return 1;
    }

    /**
     * Set article data
     *
     * @param Article $article
     * @param Newscoop\News\Item $item
     * @return void
     */
    private function setArticleData(\Article $article, Item $item)
    {
        $this->createArticleType($article->getType());
        $data = $article->getArticleData();
        $data->setProperty('Fguid', $item->getId());
        $data->setProperty('Fversion', $item->getVersion());
        $data->setProperty('Furgency', $item->getContentMeta()->getUrgency());
        $data->setProperty('Fcopyright', $item->getRightsInfo()->first()->getCopyrightNotice());
        $data->setProperty('Fprovider', $item->getItemMeta()->getProvider());
        $data->setProperty('Fdescription', $item->getContentMeta()->getDescription());
        $data->setProperty('Fdateline', $item->getContentMeta()->getDateline());
        $data->setProperty('Fbyline', $item->getContentMeta()->getByline());
        $data->setProperty('Fcreditline', $item->getContentMeta()->getCreditline());
        $data->setProperty('Finlinecontent', (string) $item->getContentSet()->getInlineContent());
        $data->create();
    }

    /**
     * Create article type if does not exist
     *
     * @param string $typeName
     * @return void
     */
    private function createArticleType($typeName)
    {
        static $requiredFields = array(
            'guid' => 'text',
            'version' => 'text',
            'urgency' => 'text',
            'copyright' => 'text',
            'provider' => 'text',
            'description' => 'body',
            'dateline' => 'text',
            'byline' => 'text',
            'creditline' => 'text',
            'inlinecontent' => 'body',
        );

        $type = new \ArticleType($typeName);
        if (!$type->exists()) {
            $type->create();
        }

        $fields = $type->getUserDefinedColumns(null, true, true);
        foreach ($requiredFields as $fieldName => $fieldType) {
            if (!array_key_exists('urgency', $fields)) {
                $field = new \ArticleTypeField($type->getTypeName(), $fieldName);
                $field->create($fieldType, array());
            }
        }
    }
}
