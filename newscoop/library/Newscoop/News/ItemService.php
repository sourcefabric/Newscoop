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
    const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $odm;

    /**
     * @var Doctrine\ODM\MongoDB\DocumentRepository
     */
    protected $repository;

    /**
     * @var Newscoop\News\Settings
     */
    protected $settings;

    /**
     * @var Doctrine\Common\Persistence\ObjectManager $odm
     * @var Newscoop\News\SettingsService
     */
    public function __construct(\Doctrine\ODM\MongoDB\DocumentManager $odm, SettingsService $settingsService)
    {
        $this->odm = $odm;
        $this->repository = $this->odm->getRepository('Newscoop\News\Item');
        $this->settings = $settingsService->find('ingest');
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
     * Find item by given itemRef
     *
     * @param Newscoop\News\ItemRef $itemRef
     * @param Newscoop\News\Feed $feed
     * @return Newscoop\News\Item
     */
    public function findByItemRef(ItemRef $itemRef, Feed $feed)
    {
        $item = $this->find($itemRef->getResidRef());
        if (!$item) {
            $item = $feed->getItem($itemRef->getResidRef());
            if ($item) {
                $item->setFeed($feed);
                $this->odm->persist($item);
                $this->odm->flush();
            }
        }

        return $item;
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
     * @return mixed
     */
    public function publish(Item $item)
    {
        $return = null;
        switch ($item->getItemMeta()->getItemClass()) {
            case ItemMeta::CLASS_TEXT:
                $return = $this->publishText($item);
                break;

            case ItemMeta::CLASS_PICTURE:
                $return = $this->publishPicture($item);
                break;

            case ItemMeta::CLASS_PACKAGE:
                $return = $this->publishPackage($item);
                break;

            default:
                throw new \InvalidArgumentException("Can't publish {$item->getItemMeta()->getItemClass()}");
                break;
        }

        $item->setPublished(new \DateTime());
        $this->odm->flush();
        return $return;
    }

    /**
     * Publish text item
     *
     * @param Newscoop\News\NewsItem $item
     * @return Article
     */
    private function publishText(NewsItem $item)
    {
        $issueNumber = $this->settings->getPublicationId() ? \Issue::GetCurrentIssue($this->settings->getPublicationId())->getIssueNumber() : null;
        $type = $this->getArticleType($this->settings->getArticleTypeName());
        $article = new \Article($this->findLanguageId($item->getContentMeta()->getLanguage()));
        $article->create($type->getTypeName(), $item->getContentMeta()->getHeadline(), $this->settings->getPublicationId(), $issueNumber, $this->settings->getSectionNumber());
        $article->setKeywords($item->getContentMeta()->getSlugline());
        $article->setCreationDate($item->getItemMeta()->getFirstCreated()->format(self::DATE_FORMAT));
        $article->setPublishDate(null);
        $article->setProperty('time_updated', date_create('now')->format(self::DATE_FORMAT));
        $this->setArticleData($article, $item);
        $article->commit();
        return $article;
    }

    /**
     * Publish picture item
     *
     * @param Newscoop\News\NewsItem $item
     * @return Image
     */
    private function publishPicture(NewsItem $item)
    {
        $rendition = $item->getContentSet()->getRemoteContent('rend:baseImage') ?: $item->getContentSet()->getRemoteContent('rend:viewImage');
        $realpath = tempnam('/tmp', 'picture');
        file_put_contents($realpath, file_get_contents($item->getFeed()->getRemoteContentSrc($rendition)));

        $imagesize = getimagesize($realpath);
        $info = array(
            'name' => uniqid(),
            'type' => $imagesize['mime'],
            'tmp_name' => $realpath,
            'size' => filesize($realpath),
            'error' => 0,
        );

        $attributes = array(
            'Photographer' => $item->getContentMeta()->getByline(),
            'Description' => $item->getContentMeta()->getHeadline(),
            'Source' => \Newscoop\Entity\Picture::SOURCE_INGEST,
            'Caption' => $item->getContentMeta()->getDescription(),
            'Status' => \Newscoop\Entity\Picture::STATUS_APPROVED,
            'Date' => $item->getItemMeta()->getFirstCreated()->format(self::DATE_FORMAT),
            'Place' => $item->getContentMeta()->getSubject('cptType:5')->getName(),
        );

        return \Image::OnImageUpload($info, $attributes, null, null, true);
    }

    /**
     * Publish package item
     *
     * @param Newscoop\News\PackageItem $item
     * @return void
     */
    private function publishPackage(PackageItem $item)
    {
        $root = $item->getGroupSet()->getRootGroup();
        foreach ($root->getRefs() as $ref) {
            $this->publishGroup($ref, $item);
        }

        $item->setPublished(new \DateTime());
        $this->odm->flush();
    }

    /**
     * Publish group
     *
     * @param Newscoop\News\GroupRef $groupRef
     * @param Newscoop\News\Item $item
     * @return void
     */
    private function publishGroup(GroupRef $groupRef, Item $item)
    {
        $group = $item->getGroupSet()->getGroup($groupRef);
        $items = array();
        foreach ($group->getRefs() as $ref) {
            if ($ref instanceof ItemRef) {
                $groupItem = $this->findByItemRef($ref, $item->getFeed());
                if ($groupItem === null) {
                    continue;
                }

                $groupItem->setPublished(new \DateTime());
                switch ($groupItem->getItemMeta()->getItemClass()) {
                    case ItemMeta::CLASS_PICTURE:
                        $image = $this->publishPicture($groupItem);
                        \ArticleImage::AddImageToArticle($image->getImageId(), $items[0]->getArticleNumber());
                        break;

                    case ItemMeta::CLASS_TEXT:
                        $article = $this->publishText($groupItem);
                        $items[] = $article;
                        break;
                }
            } else {
                $this->publishGroup($ref, $item);
            }
        }
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
        $data = $this->getArticleData($article);
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
     * @return ArticleType
     */
    private function getArticleType($typeName)
    {
        $requiredFields = array(
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

        $missingFields = array_diff(array_keys($requiredFields), $this->getFieldNames($type));
        foreach ($missingFields as $fieldName) {
            $field = new \ArticleTypeField($type->getTypeName(), $fieldName);
            $field->create($requiredFields[$fieldName]);
        }

        return $type;
    }

    /**
     * Get defined field names for type
     *
     * @param ArticleType $type
     * @return array
     */
    private function getFieldNames(\ArticleType $type)
    {
        return array_map(function($field) {
            return $field->getPrintName();
        }, $type->getUserDefinedColumns(null, true, true));
    }

    /**
     * Get article data
     *
     * @param Article $article
     * @return ArticleData
     */
    private function getArticleData(\Article $article)
    {
        $data = $article->getArticleData();

        if (!$data->exists()) {
            $data->create();
        }

        return $data;
    }
}
