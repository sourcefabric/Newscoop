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
class Api_IssuesController extends AbstractController
{
    /**
     * @var int
     */
    private $rank = 1;

    /**
     * @var Tageswoche\Mobile\IssueFacade
     */
    private $service;

    public function init()
    {
        $this->service = $this->_helper->service('mobile.issue');
    }

    public function preDispatch()
    {
        if ($this->getRequest()->getActionName() === 'index' && $this->_getParam('id')) {
            $this->_forward('toc');
        }
    }

    public function indexAction()
    {
        $this->_helper->json(array_map(array($this, 'formatIndexIssue'), $this->service->findAll()));
    }

    public function tocAction()
    {
        if (!$this->_getParam('id')) {
            $this->sendError();
        }

        $issue = $this->service->find($this->_getParam('id'));
        if (empty($issue)) {
            $this->sendError('Issue not found.', 404);
        }

        $this->_helper->json($this->formatTocIssue($issue));
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
            'url' => $this->view->serverUrl($this->view->baseUrl(sprintf('/issues/%d', $issue->getNumber()))),
            'cover_url' => null,
            'title' => $issue->getTitle(),
            'year' => (int) $issue->getPublished()->format('Y'),
            'month' => (int) $issue->getPublished()->format('m'),
            'rank' => $this->rank++,
        );
    }

    /**
     * Format toc issue
     *
     * @param Newscoop\Entity\Article $issue
     * @return array
     */
    private function formatTocIssue(Article $issue)
    {
        return array_merge($this->formatIndexIssue($issue), array(
            'single_issue_product_id' => null,
            'publication_date' => $issue->getPublished()->format('Y-m-d'),
            'articles' => array_map(array($this, 'formatArticle'), $this->service->getArticles($issue)),
        ));
    }

    /**
     * Format article
     *
     * @param Newscoop\Entity\Article $article
     * @return array
     */
    private function formatArticle(Article $article)
    {
        return array(
            'article_id' => $article->getNumber(),
            'url' => '',
            'dateline' => '',
            'short_name' => $article->getTitle(),
            'website_url' => $this->_helper->service('article.link')->getLink($article),
            'image_url' => '',
            'topics' => array(),
            'comment_count' => 0,
            'comment_url' => '',
            'article_quality' => 'prominent',
        );
    }
}
