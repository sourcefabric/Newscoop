<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/TopicName.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/Operator.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/ComparisonOperation.php');

use Doctrine\ORM\EntityManager;

/**
 * Agenda service
 */
class AgendaService
{

    /**
     */
    public function __construct()
    {
    }

    public function getEventList($p_params)
    {
        $empty_list = array();

        if (!isset($p_params['event_date'])) {
            return $empty_list;
        }
        if (!isset($p_params['event_region'])) {
            return $empty_list;
        }
        //if (!isset($p_params['event_type'])) {
        //    return $empty_list;
        //}

        if (!isset($p_params['publication'])) {
            return $empty_list;
        }
        if (!isset($p_params['language'])) {
            return $empty_list;
        }
        if (!isset($p_params['section'])) {
            return $empty_list;
        }

        if (!isset($p_params['article_type'])) {
            return $empty_list;
        }

        $use_publication = $p_params['publication']; // 3;
        $use_language = $p_params['language']; // 5;
        $use_section = $p_params['section']; // 71;
        $use_type = $p_params['article_type']; // 'event';


        $use_date = $p_params['event_date']; // '2012-06-06';

        //$use_topics = array('99', '134');
        $use_topics = array();

        $region_topic = new \TopicName($p_params['event_region'], $p_params['language']);
        $region_topic_id = $region_topic->getTopicId();
        $use_topics[] = $region_topic_id;

        if (isset($p_params['event_type'])) {
            $type_topic = new \TopicName($p_params['event_type'], $p_params['language']);
            $type_topic_id = $type_topic->getTopicId();
            $use_topics[] = $type_topic_id;
        }

        $ev_parameters = array();

        if (true) {
            $multidate_field = 'schedule';
            if (isset($p_params['multidate'])) {
                $multidate_field = $p_params['multidate'];
            }

            $leftOperand = 'complex_date';
            $rightOperand = array($multidate_field => 'start_date: ' . $use_date . ', end_date: ' . $use_date);
            $operator = new \Operator('is', 'string');
            $constraint = new \ComparisonOperation($leftOperand, $operator, $rightOperand);
            $ev_parameters[] = $constraint;
        }

        if (true) {
            $leftOperand = 'IdPublication';
            $rightOperand = $use_publication;
            $operator = new \Operator('is', 'integer');
            $constraint = new \ComparisonOperation($leftOperand, $operator, $rightOperand);
            $ev_parameters[] = $constraint;
        }
        if (true) {
            $leftOperand = 'IdLanguage';
            $rightOperand = $use_language;
            $operator = new \Operator('is', 'integer');
            $constraint = new \ComparisonOperation($leftOperand, $operator, $rightOperand);
            $ev_parameters[] = $constraint;
        }
        if (true) {
            $leftOperand = 'published';
            $rightOperand = 'true';
            $operator = new \Operator('is', 'string');
            $constraint = new \ComparisonOperation($leftOperand, $operator, $rightOperand);
            $ev_parameters[] = $constraint;
        }

        if (true) {
            $leftOperand = 'section';
            $rightOperand = $use_section;
            $operator = new \Operator('is', 'integer');
            $constraint = new \ComparisonOperation($leftOperand, $operator, $rightOperand);
            $ev_parameters[] = $constraint;
        }
        if (true) {
            $leftOperand = 'type';
            $rightOperand = $use_type;
            $operator = new \Operator('is', 'string');
            $constraint = new \ComparisonOperation($leftOperand, $operator, $rightOperand);
            $ev_parameters[] = $constraint;
        }
        if (true) {
            foreach ($use_topics as $one_topic) {
                $leftOperand = 'topic';
                $rightOperand = $one_topic;
                $operator = new \Operator('is', 'topic');
                $constraint = new \ComparisonOperation($leftOperand, $operator, $rightOperand);
                $ev_parameters[] = $constraint;
            }
        }

        if (true) {
            $leftOperand = 'matchalltopics';
            $rightOperand = true;
            $operator = new \Operator('is', 'boolean');
            $constraint = new \ComparisonOperation($leftOperand, $operator, $rightOperand);
            $ev_parameters[] = $constraint;
        }

        $ev_order = array();
        if (isset($p_params['order'])) {
            $ev_order = $p_params['order'];
        }

        $ev_start = 0;
        $ev_limit = 0;
        $ev_count = 0;
        $ev_skipCache = false;
        $ev_returnObjs = true;

        $events = \Article::GetList($ev_parameters, $ev_order, $ev_start, $ev_limit, &$ev_count, $ev_skipCache, $ev_returnObjs);

        if (empty($events)) {
            return $empty_list;
        }

        return $events;
    }

