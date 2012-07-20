<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

require_once __DIR__ . '/AbstractController.php';

/**
 * Dossiers API service controller.
 */
class Api_DossiersController extends AbstractController
{
    const ARTICLE_TYPE = 'dossier';
    const PUBLICATION = 5;
    const LANGUAGE = 5;

    /** @var Zend_Controller_Request_Http */
    private $request;

    /**
     * Init controller.
     */
    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->request = $this->getRequest();
    }

    /**
     * Default action controller.
     */
    public function indexAction()
    {
        $this->_forward('list');
    }

    /**
     * Return list of dossiers.
     *
     * @return json
     */
    public function listAction()
    {
        $this->getHelper('contextSwitch')->addActionContext('list', 'json')->initContext();

        $articles = $this->_helper->service('article')->findBy(array('type' => self::ARTICLE_TYPE));
        $response = array();
        $rank = 1;
        foreach ($articles as $article) {
            $articleData = new ArticleData(self::ARTICLE_TYPE, $article->getNumber(), self::LANGUAGE);
            $articleImages = ArticleImage::GetImagesByArticleNumber($article->getNumber());
            $imageUrl = isset($articleImages[0]) ? $articleImages[0]->getImage()->getImageUrl() : null;

            $response[] = array(
                'dossier_id' => $article->getNumber(),
                'url' => $this->view->serverUrl($this->view->url(array(
                    'module' => 'api',
                    'controller' => 'dossiers',
                    'action' => 'articles',
                ), 'default') . $this->getApiQueryString(array('dossier_id' => $article->getNumber()))),
                'title' => $article->getTitle(),
                'description' => trim(strip_tags($articleData->getFieldValue('lede'))),
                'image_url' => $imageUrl,
                'published' => $article->getPublishDate(),
                'rank' => $rank++,
            );
        }

        $this->_helper->json($response);
    }

    /**
     * Return list of articles in given dossier.
     *
     * @return json
     */
    public function articlesAction()
    {
        $this->getHelper('contextSwitch')->addActionContext('articles', 'json')->initContext();

        $response = array();
        $parameters = $this->request->getParams();

        if ($parameters['dossier_id']) {
            $dossierId = $parameters['dossier_id'];
        } else {
            $articles = $this->_helper->service('article')
                ->findBy(array('type' => self::ARTICLE_TYPE), array('published' => 'desc'));
            $dossierId = $articles[0]->getNumber();
        }

        $language = $this->_helper->entity->getRepository('Newscoop\Entity\Language')->findOneBy(array('id' => self::LANGUAGE));
        $dossier = $this->_helper->service('article')
            ->findBy(array('number' => $dossierId, 'language' => self::LANGUAGE));
        $contextBox = new ContextBox(null, $dossierId);
        $articleIds = $contextBox->getArticlesList();

        foreach ($articleIds as $articleId) {
            $article = $this->_helper->service('article')->find($language, $articleId);
            if (!empty($article)) {
                $response[] = $this->formatArticle($article);
            }
        }

        $this->_helper->json($response);
    }
}
