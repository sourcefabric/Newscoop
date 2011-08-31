<?php
/**
 * Parsing the 'eventexport.xml' file
 */

/**
 * EventData Importer class for managing parsing of EventData files.
 */

class EventData_Parser {

    /**
     * Parses EventData data (by EventData_Parser_SimpleXML)
     *
     * @param string $p_file file name of the event file
     * @return array
     */
    public static function Parse($p_provider, $p_dir, $p_categories) {
        if (!is_array($p_categories)) {
            $p_categories = array();
        }

        $parser = new EventData_Parser_SimpleXML;

        $events = array();
        $files = array();

        $dir_handle = null;
        try {
            $dir_handle = opendir($p_dir);
        }
        catch (Exception $exc) {
            return false;
        }

        if ($dir_handle) {
            while (false !== ($event_file = readdir($dir_handle))) {
                if (!is_file($p_dir . '/' . $event_file)) {
                    continue;
                }

                try {
                    $result = $parser->parse($events, $p_provider, $p_dir . '/' . $event_file, $p_categories);
                }
                catch (Exception $exc) {
                    // may be some logging;
                }
                $files[] = $event_file;
            }
        }

        return array('files' => $files, 'events' => $events);
    } // fn parse
} // class EventData_Parser

/**
 * EventData Parser that makes use of the SimpleXML PHP extension.
 */
class EventData_Parser_SimpleXML {

