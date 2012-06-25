<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleTopic.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleImage.php');

/**
 */
class Api_CinemaController extends Zend_Controller_Action
{
    const API_VERSION = 1;
    const BASE_URL = '/api/cinema';

    const PUBLICATION = 1;
    const LANGUAGE = 5;
    const EV_SECTION = 72; // movies
    const EV_TYPE = 'screening';

    /** @var Zend_Controller_Request_Http */
    private $request;

    /** @var array */
    private $m_client;

    /** @var Newscoop\Services\AgendaService */
    private $m_service;

    private $req_date;

    /**
     *
     */
    public function init()
    {
        global $Campsite;

        $this->m_service = $this->_helper->service('agenda');

        //$this->_helper->layout->disableLayout();
        //$this->params = $this->getRequest()->getParams();
        //$this->url = $Campsite['WEBSITE_URL'];
/*
        if (empty($this->params['client'])) {
            print Zend_Json::encode(array());
            exit;
        }
        $this->initClient($this->params['client']);
        if (is_null($this->client['type'])) {
            print Zend_Json::encode(array());
            exit;
        }
*/
    }

    /**
     */
    public function indexAction()
    {
        $this->_forward('list');
    }

    /**
     * Serve list of sections.
     */
    public function listAction()
    {
        $basically_correct = true;

        $param_version = $this->_request->getParam('version');
        $param_client = $this->_request->getParam('client');
        if (empty($param_version) || empty($param_client)) {
            $basically_correct = false;
        }
        if (!in_array($param_client, array('iphone', 'iphone_retina', 'ipad', 'ipad_retina'))) {
            $basically_correct = false;
        }

        if (!$basically_correct) {

            $output_data = array();
            //$output_json = json_encode($output_data);
            $output_json = Zend_Json::encode($output_data);

            header('Content-Type: application/json; charset=utf-8');
            header('Content-Length: ' . strlen($output_json));

            echo $output_json;
            exit(0);

        }


        $movie_list = $this->_innerListProcess();
//        echo count($event_list);

        $last_movie_name = '';

        $movie_types_reversed = array();
        foreach ($this->m_service->getMovieTypeList(array('country' => 'ch')) as $type_key => $type_info) {
            $movie_types_reversed[$type_info['topic']] = $type_info['outer']; // $type_key;
        }

        $movie_list_data = array();
        $movie_locations_used = array();
        $movie_locations_data = array();
        foreach ($movie_list as $one_movie) {
            $one_movie_types = array();
            $one_data = $one_movie->getArticleData();
            $one_movie_topics = \ArticleTopic::GetArticleTopics($one_movie->getArticleNumber());
            foreach ($one_movie_topics as $one_topic) {
                $one_topic_name = $one_topic->getName(self::LANGUAGE);
                if (array_key_exists($one_topic_name, $movie_types_reversed)) {
                    $one_movie_types[] = $movie_types_reversed[$one_topic_name];
                }
            }
            if (empty($one_movie_types)) {
                $one_movie_types = null;
            }

            $one_date_time = null;
            $one_canceled = false;

            $one_web = $one_data->getProperty('Fweb');
            if (empty($one_web)) {
                $one_web = null;
            }
            $one_email = $one_data->getProperty('Femail');
            if (empty($one_email)) {
                $one_email = null;
            }

            $one_location_id = null;
            try {
                $one_location_id = $one_data->getProperty('Fmovie_cinema_key');
            }
            catch (\InvalidPropertyException $exc) {
                $one_location_id = null;
            }
            if (!$one_location_id) {
                $one_location_id = $one_data->getProperty('Flocation_id');
            }

            if (!isset($movie_locations_used[$one_location_id])) {
                $movie_locations_used[$one_location_id] = true;
                $movie_locations_data[] = array(
                    'location_id' => $one_location_id,
                    'location_name' => $one_data->getProperty('Forganizer'),
                    'zipcode' => $one_data->getProperty('Fzipcode'),
                    'town' => $one_data->getProperty('Ftown'),
                    'street' => $one_data->getProperty('Fstreet'),
                    'web' => $one_web,
                    'email' => $one_email,
                );
            }

            if ($last_movie_name != $one_data->getProperty('Fheadline')) {
                $one_suisa = $one_data->getProperty('Fmovie_suisa');
                if (!$one_suisa) {
                    $one_suisa = null;
                }

                $one_min_age = '' . $one_data->getProperty('Fminimal_age_category');
                if ((!$one_min_age) || ('99' == $one_min_age)) {
                    $one_min_age = '16';
                }
                $one_min_age = ltrim($one_min_age, '0');
                if ('' == $one_min_age) {
                    $one_min_age = '3-6 Jahre';
                }
                else {
                    $one_min_age = 'ab ' . $one_min_age . ' Jahren';
                }

                $one_movie_trailer = $one_data->getProperty('Fmovie_trailer_vimeo');
                if (!$one_movie_trailer) {
                    $one_movie_trailer = null;
                }
                else {
                    $one_movie_trailer = 'http://vimeo.com/' . $one_movie_trailer;
                }

                $one_movie_director = $one_data->getProperty('Fmovie_director');
                if (!$one_movie_director) {
                    $one_movie_director = null;
                }
                else {
                    $one_movie_director = str_replace(array(','), array(', '), $one_movie_director);
                }

                $one_movie_cast = $one_data->getProperty('Fmovie_cast');
                if (!$one_movie_cast) {
                    $one_movie_cast = null;
                }
                else {
                    $one_movie_cast = str_replace(array(','), array(', '), $one_movie_cast);
                }

                $one_year = $one_data->getProperty('Fmovie_year');
                if (!$one_year) {
                    $one_year = null;
                }
                else {
                    $one_year = '' . $one_year;
                }

                $one_duration = $one_data->getProperty('Fmovie_duration');
                if (empty($one_duration)) {
                    $one_duration = null;
                }
                else {
                    $one_duration = '' . $one_duration . ' Minuten';
                }

                $one_distributor = $one_data->getProperty('Fmovie_distributor');
                if (!$one_distributor) {
                    $one_distributor = null;
                }

                $one_rating = $one_data->getProperty('Fmovie_rating_wv');
                if (empty($one_rating)) {
                    $one_rating = null;
                }
                else {
                    $one_rating = 0 + $one_rating;
                }

                $one_image_id = null;
                $articleImage = new \ArticleImage($one_movie->getArticleNumber(), null, 1);
                if ($articleImage->exists()) {
                    $one_image_id = $articleImage->getImageId();
                }

                $one_image_url = null;
                if (!empty($one_image_id)) {
                    $one_image_url = '/get_img?ImageWidth=188&ImageId=' . $one_image_id;
                }

                $one_movie_data = array(
                    'suisa' => $one_suisa,
                    'title' => $one_data->getProperty('Fheadline'),
                    'synopsis' => $one_data->getProperty('Fdescription'),
                    //'organizer' => $one_data->getProperty('Forganizer'),
                    'genres' => $one_movie_types,
                    'minimal_age' => $one_min_age,
                    'trailer_url' => $one_movie_trailer,
                    'image_url' => $one_image_url, // 'http://www.tageswoche.ch/images/cache/555x370/crop/images%7Ccms-image-000042058.png',
                    'movie_detail_url' => self::BASE_URL . '/movie?key=' . urlencode('' . $one_data->getProperty('Fmovie_key')),
                    'director' => $one_movie_director,
                    'actors' => $one_movie_cast,
                    'year' => $one_year,
                    'duration' => $one_duration,
                    'distributor' => $one_distributor,
                    'rating' => $one_rating,
                    'screenings' => array(),
                );

                $movie_list_data[] = $one_movie_data;

            }
            // to add screening info
            $last_movie_data = array_pop($movie_list_data);
            $one_movie_screen_info = array(
                'location_id' => $one_location_id,
                'times' => array(),
            );

            $one_date_screening_info = $this->m_service->getMovieDateInfo($one_movie, $this->req_date);

            foreach ($one_date_screening_info as $one_date_info) {
                $one_movie_screen_info['times'][] = array(
                    'date_time' => $one_date_info['date'] . ' ' . $one_date_info['time'],
                    'languages' => $one_date_info['lang'],
                    'canceled' => false,
                );
            }

            $last_movie_data['screenings'][] = $one_movie_screen_info;

            $movie_list_data[] = $last_movie_data;

            $last_movie_name = $one_data->getProperty('Fheadline');

        }

        $cur_date = date('Y-m-d');
        $cur_date_time = date('Y-m-d H:i:s');

        $output_regions = array();
        $output_region_rank = -1;
        foreach ($this->m_service->getRegionList(array('country' => 'ch')) as $region_key => $region_info) {
            $output_region_rank += 1;
            $output_regions[] = array(
                'region_id' => $region_key,
                'region_name' => $region_info['label'],
                'rank' => $output_region_rank,
            );
        }

        $output_types = array();
        $output_type_rank = -1;
        foreach ($this->m_service->getMovieTypeList(array('country' => 'ch')) as $type_key => $type_info) {
            $output_type_rank += 1;
            $output_types[] = array(
                'genre_id' => $type_info['outer'], // $type_key,
                'genre_name' => $type_info['label'],
                'rank' => $output_type_rank,
            );
        }

        if (empty($movie_locations_data)) {
            $movie_locations_data = null;
        }
        if (empty($movie_list_data)) {
            $movie_list_data = null;
        }

        $last_modified_regions = '2012-06-19 18:00:00';
        $last_modified_genres = '2012-06-19 18:00:00';
        $output_data = array(
            'date' => $cur_date,
            'regions_last_modified' => $last_modified_regions,
            'regions' => $output_regions,
            'genres_last_modified' => $last_modified_genres,
            'genres' => $output_types,
            'locations' => $movie_locations_data,
            'films' => $movie_list_data,
        );

        //$output_json = json_encode($output_data);
        $output_json = Zend_Json::encode($output_data);

        header('Content-Type: application/json; charset=utf-8');
        header('Content-Length: ' . strlen($output_json));

        echo $output_json;

        exit(0);

    }

