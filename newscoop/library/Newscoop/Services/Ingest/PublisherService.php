<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services\Ingest;

use Newscoop\Entity\Ingest\Feed\Entry;

/**
 * Ingest publisher service
 */
class PublisherService
{
    const DATETIME_FORMAT = 'Y-m-d H:i:s';

    const PROGRAM_TITLE = 'sda - WOCHENPROGRAMM';

    /** @var array */
    protected $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Publish entry
     *
     * @param Entry $entry
     * @param string $status
     * @return \Article
     */
    public function publish(Entry $entry, $status = 'Y')
    {
        $article = new \Article($this->getLanguage($entry->getLanguage()));
        $article->create($this->config['article_type'], $entry->getTitle(), $this->getPublication(), $this->getIssue(), $this->getSection($entry));
        $article->setWorkflowStatus(strpos($entry->getTitle(), self::PROGRAM_TITLE) === 0 ? 'N' : $status);
        $article->setKeywords($entry->getCatchWord());
        $article->setCommentsEnabled(TRUE);
        $this->setArticleData($article, $entry);
        $this->setArticleDates($article, $entry);
        $this->setArticleAuthors($article, $entry);
        $this->setArticleImages($article, $entry->getImages());
        $entry->setArticleNumber($article->getArticleNumber());
        $article->commit();
        return $article;
    }

    /**
     * Update published entry
     *
     * @param Entry $entry
     * @return null|\Article
     */
    public function update(Entry $entry)
    {
        if (!$entry->isPublished()) {
            return;
        }

        $article = $this->getArticle($entry);
        $article->setTitle($entry->getTitle());
        $article->setProperty('time_updated', $entry->getUpdated()->format(self::DATETIME_FORMAT));
        $article->setKeywords($entry->getCatchWord());
        $this->setArticleData($article, $entry);
        $this->setArticleAuthors($article, $entry);
        $this->setArticleImages($article, $entry->getImages());
        $entry->setPublished(new \DateTime());
        return $article;
    }

    /**
     * Delete published entry
     *
     * @param Entry $entry
     * @return void
     */
    public function delete(Entry $entry)
    {
        $article = $this->getArticle($entry);
        if ($article->exists()) {
            $article->delete();
        }
    }

    /**
     * Get language
     *
     * @param string $code
     * @return int
     */
    public function getLanguage($code)
    {
         $languages = \Language::GetLanguages(null, $code);
         if (empty($languages)) {
             throw new \InvalidArgumentException("Language with code '$code' not found");
         }

         return array_shift($languages)->getLanguageId();
    }

    /**
     * Get publication
     *
     * @return int
     */
    public function getPublication()
    {
        if (array_key_exists('default_publication_id', $this->config)) {
            return (int) $this->config['default_publication_id'];
        }
        
        $publications = $GLOBALS['Campsite']['publications'];
        if (empty($publications)) {
            throw new \RuntimeException("No publications defined.");
        }

        return (int) $publications[0]->getPublicationId();
    }

    /**
     * Get issue
     *
     * @return int
     */
    public function getIssue()
    {
        return (int) \Issue::GetCurrentIssue($this->getPublication())->getIssueNumber();
    }

    /**
     * Get section
     *
     * @param Entry $entry
     * @return int
     */
    public function getSection(Entry $entry)
    {
        if (array_key_exists($entry->getSubject(), $this->config)) {
            return $this->config[$entry->getSubject()];
        }

        return $this->config['section_other'];
    }

    /**
     * Set article data
     *
     * @param \Article $article
     * @param Entry $entry
     * @return void
     */
    private function setArticleData(\Article $article, Entry $entry)
    {
        $data = $article->getArticleData();
        foreach ($this->config['field'] as $property => $getter) {
            if (method_exists($entry, $getter)) {
                $data->setProperty("F{$property}", $entry->$getter());
            }
        }

        $data->create();
    }

    /**
     * Set article dates
     *
     * @param \Article $article
     * @param Entry $entry
     * @return void
     */
    private function setArticleDates(\Article $article, Entry $entry)
    {
        $entry->setPublished(new \DateTime());
        $article->setCreationDate($entry->getCreated()->format(self::DATETIME_FORMAT));
        $article->setPublishDate($entry->getPublished()->format(self::DATETIME_FORMAT));
        $article->setProperty('time_updated', $entry->getUpdated()->format(self::DATETIME_FORMAT));
    }

    /**
     * Set article authors
     *
     * @param \Article $article
     * @param Entry $entry
     * @return void
     */
    private function setArticleAuthors(\Article $article, Entry $entry)
    {
        $name = $entry->getFeed()->getTitle();
        $author = new \Author($name);
        if (!$author->exists()) {
            $author->create();
        }

        $article->setAuthor($author);
    }

    /**
     * Set article images
     *
     * @param \Article $article
     * @param array $images
     * @return void
     */
    private function setArticleImages(\Article $article, $images)
    {
        if (empty($images)) {
            return;
        }

        $oldImages = \ArticleImage::GetImagesByArticleNumber($article->getArticleNumber());
        foreach ($oldImages as $image) {
            $image->delete();
        }

        foreach ($images as $basename => $caption) {
            $file = $this->config['image_path'] . "/$basename";
            $realpath = realpath($file);
            if (!$realpath) {
                continue;
            }

            $imagesize = getimagesize($realpath);
            $info = array(
                'name' => $basename,
                'type' => $imagesize['mime'],
                'tmp_name' => $realpath,
                'size' => filesize($realpath),
                'error' => 0,
            );

            $attributes = array(
                'Photographer' => 'sda',
                'Description' => $caption,
                'Source' => 'newsfeed',
            );

            try {
                $image = \Image::OnImageUpload($info, $attributes, null, null, true);
                \ArticleImage::AddImageToArticle($image->getImageId(), $article->getArticleNumber(), null);
            } catch (\Exception $e) {
                var_dump($e);
                exit;
            }
        }
    }

    /**
     * Get article for entry
     *
     * @param Entry $entry
     * @return \Article
     */
    private function getArticle(Entry $entry)
    {
        return new \Article($this->getLanguage($entry->getLanguage()), $entry->getArticleNumber());
    }
}