    public function getRegionList($p_params)
    {
        if (isset($p_params['country']) && ('ch' != $p_params['country'])) {
            return array();
        }

        return array(
            'region-basel' => array('topic' => 'Region Basel', 'label' => 'Region Basel'),
            'kanton-basel-stadt' => array('topic' => 'Kanton Basel-Stadt', 'label' => 'Basel-Stadt'),
            'kanton-basel-landschaft' => array('topic' => 'Kanton Basel-Landschaft', 'label' => 'Basel-Landschaft'),
            'kanton-aargau' => array('topic' => 'Kanton Aargau', 'label' => 'Aargau'),
            'kanton-appenzell-ausserrhoden' => array('topic' => 'Kanton Appenzell Ausserrhoden', 'label' => 'Appenzell Ausserrhoden'),
            'kanton-appenzell-innerrhoden' => array('topic' => 'Kanton Appenzell Innerrhoden', 'label' => 'Appenzell Innerrhoden'),
            'kanton-bern' => array('topic' => 'Kanton Bern', 'label' => 'Bern'),
            'kanton-freiburg' => array('topic' => 'Kanton Freiburg', 'label' => 'Freiburg'),
            'kanton-genf' => array('topic' => 'Kanton Genf', 'label' => 'Genf'),
            'kanton-glarus' => array('topic' => 'Kanton Glarus', 'label' => 'Glarus'),
            'kanton-graubuenden' => array('topic' => 'Kanton Graubünden', 'label' => 'Graubünden'),
            'kanton-jura' => array('topic' => 'Kanton Jura', 'label' => 'Jura'),
            'kanton-luzern' => array('topic' => 'Kanton Luzern', 'label' => 'Luzern'),
            'kanton-neuenburg' => array('topic' => 'Kanton Neuenburg', 'label' => 'Neuenburg'),
            'kanton-nidwalden' => array('topic' => 'Kanton Nidwalden', 'label' => 'Nidwalden'),
            'kanton-obwalden' => array('topic' => 'Kanton Obwalden', 'label' => 'Obwalden'),
            'kanton-schaffhausen' => array('topic' => 'Kanton Schaffhausen', 'label' => 'Schaffhausen'),
            'kanton-schwyz' => array('topic' => 'Kanton Schwyz', 'label' => 'Schwyz'),
            'kanton-solothurn' => array('topic' => 'Kanton Solothurn', 'label' => 'Solothurn'),
            'kanton-st-gallen' => array('topic' => 'Kanton St. Gallen', 'label' => 'St. Gallen'),
            'kanton-tessin' => array('topic' => 'Kanton Tessin', 'label' => 'Tessin'),
            'kanton-thurgau' => array('topic' => 'Kanton Thurgau', 'label' => 'Thurgau'),
            'kanton-uri' => array('topic' => 'Kanton Uri', 'label' => 'Uri'),
            'kanton-waadt' => array('topic' => 'Kanton Waadt', 'label' => 'Waadt'),
            'kanton-wallis' => array('topic' => 'Kanton Wallis', 'label' => 'Wallis'),
            'kanton-zug' => array('topic' => 'Kanton Zug', 'label' => 'Zug'),
            'kanton-zuerich' => array('topic' => 'Kanton Zürich', 'label' => 'Zürich'),
        );
    }

    public function getEventTypeList($p_params)
    {
        if (isset($p_params['country']) && ('ch' != $p_params['country'])) {
            return array();
        }

        return array(
            'theater' => array('topic' => 'Theater Veranstaltung', 'label' => 'Theater', 'outer' => 'theater'),
            'musik' => array('topic' => 'Musik Veranstaltung', 'label' => 'Konzerte', 'outer' => 'concert'),
            'party' => array('topic' => 'Party Veranstaltung', 'label' => 'Partys', 'outer' => 'party'),
            'ausstellung' => array('topic' => 'Ausstellung Veranstaltung', 'label' => 'Ausstellungen', 'outer' => 'exhibit'),
            'andere' => array('topic' => 'Andere Veranstaltung', 'label' => 'Diverse', 'outer' => 'misc'),
        );

    }