    /**
     * Serve list of sections.
     */
    private function _innerListProcess()
    {
        $empty_res = array();

        $param_date = $this->_request->getParam('date');
        $param_region = $this->_request->getParam('region');
        //$param_type = $this->_request->getParam('genre'); // $this->_request->getParam('type'); // not required ?
        if (empty($param_date)) {
            $param_date = date('Y-m-d');
        }
        if (empty($param_region)) {
            $param_region = 'kanton-basel-stadt';
        }

        $this->req_date = $this->m_service->getRequestDate($param_date);
        if (!$this->req_date) {
            return $empty_res;
        }

        $req_region = null;
        $region_list = $this->m_service->getRegionList(array('country' => 'ch'));
        foreach ($region_list as $region_key => $region_info) {
            if ($region_key == $param_region) {
                $req_region = $region_info['topic'];
                break;
            }
        }
        if (!$req_region) {
            return $empty_res;
        }

        $params = array();

        $params['event_date'] = $this->req_date;
        $params['event_region'] = $req_region;
        //$params['event_type'] = $req_type;

        $params['publication'] = self::PUBLICATION;
        $params['language'] = self::LANGUAGE;
        $params['section'] = self::EV_SECTION;
        $params['article_type'] = self::EV_TYPE;

        $params['order'] = array(array('field' => 'byname', 'dir' => 'asc'));
        $params['multidate'] = 'movie_screening';

        $movies = $this->m_service->getEventList($params);

        return $movies;

/*
        $this->getHelper('contextSwitch')->addActionContext('list', 'json')->initContext();
        print Zend_Json::encode($events);
*/
    }



/*
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
*/

}