    /**
     * Parses EventData data (by SimplXML)
     *
     * @param string $p_file file name of the eventdata file
     * @return array
     */
    function parse(&$p_events, $p_provider, $p_file, $p_categories) {

        libxml_clear_errors();
        $internal_errors = libxml_use_internal_errors(true);
        $xml = simplexml_load_file($p_file);

        // halt if loading produces an error
        if (!$xml) {
            $error_msg = "";
            foreach (libxml_get_errors() as $err_line) {
                $error_msg .= $err_line->message;
            }
            libxml_clear_errors();
            return array("correct" => false, "errormsg" => $error_msg);
        }

        // $xml->date is an array of (per-day) event sets
        foreach ($xml->date as $event_day) {
            // day of event set (event record)
            $recdate = $event_day->recdate;

            // array of events info
            $entry_set = $event_day->entry;

            //$debug_end = false;
            // one event is object of 72 (simple) properties, most of them are empty
            foreach ($entry_set as $event) {
                //if ($debug_end) {return;}
                //$debug_end = true;

                $event_info = array('provider_id' => $p_provider);
                $event_other = array();

                // Ids: hidden fields, other ones will be visible

                // number, event id, shall be unique
                $x_eveid = trim('' . $event->eveid);
                $event_info['event_id'] = $x_eveid;

                // number, tour id, shall be shared among events of particular repeated actions
                $x_trnid = trim('' . $event->trnid);
                $event_info['tour_id'] = $x_trnid;

                // number, location id
                $x_locid = trim('' . $event->locid);
                $event_info['location_id'] = $x_locid;


                // Categories

                // event subcategory
                $x_catsub = trim('' . $event->catsub);
                $event_info['genre'] = $x_catsub;

                // location hot
                $x_lochot = trim('' . $event->lochot);
                $event_info['rated'] = (empty($x_lochot) ? true : false);

                $event_topics = array();
                // * main type fields
                // event category
                $x_catnam = strtolower(trim('' . $event->catnam));
                foreach ($p_categories as $one_category) {
                    if (!is_array($one_category)) {
                        continue;
                    }
                    if (array_key_exists('fixed', $one_category)) {
                        $event_topics[] = $one_category['fixed'];
                        continue;
                    }
                    if ((array_key_exists('match_xml', $one_category)) && (array_key_exists('match_topic', $one_category))) {
                        $one_cat_match_xml = $one_category['match_xml'];
                        $one_cat_match_topic = $one_category['match_topic'];
                        if ((!is_array($one_cat_match_xml)) || (!is_array($one_cat_match_topic))) {
                            continue;
                        }
                        if (in_array($x_catnam, $one_cat_match_xml)) {
                            $event_topics[] = $one_cat_match_topic;
                            continue;
                        }
                    }
                }
                $event_info['topics'] = $event_topics;

                // Location

                // * main display provider name
                // event location name
                // the 'trnorg'/'tour_organization' is a similar/related info
                $x_locnam = trim('' . $event->locnam);
                $event_info['organizer'] = $x_locnam; // may be overwritten by tour_organizer

                // !!! no country info
                $event_info['country'] = 'ch';

                // * main location info
                // town name
                $x_twnnam = trim('' . $event->twnnam);
                $event_info['town'] = $x_twnnam;

                // zip code
                $x_loczip = trim('' . $event->loczip);
                $event_info['zipcode'] = $x_loczip;

                // street address, free form, but usually 'street_name house_number'
                $x_locadr = trim('' . $event->locadr);
                $event_info['street'] = $x_locadr;

                // * minor location info
                // other location specification
                $x_locade = trim('' . $event->locade);
                if (!empty($x_locade)) {
                    $event_other[] = $x_locade;
                }

                // directions to the location
                $x_locacc = trim('' . $event->locacc);
                if (!empty($x_locacc)) {
                    $event_other[] = $x_locacc;
                }

                // Tour

                // * main display tour name
                // tour name
                $x_trnnam = trim('' . $event->trnnam);
                $event_info['headline'] = $x_trnnam;

                // tour organizer
                $x_trnorg = trim('' . $event->trnorg);
                if (!empty($x_trnorg)) {
                    $event_info['organizer'] = $x_trnorg;
                }

                // tour language
                $x_trnlan = trim('' . $event->trnlan);
                $event_info['languages'] = $x_trnlan;

                // * additional usually empty info
                // minimal age of tour visitors
                $x_trnage = trim('' . $event->trnage);
                $event_info['minimal_age'] = $x_trnage;

                // additional text, usually (but not always) long-term days if anything at all
                $x_trntxx = trim('' . $event->trntxx);
                if (!empty($x_trntxx)) {
                    $event_other[] = $x_trntxx;
                }

                // Event

                // other event location specification
                $x_eveloz = trim('' . $event->eveloz);
                if (!empty($x_eveloz)) {
                    $event_other[] = $x_eveloz;
                }

                // Descriptions

                $event_info['description'] = '';
                $event_texts = array();

                // * usually the main texts

                $event_tour_text = '';

                // long description
                $x_trntxl = trim('' . $event->trntxl);
                if (empty($event_tour_text)) {
                    if (!empty($x_trntxl)) {
                        $event_tour_text = $x_trntxl;
                    }
                }

                // middle description
                $x_trntxm = trim('' . $event->trntxm);
                if (empty($event_tour_text)) {
                    if (!empty($x_trntxm)) {
                        $event_tour_text = $x_trntxm;
                    }
                }

                // short description
                $x_trntxs = trim('' . $event->trntxs);
                if (empty($event_tour_text)) {
                    if (!empty($x_trntxs)) {
                        $event_tour_text = $x_trntxs;
                    }
                }

                if (!empty($event_tour_text)) {
                    $event_texts[] = $event_tour_text;
                }

                // * short additional info

                $event_tour_subtitle = '';

                // subtitle
                $x_trntt3 = trim('' . $event->trntt3);
                if (empty($event_tour_subtitle)) {
                    if (!empty($x_trntt3)) {
                        $event_tour_subtitle = $x_trntt3;
                    }
                }

                // subtitle
                $x_trntt2 = trim('' . $event->trntt2);
                if (empty($event_tour_subtitle)) {
                    if (!empty($x_trntt2)) {
                        $event_tour_subtitle = $x_trntt2;
                    }
                }

                // subtitle
                $x_trntt1 = trim('' . $event->trntt1);
                if (empty($event_tour_subtitle)) {
                    if (!empty($x_trntt1)) {
                        $event_tour_subtitle = $x_trntt1;
                    }
                }

                if (!empty($event_tour_subtitle)) {
                    $event_texts[] = $event_tour_subtitle;
                }

                // * additional notices

                $event_event_subtitle = '';

                // subtitle
                $x_evett3 = trim('' . $event->evett3);
                if (empty($event_event_subtitle)) {
                    if (!empty($x_evett3)) {
                        $event_event_subtitle = $x_evett3;
                    }
                }

                // subtitle
                $x_evett2 = trim('' . $event->evett2);
                if (empty($event_event_subtitle)) {
                    if (!empty($x_evett2)) {
                        $event_event_subtitle = $x_evett2;
                    }
                }

                // subtitle
                $x_evett1 = trim('' . $event->evett1);
                if (empty($event_event_subtitle)) {
                    if (!empty($x_evett1)) {
                        $event_event_subtitle = $x_evett1;
                    }
                }

                if (!empty($event_event_subtitle)) {
                    $event_texts[] = $event_event_subtitle;
                }

                $one_text_rank = -1;
                foreach ($event_texts as $one_text) {
                    $one_text_rank += 1;
                    if (0 == $one_text_rank) {
                        $event_info['description'] = $one_text;
                        continue;
                    }
                    $event_other[] = $one_text;
                }

                // Date, time

                // * main date-time info

                $event_date = '0000-00-00';

                // year, four digits
                $x_evedatyeanum2 = trim('' . $event->evedatyeanum2);
                // month, two digits
                $x_evedatmonnum2 = trim('' . $event->evedatmonnum2);
                // day, two digits
                $x_evedatdaynum2 = trim('' . $event->evedatdaynum2);

                if ((!empty($x_evedatyeanum2)) && (!empty($x_evedatmonnum2)) && (!empty($x_evedatdaynum2))) {
                    if ((4 == strlen($x_evedatyeanum2)) && (2 == strlen($x_evedatmonnum2)) && (2 == strlen($x_evedatdaynum2))) {
                        $event_date = $x_evedatyeanum2 . '-' . $x_evedatmonnum2 . '-' . $x_evedatdaynum2;
                    }
                }
                $event_info['date'] = $event_date;

                // year, four digits
                $x_evedatyeanum2 = trim('' . $event->evedatyeanum2);
                $event_info['date_year'] = $x_evedatyeanum2;

                // month, one/two digits
                $x_evedatmonnum1 = trim('' . $event->evedatmonnum1);
                $event_info['date_month'] = $x_evedatmonnum1;

                // day, one/two digits
                $x_evedatdaynum1 = trim('' . $event->evedatdaynum1);
                $event_info['date_day'] = $x_evedatdaynum1;

                // hours, like '14.30'
                $x_eveda2 = trim('' . $event->eveda2);
                $event_info['time'] = $x_eveda2;

                // * plaint text days/hours span info

                $x_eveda2 = trim('' . $event->eveda2);
                $event_info['date_time_text'] = $x_eveda2;

                $event_date_time_text = array();

                // hour span
                $x_evemtx = trim('' . $event->evemtx);
                if (!empty($x_evemtx)) {
                    $event_date_time_text[] = $x_evemtx;
                }

                // long-term hours
                $x_lochou = trim('' . $event->lochou);
                if (!empty($x_lochou)) {
                    $event_date_time_text[] = $x_lochou;
                }

                // hour span
                $x_evehou = trim('' . $event->evehou);
                if (!empty($x_evehou)) {
                    $event_date_time_text[] = $x_evehou;
                }

                if (!empty($event_date_time_text)) {
                    $event_info['date_time_text'] = implode("\n", $event_date_time_text);
                }

                // Prices, mostly empty, frequently inconsistent

                $event_info['prices'] = '';

                // number, better to ignore
                $x_trnpri = trim('' . $event->trnpri);
                if (!empty($x_trnpri)) {
                    $event_info['prices'] = $x_trnpri;
                }

                // number, better to ignore
                $x_evepri = trim('' . $event->evepri);
                if (!empty($x_evepri)) {
                    $event_info['prices'] = $x_evepri;
                }

                // plain text, may be used
                $x_trnptx = trim('' . $event->trnptx);
                if (!empty($x_trnptx)) {
                    $event_info['prices'] = $x_trnptx;
                }

                // plain text, may be used
                $x_eveptx = trim('' . $event->eveptx);
                if (!empty($x_eveptx)) {
                    $event_info['prices'] = $x_eveptx;
                }

                // Links

                $event_web = '';

                // location web url
                $x_locurl = trim('' . $event->locurl);
                $event_web = $x_locurl;

                // tour web url
                $x_trnurl = trim('' . $event->trnurl);
                if (!empty($x_trnurl)) {
                    if (!empty($event_web)) {
                        $event_other[] = $x_trnurl;
                    }
                    else {
                        $event_web = $x_trnurl;
                    }
                }

                $event_other_links = array();

                // location web links
                $event_other_links[] = trim('' . $event->loclnk);

                // tour web links
                $event_other_links[] = trim('' . $event->trnlnk);

                // event web links
                $event_other_links[] = trim('' . $event->evelnk);

                foreach ($event_other_links as $other_links) {
                    if (!empty($other_links)) {
                        if (!empty($event_web)) {
                            $event_other[] = $other_links;
                        }
                        else {
                            $other_links_arr_tmp = explode("\n", $other_links);
                            $other_links_arr = array();
                            foreach ($other_links_arr_tmp as $one_links) {
                                $one_links = trim($one_links);
                                if (!empty($one_links)) {
                                    $other_links_arr_arr[] = $one_links;
                                }
                            }
                            if (0 < count($other_links_arr_arr)) {
                                $event_web = $other_links_arr_arr[0];
                            }
                            if (1 < count($other_links_arr_arr)) {
                                $event_other[] = $other_links;
                            }
                        }
                    }
                }

                $event_info['web'] = $event_web;

                // location email address
                $x_locema = trim('' . $event->locema);
                $event_info['email'] = $x_locema;

                // location phone number
                $x_loctel = trim('' . $event->loctel);
                $event_info['phone'] = $x_loctel;

                // Multimedia
                // * list (usually by newlines) of links plus names (space separated)
                // images

                $event_images = array();

                $x_eveimg = trim('' . $event->eveimg);
                if (!empty($x_eveimg)) {
                    $x_eveimg = trim($x_eveimg);
                    $x_eveimg_arr = explode("\n", $x_eveimg);
                    foreach ($x_eveimg_arr as $one_image) {
                        $one_image = trim($one_image);
                        if ('http' != substr($one_image, 0, 4)) {
                            continue;
                        }
                        $one_image_desc_start = strpos($one_image, ' ');
                        if (false === $one_image_desc_start) {
                            $event_images[] = array('url' => $one_image, 'label' => null);
                            continue;
                        }
                        $one_image_desc = trim(substr($one_image, $one_image_desc_start));
                        if ('' == $one_image_desc) {
                            $one_image_desc = null;
                        }
                        if ('default image' == strtolower($one_image_desc)) {
                            $one_image_desc = null;
                        }

                        $event_images[] = array('url' => substr($one_image, 0, ($one_image_desc_start - 1)), 'label' => $one_image_des);
                    }
                }

                $event_info['event_images'] = $event_images;

                // videos
                $x_evevid = trim('' . $event->evevid);
                if (!empty($x_locvid)) {
                    $event_other[] = $x_locvid;
                }
                $event_info['event_video'] = $x_locvid;

                // audios
                $x_eveaud = trim('' . $event->eveaud);
                if (!empty($x_locaud)) {
                    $event_other[] = $x_locaud;
                }

                $event_info['other'] = $event_other[];

                $p_events[] = $event_info;
            }

        }

        return $p_events;
    } // fn parse
} // class EventData_Parser_SimpleXML


