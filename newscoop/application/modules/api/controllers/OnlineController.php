<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\Article;

require_once __DIR__ . '/AbstractController.php';

/**
 * Issues Service
 */
class Api_OnlineController extends AbstractController
{
    const LANGUAGE_ID = 1;

    /**
     * @var int
     */
    private $rank = 1;

    /**
     * @var Tageswoche\Mobile\IssueFacade
     */
    private $service;

    /**
     * @var array
     */
    private $commentStats = array();

    /** @var array */
    private $sections = array();

    public function init()
    {
        $this->service = $this->_helper->service('mobile.issue');
        $this->_helper->layout->disableLayout();
    }

    public function issuesAction()
    {
        $this->_helper->json(array_map(array($this, 'formatIndexIssue'), $this->service->findAll()));
    }

    public function tocAction()
    {
        if (!$this->_getParam('id')) {
            $this->sendError();
        }

        $this->issue = $this->service->find($this->_getParam('id'));
        if (empty($this->issue)) {
            $this->sendError('Issue not found.', 404);
        }

        $this->_helper->json($this->formatTocIssue($this->issue));
    }

    public function articlesAction()
    {
        $this->assertIsSubscriber();

        $article = $this->_helper->service('article')->findOneByNumber($this->_getParam('id'));
        if (!$article) {
            $this->sendError('Article not found.', 404);
        }

        $this->view->article = new MetaArticle($article->getLanguageId(), $article->getNumber());
    }

    /**
     * Format issue for list
     *
     * @param Newscoop\Entity\Article $issue
     * @return array
     */
    private function formatIndexIssue(Article $issue)
    {
        return array(
            'issue_id' => $issue->getNumber(),
            'url' => $this->view->serverUrl($this->view->url(array(
                'controller' => 'online',
                'action' => 'toc',
                'id' => $issue->getNumber(),
            ), 'api')) . $this->getClientVersionParams(),
            'cover_url' => $this->getCoverUrl($issue),
            'title' => $issue->getTitle(),
            'year' => (int) $issue->getPublished()->format('Y'),
            'month' => (int) $issue->getPublished()->format('m'),
            'rank' => $this->rank++,
        );
    }

    /**
     * Get cover image url
     *
     * @param Newscoop\Entity\Article $issue
     * @return string
     */
    private function getCoverUrl(Article $issue)
    {
        $image = $issue->getImage();
        return $image ? $this->view->serverUrl($this->view->baseUrl($image->getPath())) : null;
    }

    /**
     * Format toc issue
     *
     * @param Newscoop\Entity\Article $issue
     * @return array
     */
    private function formatTocIssue(Article $issue)
    {
        $articles = $this->service->getArticles($issue);
        $this->commentStats = $this->_helper->service('comment')->getArticleStats(array_map(function($article) {
            return $article->getNumber();
        }, $articles));

        $toc = array(
            'issue_id' => $issue->getNumber(),
            'cover_url' => $this->getCoverUrl($issue),
            'single_issue_product_id' => $issue->getData('issue_product_id'),
            'publication_date' => $issue->getPublished()->format('Y-m-d'),
            'last_modified' => $issue->getUpdated()->format(self::DATE_FORMAT),
            'articles' => array_map(array($this, 'formatArticle'), $articles),
        );

        $toc['last_modified'] = array_reduce($toc['articles'], function($prev, $article) {
            return $prev > $article['last_modified'] ? $prev : $article['last_modified'];
        });

        return $toc;
    }

    /**
     * Format article
     *
     * @param Newscoop\Entity\Article $article
     * @return array
     */
    protected function formatArticle(Article $article)
    {
        return array_merge(parent::formatArticle($article), array(
            'url' => $this->view->serverUrl($this->view->url(array(
                'controller' => 'online',
                'action' => 'articles',
                'id' => $article->getNumber(),
            ), 'api')) . $this->getClientVersionParams(),
            'section_id' => $this->getSectionId($article),
            'section_name' => $this->getSectionName($article),
            'section_rank' => $this->rank,
            'image_url' => $this->getCoverUrl($article),
            'article_quality' => 'prominent',
            'last_modified' => $article->getUpdated()->format(self::DATE_FORMAT),
            'published' => $article->getPublished()->format(self::DATE_FORMAT),
        ));
    }

    /**
     * Get section id
     *
     * @param Newscoop\Entity\Article $article
     * @return int
     */
    protected function getSectionId($article)
    {
        $section = $this->getSectionName($article);
        if ($section === null) {
            return null;
        }

        if (!isset($this->sections[$section])) {
            $this->sections[$section] = count($this->sections) + 1;
        }

        return $this->sections[$section];
    }

    /**
     * Get section name
     *
     * @param Newscoop\Entity\Article $article
     * @return string
     */
    protected function getSectionName($article)
    {
        try {
            return $article->getData('printsection');
        } catch (\Exception $e) {
            return null;
        }
    }
}
