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

        //TODO parse these to make sure are times.
        $start_date = $params['start'];
        $end_date = $params['end'];

        $articles = Article::GetArticlesOfTheDay($start_date, $end_date, $publication_id, $language_id);

        //get what we need for the json returned data.
        $results = array();

        foreach ($articles as $article) {
            $json = array();

            $images = ArticleImage::GetImagesByArticleNumber($article->getArticleNumber());
            $image = $images[0];

            $json['title'] = $article->getTitle();
            $json['image'] = $this->view->baseUrl("/get_img?ImageWidth=100&ImageId=".$image->getImageId());

            $date = $article->getPublishDate();
            $date = explode(" ", $date);
            $YMD = explode("-", $date[0]);

            //month-1 is for js, months are 0-11.
            $json['date'] = array("year"=>intval($YMD[0]), "month"=>intval($YMD[1]-1), "day"=>intval($YMD[2]));

            $results[] = $json;
        }

        $this->view->articles = $results;
    }
}
