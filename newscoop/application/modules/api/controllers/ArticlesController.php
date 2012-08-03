<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\Article;
use Newscoop\Webcode\Manager;

require_once __DIR__ . '/AbstractController.php';
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleTopic.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleAttachment.php');

/**
 */
class Api_ArticlesController extends AbstractController
{
    const LANGUAGE = 5;

    const LIST_URI_PATH = 'api/articles/list';
    const ITEM_URI_PATH = 'api/articles/item';

    const LIST_LIMIT_DEFAULT = 15;
    const LIST_LIMIT_BYTOPIC = 30;

    private $client;

    /** @var Zend_Controller_Request_Http */
    private $request;

    /** @var array */
    private $response = array();

    /**
     * Init controller
     */
    public function init()
    {
        global $Campsite;

        $this->_helper->layout->disableLayout();
        $this->request = $this->getRequest();
        $this->language = $this->_helper->entity->getRepository('Newscoop\Entity\Language')->findOneBy(array('id' => self::LANGUAGE));
        $this->articleService = $this->_helper->service('article');
        $this->url = $Campsite['WEBSITE_URL'];
        $this->params = $this->request->getParams();

        if (empty($this->params['client'])) {
            $this->sendError('Not client provided', 500);
        }

        $this->initClient($this->params['client']);
        if (is_null($this->client['type'])) {
            $this->sendError('Invalid client', 500);
        }
    }

    /**
     * Default action controller
     */
    public function indexAction()
    {
        $this->_forward('list');
    }

    /**
     * Return list of articles
     *
     * @return Newscoop\API\Response
     */
    public function listAction()
    {
        $this->getHelper('contextSwitch')->addActionContext('list', 'json')->initContext();

        $criteria = array();
        $criteria[] = new ComparisonOperation('workflow_status', new Operator('is'), 'published');
        $params = $this->request->getParams();

        if (!empty($params['section_id'])) {
            $playlist = $this->_helper->entity->getRepository('Newscoop\Entity\Playlist')
                ->find($params['section_id']);
            if (empty($playlist)) {
                $this->sendError('Section not found', 404);
            }

            $articles = $this->_helper->entity->getRepository('Newscoop\Entity\Playlist')
                ->articles($playlist, null, false, self::LIST_LIMIT_DEFAULT);
        } else {
            if (!empty($params['type'])) {
                $criteria[] = new ComparisonOperation('type', new Operator('is'), (string) $params['type']);
            }
            if (!empty($params['topic_id'])) {
                /** @todo */
                $criteria[] = new ComparisonOperation('topic', new Operator('is'), $params['topic_id']);
            }

            $articles = \Article::GetList($criteria, null, 0, self::LIST_LIMIT_BYTOPIC, $count = 0, false, false);
        }

        foreach ($articles as $item) {
            $articleNumber = isset($playlist) ? $item['articleId'] : $item['number'];
            $article = $this->articleService->find($this->language, $articleNumber);
            if (!empty($article)) {
                $this->response[] = $this->formatArticle($article);
            }
        }

        $this->_helper->json($this->response);
    }

    /**
     * Send article info
     */
    public function itemAction()
    {
        $this->getHelper('contextSwitch')->addActionContext('item', 'json')->initContext();

        $id = $this->request->getParam('article_id');
        if (is_null($id)) {
            print Zend_Json::encode($this->response);
            return;
        }

        $article = $this->articleService->find($this->language, $this->request->getParam('article_id'));
        if (empty($article)) {
            return $this->_helper->json($response);
        }

        $articleData = new ArticleData($article->getType(), $article->getId(), self::LANGUAGE);
        if ($this->request->getParam('side') == 'back') {
            $data = $this->getBackside($article, $articleData);
            $template = 'article-backside.phtml';
        } else {
            $data = $this->getFrontside($article, $articleData);
            $template = 'article.phtml';
        }

        $smarty = CampTemplate::singleton();
        $smarty->setTemplateDir('/var/www/tw-reloaded/application/modules/api/views');
        $smarty->assign('data', $data);
        $smarty->display('scripts/articles/' . $template);
    }

    private function getBackside(Article $article, \ArticleData $adata)
    {
        $webcode = Manager::getWebcoder('')->encode($article->getId());
        $topics = ArticleTopic::GetArticleTopics($article->getId());
        $attachments = ArticleAttachment::GetAttachmentsByArticleNumber($article->getId(), self::LANGUAGE);
        try {
            $history = $adata->getFieldValue('history');
        } catch (\InvalidPropertyException $e) {
            $history = '';
        }

        try {
            $sources = $adata->getFieldValue('sources');
        } catch (\InvalidPropertyException $e) {
            $sources = '';
        }

        $authors = array();
        $aauthors = ArticleAuthor::GetAuthorsByArticle($article->getId(), self::LANGUAGE);
        foreach ($aauthors as $author) {
            $user = Zend_Registry::get('container')->getService('user')->findByAuthor($author->getId());
            $authors[] = array('user' => $user, 'author' => $author);
        }

        //$comments = Zend_Registry::get('container')->getService('comment')->findUserComments($params, $order, $p_limit, $p_start)

        $data = array(
            'title' => $article->getTitle(),
            'publish_date' => $article->getPublishDate(),
            'last_update' => $article->getDate(),
            'topics' => $topics,
            'history' => $history,
            'attachments' => $attachments,
            'sources' => $sources,
            'base_url' => $this->url,
            'webcode' => $webcode,
            'authors' => $authors,
        );

        return $data;
    }

    private function getFrontside(Article $article, \ArticleData $adata)
    {
        $comments = Zend_Registry::get('container')->getService('comment')->countBy(array(
            'language' => $this->language->getId(),
            'thread' => $article->getId(),
        ));

        $teaserField = 'teaser';
        $bodyField = 'body';
        if ($article->getType() == 'newswire') {
            $teaserField = 'DataLead';
            $bodyField = 'DataContent';
        }

        try {
            $dateline = $adata->getFieldValue('dateline');
        } catch (InvalidArgumentException $e) {
            $dateline = null;
        }

        return array(
            'article_id' => $article->getId(),
            'title' => $article->getTitle(),
            'dateline' => $dateline,
            'publish_date' => $article->getPublishDate(),
            'last_modified' => $article->getDate(),
            'teaser' => $adata->getFieldValue($teaserField),
            'body' => $adata->getFieldValue($bodyField),
            'image_url' => $this->getImageUrl($article),
            'comment_count' => $comments,
        );
    }

    private function initClient($client)
    {
        $type = null;
        if (strstr($client, 'ipad')) {
            $type = 'ipad';
        } elseif (strstr($client, 'iphone')) {
            $type = 'iphone';
        }

        $this->client = array(
            'name' => $client,
            'type' => $type,
        );
    }
}
