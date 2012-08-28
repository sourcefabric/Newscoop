<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\Article;

require_once __DIR__ . '/AbstractController.php';

/**
 * Dossiers API service controller.
 */
class Api_DossiersController extends AbstractController
{
    const ARTICLE_TYPE = 'dossier';
    const PUBLICATION = 5;
    const LANGUAGE = 5;
    const IMAGE_STANDARD_WIDTH = 320;
    const IMAGE_STANDARD_HEIGHT = 140;
    const IMAGE_RETINA_WIDTH = 640;
    const IMAGE_RETINA_HEIGHT = 280;

    /**
     * Init controller.
     */
    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->params = $this->getRequest()->getParams();

        if (empty($this->params['client'])) {
            $this->sendError('Not client provided', 500);
        }
    }

    /**
     * Default action controller.
     */
    public function indexAction()
    {
        $this->_forward('list');
    }

    /**
     * Returns list of dossiers.
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
            $date = new DateTime($article->getPublishDate());
            $response[] = array(
                'dossier_id' => $article->getNumber(),
                'url' => $this->view->serverUrl($this->view->url(array(
                    'module' => 'api',
                    'controller' => 'dossiers',
                    'action' => 'articles',
                ), 'default') . $this->getApiQueryString(array('dossier_id' => $article->getNumber()))),
                'title' => trim($article->getTitle()),
                'description' => trim(strip_tags($article->getData('lede'))),
                'image_url' => $this->getDossierImageUrl($article),
                'published' => $this->formatDate($date),
                'rank' => $rank++,
            );
        }

        $this->_helper->json($response);
    }

    /**
     * Returns list of articles in given dossier.
     *
     * @return json
     */
    public function articlesAction()
    {
        $this->getHelper('contextSwitch')->addActionContext('articles', 'json')->initContext();

        if ($this->params['dossier_id']) {
            $dossierId = $this->params['dossier_id'];
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

        $response = array();
        foreach ($articleIds as $articleId) {
            $article = $this->_helper->service('article')->find($language, $articleId);
            if (!empty($article)) {
                $response[] = $this->formatArticle($article);
            }
        }

        $this->_helper->json($response);
    }

    /**
     * Get image url
     *
     * @param Newscoop\Entity\Article $article
     * @return string
     */
    protected function getDossierImageUrl(Article $article)
    {
        $image = $article->getImage();
        if ($image) {
            return $this->view->serverUrl($this->view->url(array(
                'src' => $this->getHelper('service')->getService('image')->getSrc($image->getPath(), $this->getImageWidth(), $this->getImageHeight(), 'crop'),
            ), 'image', false, false));
        } else {
            return null;
        }
    }

    /**
     * Get image width
     *
     * @return int
     */
    private function getImageWidth()
    {
        return $this->isRetinaClient() ? self::IMAGE_RETINA_WIDTH : self::IMAGE_STANDARD_WIDTH;
    }

    /**
     * Get image height
     *
     * @return int
     */
    private function getImageHeight()
    {
        return $this->isRetinaClient() ? self::IMAGE_RETINA_HEIGHT : self::IMAGE_STANDARD_HEIGHT;
    }
}