    public function getMovieTypeList($p_params)
    {
        if (isset($p_params['country']) && ('ch' != $p_params['country'])) {
            return array();
        }

        return array(
            'adventure' => array('topic' => 'Abenteuer Film', 'label' => 'Abenteuer', 'outer' => 'abenteuer'),
            'action' => array('topic' => 'Action Film', 'label' => 'Action', 'outer' => 'action'),
            'adult' => array('topic' => 'Adult Film', 'label' => 'Adult', 'outer' => 'adult'),
            'animation' => array('topic' => 'Animation Film', 'label' => 'Animation', 'outer' => 'animation'),
            'biografie' => array('topic' => 'Biografie Film', 'label' => 'Biografie', 'outer' => 'biografie'),
            'crime' => array('topic' => 'Crime Film', 'label' => 'Crime', 'outer' => 'crime'),
            'dokumentation' => array('topic' => 'Dokumentation Film', 'label' => 'Dokumentation', 'outer' => 'dokumentation'),
            'drama' => array('topic' => 'Drama Film', 'label' => 'Drama', 'outer' => 'drama'),
            'familienfilm' => array('topic' => 'Familienfilm Film', 'label' => 'Familienfilm', 'outer' => 'familienfilm'),
            'fantasy' => array('topic' => 'Fantasy Film', 'label' => 'Fantasy', 'outer' => 'fantasy'),
            'film-noir' => array('topic' => 'Film-Noir Film', 'label' => 'Film-Noir', 'outer' => 'film-noir'),
            'historischer' => array('topic' => 'Historischer Film', 'label' => 'Historisch', 'outer' => 'historisch'),
            'horror' => array('topic' =>  'Horror Film', 'label' => 'Horror', 'outer' => 'horror'),
            'komoedie' => array('topic' => 'Komödie Film', 'label' => 'Komödie', 'outer' => 'komoedie'),
            'kriegsfilm' => array('topic' => 'Kriegsfilm Film', 'label' => 'Kriegsfilm', 'outer' => 'kriegsfilm'),
            'kurzfilm' => array('topic' => 'Kurzfilm Film', 'label' => 'Kurzfilm', 'outer' => 'kurzfilm'),
            'musical' => array('topic' => 'Musical Film', 'label' => 'Musical', 'outer' => 'musical'),
            'musikfilm' => array('topic' => 'Musikfilm Film', 'label' => 'Musikfilm', 'outer' => 'musikfilm'),
            'mystery' => array('topic' => 'Mystery Film', 'label' => 'Mystery', 'outer' => 'mystery'),
            'romanze' => array('topic' => 'Romanze Film', 'label' => 'Romanze', 'outer' => 'romanze'),
            'sci-fi' => array('topic' => 'Sci-Fi Film', 'label' => 'Sci-Fi', 'outer' => 'sci-fi'),
            'sport' => array('topic' => 'Sport Film', 'label' => 'Sport', 'outer' => 'sport'),
            'thriller' => array('topic' => 'Thriller Film', 'label' => 'Thriller', 'outer' => 'thriller'),
            'western' => array('topic' => 'Western Film', 'label' => 'Western', 'outer' => 'western'),
            'andere' => array('topic' => 'Anderer Film', 'label' => 'Andere', 'outer' => 'andere'),
        );

    }

