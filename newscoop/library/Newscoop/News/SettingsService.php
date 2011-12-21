<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 * Settings Service
 */
class SettingsService
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
     * @var Doctrine\Common\Persistence\ObjectManager $odm
     */
    public function __construct(\Doctrine\ODM\MongoDB\DocumentManager $odm)
    {
        $this->odm = $odm;
        $this->repository = $this->odm->getRepository('Newscoop\News\Settings');
    }

    /**
     * Find settings by given id
     *
     * @param string $id
     * @return Newscoop\News\Settings
     */
    public function find($id)
    {
        $settings = $this->repository->find($id);
        if ($settings === null) {
            $settings = new Settings();
        }

        return $settings;
    }

    /**
     * Save settings
     *
     * @param Newscoop\News\Item $item
     * @return void
     */
    public function save(array $values, Settings $settings)
    {
        $settings->setArticleTypeName($values['article_type']);
        $settings->setPublicationId($values['publication']);
        $settings->setSectionNumber($values['section']);

        $this->odm->persist($settings);
        $this->odm->flush();
    }
}
