<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

class ArticleofthedayController extends Zend_Controller_Action
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('article-of-the-day', 'json')
                    ->initContext();
    }

    public function indexAction()
    {
        $this->view->gimme = CampTemplate::singleton()->context();
    }

    public function articleOfTheDayAction()
    {
        $params = $this->getRequest()->getParams();
        $publication_id = $params['publication_id'];
        $language_id = $params['language_id'];

        $articles = Article::GetArticlesOfTheDay(null, null, $publication_id, $language_id);

        //get what we need for the json returned data.
        $results = array();

        foreach ($articles as $article) {
            $json = array();

            $images = ArticleImage::GetImagesByArticleNumber($article->getArticleNumber());
            $image = $images[0];

            $json['title'] = $article->getTitle();
            $json['image'] = $this->view->baseUrl("/get_img?ImageWidth=100&ImageId=".$image->getImageId());

            $results[] = $json;
        }

        $this->view->articles = $results;
    }
}