    public function getRequestDate($p_date)
    {
        $no_date = null;

        if (preg_match('/^[\d]{4}-[\d]{2}-[\d]{2}$/', $p_date)) {
            return $p_date;
        }

        if (!preg_match('/^[\d]{4}-[\d]{1,2}-[\d]{1,2}$/', $p_date)) {
            return $no_date;
        }

        $date_parts = explode('-', $p_date);
        if (3 != count($date_parts)) {
            return $no_date;
        }

        $date_res = $date_parts[0] . '-' . str_pad($date_parts[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($date_parts[2], 2, '0', STR_PAD_LEFT);
        return $date_res;
    }

    public function getRequestEventType($p_type)
    {
        $req_type = null; // 'Veranstaltung';

        if (!empty($p_type)) {
            $type_list = $this->getEventTypeList(array('country' => 'ch'));
            foreach ($type_list as $type_key => $type_info) {
                if ($type_info['outer'] == $p_type) {
                    $req_type = $type_info['topic'];
                    break;
                }
            }
        }

        return $req_type;
    }


    public function getMovieDateInfo($p_article, $p_date)
    {
        $no_info = array();

        if (!preg_match('/^[\d]{4}-[\d]{2}-[\d]{2}$/', $p_date)) {
            return $no_info;
        }

        $regular_types = array('movie_screening');

        $em = \Zend_Registry::get('container')->getService('em');
        $repo = $em->getRepository('Newscoop\Entity\ArticleDatetime');
        $res = $repo->findBy(array('articleId'=>$p_article->getArticleNumber()));

        if (empty($res)) {
            return $no_info;
        }

        $found_screens = array();

        foreach ($res as $one_date_entry) {
            $date_part = date_format($one_date_entry->getStartDate(), 'Y-m-d');
            if ($date_part != $p_date) {
                continue;
            }

            if (!in_array($one_date_entry->getFieldName(), $regular_types)) {
                continue;
            }

            $time_part = date_format($one_date_entry->getStartTime(), 'H:i');
            $lang_part = null;

            foreach(explode("\n", $one_date_entry->getEventComment()) as $one_comment_line) {
                $one_comment_line_parts = explode(':', trim('' . $one_comment_line));
                if ((2 == count($one_comment_line_parts)) && ('lang' == $one_comment_line_parts[0])) {
                    $lang_part = $one_comment_line_parts[1];
                    break;
                }
            }

            $found_screens[] = array(
                'date' => $date_part,
                'time' => $time_part,
                'lang' => $lang_part,
            );

        }

        return $found_screens;
    }

    public function getEventDateInfo($p_article, $p_date)
    {
        $no_info = array('found' => false);

        if (!preg_match('/^[\d]{4}-[\d]{2}-[\d]{2}$/', $p_date)) {
            return $no_info;
        }

        $cancel_types = array('voided', 'withdrawn');
        $regular_types = array('schedule');

        $em = \Zend_Registry::get('container')->getService('em');
        $repo = $em->getRepository('Newscoop\Entity\ArticleDatetime');
        $res = $repo->findBy(array('articleId'=>$p_article->getArticleNumber()));

        if (empty($res)) {
            return $no_info;
        }

        $found_time = null;
        $found_about = null;
        $found_canceled = false;

        foreach ($res as $one_date_entry) {
            $date_part = date_format($one_date_entry->getStartDate(), 'Y-m-d');
            if ($date_part != $p_date) {
                continue;
            }

            $time_part = date_format($one_date_entry->getStartTime(), 'H:i:s');

            if (in_array($one_date_entry->getFieldName(), $cancel_types)) {
                $found_canceled = true;
            }

            if (in_array($one_date_entry->getFieldName(), $regular_types)) {
                $found_time = $time_part;
                $found_about = $one_date_entry->getEventComment();
            }
        }

        if (null === $found_time) {
            return $no_info;
        }

        return array(
            'found' => true,
            'date' => $p_date,
            'time' => $found_time,
            'canceled' => $found_canceled,
            'about' => $found_about,
        );
    }


    public function splitDescs($p_desc)
    {
        $res_texts = array();
        $res_links = array();

        $desc_text = preg_replace('/<a[^<>]*>[^<>]*<\/a>/i', '', $p_desc);

        $link_matches = array();
        $got_links = preg_match_all('/<a[^<>]*href=[\'"]([^<>\'"]*)[\'"][^<>]*>([^<>]*)<\/a>/i', $p_desc, $link_matches, PREG_SET_ORDER);
        if ($got_links) {
            foreach ($link_matches as $one_link_info) {
                $one_label = str_ireplace(array('http://'), array(''), $one_link_info[2]);
                $res_links[] = array('link' => $one_link_info[1], 'label' => $one_label);
            }
        }


        $multi_desc_text = strip_tags(str_replace(array('<'), array("\n\n<"), $desc_text));
        foreach (explode("\n\n", $multi_desc_text) as $one_desc_part) {
            $cur_part = array();

            foreach (explode("\n", $one_desc_part) as $one_desc_line) {
                $test_line = trim($one_desc_line);
                if (empty($test_line)) {
                    continue;
                }
                $cur_one_line = '';
                try {
                    $cur_one_line = html_entity_decode($one_desc_line);
                }
                catch (\Exception $exc) {
                    continue;
                }
                $cur_part[] = $cur_one_line;
            }

            if (!empty($cur_part)) {
                $res_texts[] = implode("\n", $cur_part);
            }

        }

        return array('texts' => $res_texts, 'links' => $res_links);
    }

}
