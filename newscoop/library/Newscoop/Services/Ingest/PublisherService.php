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

    /** @var array */
    private $config;

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
     * @param Newscoop\Entity\Ingest\Feed\Entry $entry
     * @param string $status
     * @return Article
     */
    public function publish(Entry $entry, $status = 'Y')
    {
        $article = new \Article($this->getLanguage($entry->getLanguage()));
        $article->create($this->config['article_type'], $entry->getTitle(), $this->getPublication(), $this->getIssue(), $this->getSection($entry));
        $article->setWorkflowStatus($status);
        $article->setKeywords($entry->getCatchWord());
        $this->setArticleData($article, $entry);
        $this->setArticleDates($article, $entry);
        $this->setArticleAuthors($article, $entry->getAuthors());
        $this->setArticleImages($article, $entry->getImages());
        $entry->setArticleNumber($article->getArticleNumber());
        $article->commit();
        return $article;
    }

    /**
     * Update published entry
     *
     * @param Newscoop\Entity\Ingest\Feed\Entry $entry
     * @return Article
     */
    public function update(Entry $entry)
    {
        $article = $this->getArticle($entry);
        $article->setTitle($entry->getTitle());
        $article->setProperty('time_updated', $entry->getUpdated()->format(self::DATETIME_FORMAT));
        $article->setKeywords($entry->getCatchWord());
        $this->setArticleData($article, $entry);
        $this->setArticleAuthors($article, $entry->getAuthors());
        $this->setArticleImages($article, $entry->getImages());
        return $article;
    }

    /**
     * Delete published entry
     *
     * @param Newscoop\Entity\Ingest\Feed\Entry $entry
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
    private function getLanguage($code)
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
    private function getPublication()
    {
        $publications = $GLOBALS['Campsite']['publications'];
        if (empty($publications)) {
            throw new \RuntimeException("No publications defined.");
        }

        return (int) $publications[count($publications) - 1]->getPublicationId();
    }

    /**
     * Get issue
     *
     * @return int
     */
    private function getIssue()
    {
        return (int) \Issue::GetCurrentIssue($this->getPublication())->getIssueNumber();
    }

    /**
     * Get section
     *
     * @param Newscoop\Entity\Ingest\Feed\Entry $entry
     * @return int
     */
    private function getSection(Entry $entry)
    {
        switch ($entry->getSubject()) {
            case '15000000':
                return $this->config['section_sport'];
                break;

            case '1000000':
                return $this->config['section_culture'];
                break;
        }

        if ($entry->getCountry() != 'CH')  {
            return $this->config['section_international'];
        }

        if ($entry->getProduct() == "Regionaldienst Nord") {
            return $this->config['section_basel'];
        }

        return $this->config['section_other'];
    }

    /**
     * Set article data
     *
     * @param Article $article
     * @param Newscoop\Entity\Ingest\Feed\Entry $entry
     * @return void
     */
    private function setArticleData(\Article $article, Entry $entry)
    {
        $data = $article->getArticleData();
        foreach ($this->config['field'] as $property => $getter) {
            $data->setProperty("F{$property}", $entry->$getter());
        }
        $data->create();
    }

    /**
     * Set article dates
     *
     * @param Article $article
     * @param Newscoop\Entity\Ingest\Feed\Entry $entry
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
     * @param Article $article
     * @param string $authors
     * @return void
     */
    private function setArticleAuthors(\Article $article, $authors)
    {
        if (empty($authors)) {
            return;
        }

        foreach (explode(',', $authors) as $authorName) {
            $authorName = trim($authorName);
            $author = new \Author(trim($authorName));
            if (!$author->exists()) {
                $author->create();
            }

            $article->setAuthor($author);
        }
    }

    /**
     * Set article images
     *
     * @param Article $article
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
     * @param Newscoop\Entity\Ingest\Feed\Entry $entry
     * @return Article
     */
    private function getArticle(Entry $entry)
    {
        return new \Article($this->getLanguage($entry->getLanguage()), $entry->getArticleNumber());
    }
}
