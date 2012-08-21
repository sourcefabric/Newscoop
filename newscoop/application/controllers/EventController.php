<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class EventController extends Zend_Controller_Action
{
    const PUBLICATION = 1;
    const LANGUAGE = 5;
    const EV_SECTION = 71; // events
    const EV_TYPE = 'event';

    const EV_TIMEZONE = 'Europe/Zurich';
    const EV_END_TIME = '235959';

    /** @var Newscoop\Services\AgendaService */
    private $m_service = null;

    public function init()
    {
        $this->m_service = $this; // TODO: put a real agenda_service herein
        return;
    }

    public function indexAction()
    {
        $this->render('index');
    }

    public function icalAction()
    {
        //$no_response = '';

        $event_key = $this->_getParam('key');
        $event_date = $this->_getParam('date');
        if ((!$event_key) || (!$event_date)) {
            //echo $no_response;
            $this->_returnNoneIcal();
            exit(0);
        }

        $params = array();

        $params['event_key'] = $event_key;
        $params['event_date'] = $event_date;

        $params['publication'] = self::PUBLICATION;
        $params['language'] = self::LANGUAGE;
        $params['section'] = self::EV_SECTION;
        $params['article_type'] = self::EV_TYPE;

        $ical_info = $this->m_service->getEventIcalInfo($params);

        if ((!isset($ical_info['found'])) || (!$ical_info['found'])) {
            //echo $no_response;
            $this->_returnNoneIcal();
            exit(0);
        }

        $event_date_loc = $ical_info['date'];
        $event_time_loc = $ical_info['time'];

        $event_start_timestamp = strtotime($event_date_loc . ' ' . $event_time_loc . ' ' . self::EV_TIMEZONE);
        $event_start_date_gmt = gmdate('Ymd', $event_start_timestamp);
        $event_start_time_gmt = gmdate('His', $event_start_timestamp);

        $event_end_timestamp = strtotime($event_date_loc . ' ' . self::EV_END_TIME . ' ' . self::EV_TIMEZONE);
        $event_end_date_gmt = gmdate('Ymd', $event_end_timestamp);
        $event_end_time_gmt = gmdate('His', $event_end_timestamp);

        $ical_start = $event_start_date_gmt . 'T'. $event_start_time_gmt . 'Z';
        $ical_end = $event_end_date_gmt . 'T' . $event_end_time_gmt . 'Z';

        // http://stackoverflow.com/questions/1463480/how-can-i-use-php-to-dynamically-publish-an-ical-file-to-be-read-by-google-calen
        // http://stackoverflow.com/questions/5896647/timezone-conversion-in-php
        // http://www.timeanddate.com/worldclock/timezone.html?n=87
        // http://www.php.net/manual/en/timezones.php
        // http://en.wikipedia.org/wiki/ICalendar
        // http://phpicalendar.net/

        //$ical_uid = md5(uniqid(mt_rand(), true)) . '@tageswoche.ch';
        $ical_uid = 'event_' . $ical_info['spec'] . '@tageswoche.ch';

        $ical_name = $this->entities2Utf8($ical_info['name']);
        $ical_name = str_replace(array('\\'), array('\\\\'), $ical_name);
        $ical_name = str_replace(array(','), array('\\,'), $ical_name);

        $ical_desc = $this->entities2Utf8($ical_info['desc']);
        $ical_desc = str_replace(array('\\'), array('\\\\'), $ical_desc);
        $ical_desc = str_replace(array(','), array('\\,'), $ical_desc);

        $ical_location = $this->entities2Utf8($ical_info['addr']);
        $ical_location = str_replace(array('\\'), array('\\\\'), $ical_location);
        $ical_location = str_replace(array(','), array('\\,'), $ical_location);

        $ical_organizer = $this->entities2Utf8($ical_info['orgn']);
        $ical_organizer = str_replace(array('\\'), array('\\\\'), $ical_organizer);
        $ical_organizer = str_replace(array(','), array('\\,'), $ical_organizer);

        $ical_str = 'BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
BEGIN:VEVENT
UID:' . $ical_uid . '
DTSTAMP:' . gmdate('Ymd').'T'. gmdate('His') . 'Z
DTSTART:' . $ical_start . '
SUMMARY:' . $ical_name . '
DESCRIPTION:' . $ical_desc . '
LOCATION:' . $ical_location . '
END:VEVENT
END:VCALENDAR';

// DTEND:' . $ical_end . '
// ORGANIZER:' . $ical_organizer . '

        // http://stackoverflow.com/questions/1395151/content-dispositionwhat-are-the-differences-between-inline-and-attachment
        // header('Content-type: text/html; charset=utf-8');
        //header('Content-type: text/calendar; charset=utf-8');
        //header('Content-Disposition: inline; filename=calendar.ics');
        $this->getResponse()->setHeader('Content-type', 'text/calendar; charset=utf-8', true);
        $this->getResponse()->setHeader('Content-Disposition', 'inline; filename=calendar.ics');
        $this->getResponse()->sendHeaders();

        echo str_replace("\n", "\r\n", $ical_str);
        exit(0);

    }

    private function _returnNoneIcal()
    {
        header('Status: 404 Not Found');
        echo 'The requested event was not found.';
    }

    public function getEventIcalInfo($p_params)
    {
        $no_event = array('found' => false);
        $max_desc_len = 60;

        $use_publication = $p_params['publication'];
        $use_language = $p_params['language'];
        $use_section = $p_params['section'];
        $use_article_type = $p_params['article_type'];
        $use_event_id = $p_params['event_key'];

        $use_event_date = $p_params['event_date'];
        if (!preg_match('/^[\d]{4}-[\d]{2}-[\d]{2}$/', $use_event_date)) {
            return $no_event;
        }

        $event_list = Article::GetList(array(
            new ComparisonOperation('published', new Operator('is', 'string'), 'true'),
            new ComparisonOperation('IdPublication', new Operator('is', 'integer'), $use_publication),
            new ComparisonOperation('idlanguage', new Operator('is', 'integer'), $use_language),
            new ComparisonOperation('section', new Operator('is', 'integer'), $use_section),
            new ComparisonOperation('type', new Operator('is', 'string'), $use_article_type),
            new ComparisonOperation($use_article_type . '.event_id', new Operator('is', 'string'), $use_event_id),
        ), null, null, 0, $p_count, true);

        if (empty($event_list)) {
            return $no_event;
        }

        $found_event = null;
        $found_event_data = null;
        foreach ($event_list as $one_event) {
            $one_event_data = $one_event->getArticleData();
            if (($one_event_data->getFieldValue('event_id')) == $use_event_id) {
                $found_event = $one_event;
                $found_event_data = $one_event_data;
                break;
            }
        }

        if ((!$found_event) || (!$found_event_data)) {
            return $no_event;
        }
        $em = \Zend_Registry::get('container')->getService('em');
        $repo = $em->getRepository('Newscoop\Entity\ArticleDatetime');
        $dt_list = $repo->findBy(array('articleId'=>$found_event->getArticleNumber()));

        if (empty($dt_list)) {
            return $no_event;
        }

        $event_address_parts = array();
        $event_address_street = '' . $found_event_data->getProperty('Fstreet');
        if (!empty($event_address_street)) {
            $event_address_parts[] = $event_address_street;
        }

        $event_address_town_parts = array();
        $event_address_town_name = '' . $found_event_data->getProperty('Ftown');
        if (!empty($event_address_town_name)) {
            $event_address_town_parts[] = $event_address_town_name;
        }
        $event_address_town_zip = '' . $found_event_data->getProperty('Fzipcode');
        if (!empty($event_address_town_zip)) {
            $event_address_town_parts[] = $event_address_town_zip;
        }
        $event_address_town = implode(' ', $event_address_town_parts);
        if (!empty($event_address_town)) {
            $event_address_parts[] = $event_address_town;
        }

        $event_address = implode(', ', $event_address_parts);

        $event_desc = '';
        $event_desc_array = explode("\n", '' . $found_event_data->getProperty('Fdescription'));
        if (0 < count($event_desc_array)) {
            $event_desc = $event_desc_array[0];
            if (strlen($event_desc) > $max_desc_len) {
                $event_desc = mb_substr($event_desc, 0, $max_desc_len) . '...';
            }
        }

        $event_ical_info = array(
            'found' => false,
            'date' => $use_event_date,
            'time' => '',
            'spec' => 'e' . $use_event_id . '_d' . $use_event_date,
            'name' => str_replace(array("\n"), array(' '), $found_event_data->getProperty('Fheadline')),
            'addr' => str_replace(array("\n"), array(' '), $event_address),
            'orgn' => str_replace(array("\n"), array(' '), $found_event_data->getProperty('Forganizer')),
            'desc' => $event_desc,
        );

        $regular_types = array('schedule');
        foreach ($dt_list as $one_date_entry) {
            $date_part = date_format($one_date_entry->getStartDate(), 'Y-m-d');
            if ($date_part != $use_event_date) {
                continue;
            }

            if (in_array($one_date_entry->getFieldName(), $regular_types)) {
                $event_ical_info['time'] = date_format($one_date_entry->getStartTime(), 'H:i:s');
                $event_ical_info['found'] = true;

                $event_about = $one_date_entry->getEventComment();
                if (!empty($event_about)) {
                    $event_about_array = explode("\n", '' . $event_about);
                    if (0 < count($event_about_array)) {
                        $event_ical_info['desc'] = $event_about_array[0];
                        if (strlen($event_ical_info['desc']) > $max_desc_len) {
                            $event_ical_info['desc'] = mb_substr($event_ical_info['desc'], 0, $max_desc_len) . '...';
                        }
                    }
                }

            }

        }

        return $event_ical_info;

    }

    public function entities2Utf8($p_text)
    {
        //$forbidden_contractions = array('38', '60', '62', '150', '151', '132', '146', '147'); // do not translate into &, <, >, dashes, and some other ones
        $forbidden_contractions = array();

        $p_text = iconv('UTF-8','UTF-8//IGNORE', $p_text);
        //$p_text = html_entity_decode($p_text, ENT_QUOTES, 'UTF-8');

        $content_lines = explode('&#', $p_text);
        $content = array_shift($content_lines);
        foreach ($content_lines as $one_line) {
            $one_line_parts = explode(';', $one_line, 2);
            if ( (2 == count($one_line_parts)) && (is_numeric($one_line_parts[0])) && (!in_array($one_line_parts[0], $forbidden_contractions)) ) {
                $content .= mb_convert_encoding('&#' . $this->altToUtf8(intval($one_line_parts[0])) . ';', 'UTF-8', 'HTML-ENTITIES');
                $content .= $one_line_parts[1];
            }
            else {
                $content .= '&#' . $one_line;
            }

        }

        $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');

        return $content;
    }

    private function altToUtf8($p_code)
    {
        // we get many bad data, like &#150; for dash. see next info:
        // http://www.php.net/manual/en/function.html-entity-decode.php#68491
        // http://en.wikipedia.org/wiki/Dash

        $code = $p_code;

        if ($code==128) { $code=8364; }
        elseif ($code==129) { $code=160; } // not affected
        elseif ($code==130) { $code=8218; }
        elseif ($code==131) { $code=402; }
        elseif ($code==132) { $code=8222; }
        elseif ($code==133) { $code=8230; }
        elseif ($code==134) { $code=8224; }
        elseif ($code==135) { $code=8225; }
        elseif ($code==136) { $code=710; }
        elseif ($code==137) { $code=8240; }
        elseif ($code==138) { $code=352; }
        elseif ($code==139) { $code=8249; }
        elseif ($code==140) { $code=338; }
        elseif ($code==141) { $code=160; } // not affected
        elseif ($code==142) { $code=381; }
        elseif ($code==143) { $code=160; } // not affected
        elseif ($code==144) { $code=160; } // not affected
        elseif ($code==145) { $code=8216; }
        elseif ($code==146) { $code=8217; }
        elseif ($code==147) { $code=8220; }
        elseif ($code==148) { $code=8221; }
        elseif ($code==149) { $code=8226; }
        elseif ($code==150) { $code=8211; }
        elseif ($code==151) { $code=8212; }
        elseif ($code==152) { $code=732; }
        elseif ($code==153) { $code=8482; }
        elseif ($code==154) { $code=353; }
        elseif ($code==155) { $code=8250; }
        elseif ($code==156) { $code=339; }
        elseif ($code==157) { $code=160; } // not affected
        elseif ($code==158) { $code=382; }
        elseif ($code==159) { $code=376; }

        return $code;
    }

}
