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
        $this->view->headScript()->appendFile($this->view->baseUrl('/public/js/jquery.qtip.min.js'));
        $this->view->headScript()->appendFile($this->view->baseUrl('/public/js/jquery.wobscalendar.js'));

        $this->view->headLink()->appendStylesheet($this->view->baseUrl('/public/css/jquery.qtip.css'));
        $this->view->headLink()->appendStylesheet($this->view->baseUrl('/public/css/wobs_calendar.css'));

        $request = $this->getRequest();

        $view = $request->getParam('view', "month");
        $this->view->defaultView = $view;

        $date = $request->getParam('date', date("Y/m/d"));
        $date = explode("/", $date);

        $today = date("Y/m/d");
        $today = explode("/", $today);
        $this->view->today = $today;

        if (isset($date[0])) {
            $this->view->year = $date[0];
        }
        if (isset($date[1])) {
            $this->view->month = $date[1]-1;
        }
        if (isset($date[2])) {
            $this->view->day = $date[2];
        }
        else if (!isset($date[2]) && ($view === "month")) {
            $this->view->day = 1;
        }

        $now = new DateTime("$today[0]-$today[1]");

        //oldest month user can scroll to YYYY/mm
        $earliestMonth = $request->getParam('earliestMonth', null);
        if (isset($earliestMonth) && $earliestMonth == "current") {
            $this->view->earliestMonth = $today;
        }
        else if (isset($earliestMonth)) {

            $earliestMonth = explode("/", $earliestMonth);
            $tmp_earliest = new DateTime("$earliestMonth[0]-$earliestMonth[1]");

            if ($tmp_earliest > $now) {
                $earliestMonth = $today;
            }

            $this->view->earliestMonth = $earliestMonth;
        }
        else {
            $this->view->earliestMonth = null;
        }

        //most recent month user can scroll to YYYY/mm
        $latestMonth = $request->getParam('latestMonth', null);
        if (isset($latestMonth) && $latestMonth == "current") {
            $this->view->latestMonth = $today;
        }
        else if (isset($latestMonth)) {
            $latestMonth = explode("/", $latestMonth);
            $tmp_latest = new DateTime("$latestMonth[0]-$latestMonth[1]");

            if ($now > $tmp_latest) {
                $latestMonth = $today;
            }

            $this->view->latestMonth = $latestMonth;
        }
        else {
            $this->view->latestMonth = null;
        }

        $this->view->nav = $request->getParam('navigation', true);
        $this->view->firstDay = $request->getParam('firstDay', 0);
        $this->view->dayNames = $request->getParam('showDayNames', true);
        $this->view->rand_int = md5(uniqid("", true));
    }

    public function articleOfTheDayAction()
    {
        $params = $this->getRequest()->getParams();

        //TODO parse these to make sure are times.
        $start_date = $params['start'];
        $end_date = $params['end'];

        $articles = Article::GetArticlesOfTheDay($start_date, $end_date);

        //get what we need for the json returned data.
        $results = array();

        foreach ($articles as $article) {
            $article_number = $article->getArticleNumber();

            $json = array();

            $json['title'] = $article->getTitle();

            $images = ArticleImage::GetImagesByArticleNumber($article_number);

            if (count($images) > 0) {
                $image = $images[0];
                $json['image'] = $this->view->baseUrl("/get_img?ImageWidth=164&ImageId=".$image->getImageId());
            }
            else {
                $json['image'] = null;
            }

            $date = $article->getPublishDate();
            $date = explode(" ", $date);
            $YMD = explode("-", $date[0]);

            //month-1 is for js, months are 0-11.
            $json['date'] = array("year"=>intval($YMD[0]), "month"=>intval($YMD[1]-1), "day"=>intval($YMD[2]));

            $json['url'] = ShortURL::GetURL($article->getPublicationId(), $article->getLanguageId(), null, null, $article_number);

            $results[] = $json;
        }

        $this->view->articles = $results;
    }
}
