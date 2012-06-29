<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Api_DossiersController extends Zend_Controller_Action
{
    const ARTICLE_TYPE = 'dossier';
    const PUBLICATION = 5;
    const LANGUAGE = 5;
    
    /** @var Zend_Controller_Request_Http */
    private $request;
    
    private $url;


    /**
     * Init controller.
     */
    public function init()
    {
        global $Campsite;
        
        $this->_helper->layout->disableLayout();
        $this->request = $this->getRequest();
        $this->url = $Campsite['WEBSITE_URL'];
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
            $imageUrl = isset($articleImages[0]) ? $articleImages[0]->getImage()->getImageUrl() : '';

            $response[] = array(
                'dossier_id' => $article->getNumber(),
                'url' => $this->url.'/api/dossiers/articles?dossier_id='.$article->getNumber(),
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
        
        $dossier = $this->_helper->service('article')
            ->findBy(array('number' => $dossierId, 'language' => self::LANGUAGE));
        $contextBox = new ContextBox(null, $dossierId);
        $articleIds = $contextBox->getArticlesList();
        
        foreach ($articleIds as $articleId) {
            $article = new Article(self::LANGUAGE, $articleId);
            $response[] = array(
                'id' => $article->getArticleNumber(),
                'url' => $this->url.'/api/articles/item?article_id='.$article->getArticleNumber(),
                'title' => $article->getTitle()
            );
        }
        
        $this->_helper->json($response);
    }
}
