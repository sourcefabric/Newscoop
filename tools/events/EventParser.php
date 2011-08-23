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
        //if (!is_array($p_events)) {
        //    $p_events = array();
        //}

        $parser = new EventData_Parser_SimpleXML;

		$events = array();
        $files = array();

        if ($dir_handle = opendir($p_dir)) {
            while (false !== ($event_file = readdir($dir_handle))) {
                try {
                    $result = $parser->parse($events, $p_provider, $p_dir . '/' . $event_file, $p_categories);
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
    function parse(@$p_events, $p_provider, $p_file, $p_categories) {

		//$events = array();

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

			// one event is object of 72 (simple) properties, most of them are empty
			foreach ($entry_set as $event) {
				$event_info = array('provider_id' => $p_provider);

				// Ids
				// number, event id, shall be unique
				$x_eveid = '' . $event->eveid;
				if (empty($x_eveid)) {
					$x_eveid = null;
				}
				$event_info['event_id'] = $x_eveid;

				// number, turnus id, shall be shared among events of particular repeated actions
				$x_trnid = '' . $event->trnid;
				if (empty($x_trnid)) {
					$x_trnid = null;
				}
				$event_info['turnus_id'] = $x_trnid;

				// number, town id
				$x_loctwn = '' . $event->loctwn;

				// number, location id
				$x_locid = '' . $event->locid;
				if (empty($x_locid)) {
					$x_locid = null;
				}
				$event_info['location_id'] = $x_locid;

				// string, location key
				$x_lockey = '' . $event->lockey;
				// string, some event category group
				$x_catgrp = '' . $event->catgrp;


				// Categories

                $event_topics = array();
				// * main type fields
				// event category
				$x_catnam = strtolower('' . $event->catnam);
				//$event_info['event_type_id'] = 0;
				//$event_info['event_type'] = 'miscellaneous';
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
                        if (array_key_exists($x_catnam, $one_cat_match_xml)) {
                            //$event_info['event_type_id'] = $one_category_id;
                            //$event_info['event_type'] = $one_category['name'];
                            $event_topics[] = $one_cat_match_topic;
                            continue;
                        }
                    }
				}

				// event subcategory
				$x_catsub = '' . $event->catsub;
				// * minor type fields
				// location type (club, museum, ...)
				$x_loctyp = '' . $event->loctyp;
				// turnus category
				$x_trncao = '' . $event->trncao;
				// * additional usually empty category info
				// minimal age of turnus visitors
				$x_trnage = '' . $event->trnage;
				// turnus language
				$x_trnlan = '' . $event->trnlan;

				// Names
				// * main display provider name
				// event location name
				$event_location_name = null;

				$x_locnam = '' . $event->locnam;
				if (!empty($x_locnam)) {
					$event_location_name = $x_locnam;
				}

				// * minor display provider name
				// event location organizer
				$x_trnorg = '' . $event->trnorg;
				if (empty($event_location_name)) {
					if (!empty($x_trnorg)) {
						$event_location_name = $x_trnorg;
					}
				}

				$event_info['event_location'] = $event_location_name;

				// * main display turnus name
				// event name
				$x_trnnam = '' . $event->trnnam;
				if (empty($x_trnnam)) {
					$x_trnnam = null;
				}
				$event_info['event_name'] = $x_trnnam;

				// Prices, mostly empty, frequently inconsistent
				// number, better to ignore
				$x_evepri = '' . $event->evepri;
				// number, better to ignore
				$x_trnpri = '' . $event->trnpri;
				// plain text, may be used
				$x_eveptx = '' . $event->eveptx;
				// plain text, may be used
				$x_trnptx = '' . $event->trnptx;

				// Locations
				// !!! no country info
				$event_info['location_country'] = 'ch';

				// * main location info
				// town name
				$x_twnnam = '' . $event->twnnam;
				if (empty($x_twnnam)) {
					$x_twnnam = null;
				}
				$event_info['location_town'] = $x_twnnam;

				// zip code
				$x_loczip = '' . $event->loczip;
				if (empty($x_loczip)) {
					$x_loczip = null;
				}
				$event_info['location_zip'] = $x_loczip;

				// street address, free form, but usually 'street_name house_number'
				$x_locadr = '' . $event->locadr;
				if (empty($x_locadr)) {
					$x_locadr = null;
				}
				$event_info['location_street'] = $x_locadr;

				// * minor location info
				// other location specification
				$x_locade = '' . $event->locade;
				// other location specification
				$x_eveloz = '' . $event->eveloz;
				// directions to the location
				$x_locacc = '' . $event->locacc;

				// Date, time
				// * main date-time info

				$event_date = null;

				// year, four digits
				$x_evedatyeanum2 = '' . $event->evedatyeanum2;
				// month, two digits
				$x_evedatmonnum2 = '' . $event->evedatmonnum2;
				// day, two digits
				$x_evedatdaynum2 = '' . $event->evedatdaynum2;

				if ((!empty($x_evedatyeanum2)) && (!empty($x_evedatmonnum2)) && (!empty($x_evedatdaynum2))) {
					if ((4 == strlen($x_evedatyeanum2)) && (2 == strlen($x_evedatmonnum2)) && (2 == strlen($x_evedatdaynum2))) {
						$event_date = $x_evedatyeanum2 . '-' . $x_evedatmonnum2 . '-' . $x_evedatdaynum2;
					}
				}
				$event_info['event_date'] = $event_date;

				// hours, like '14.30'
				$x_eveda2 = '' . $event->eveda2;
				if (empty($x_eveda2)) {
					$x_eveda2 = null;
				}
				$event_info['event_time'] = $x_eveda2;

				$event_open = array();

				// * plaint text days/hours span info
				// long-term days
				$x_trntxx = '' . $event->trntxx;
				if (!empty($x_trntxx)) {
					$event_open[] = $x_trntxx;
				}

				// long-term hours
				$x_lochou = '' . $event->lochou;
				if (!empty($x_lochou)) {
					$event_open[] = $x_lochou;
				}

				// hour span
				$x_evehou = '' . $event->evehou;
				if (!empty($x_evehou)) {
					$event_open[] = $x_evehou;
				}

				// hour span
				$x_evemtx = '' . $event->evemtx;
				if (!empty($x_evemtx)) {
					$event_open[] = $x_evemtx;
				}

				$event_info['event_open'] = $event_open;

				// Descriptions

				$event_texts = array();

				// * usually the main texts
				// short description
				$x_trntxs = '' . $event->trntxs;
				if (!empty($x_trntxs)) {
					$event_texts[] = $x_trntxs;
				}

				// middle description
				$x_trntxm = '' . $event->trntxm;
				if (!empty($x_trntxm)) {
					$event_texts[] = $x_trntxm;
				}

				// long description
				$x_trntxl = '' . $event->trntxl;
				if (!empty($x_trntxl)) {
					$event_texts[] = $x_trntxl;
				}

				// * short additional info
				// short info
				$x_trntt1 = '' . $event->trntt1;
				if (!empty($x_trntt1)) {
					$event_texts[] = $x_trntt1;
				}

				// short info
				$x_trntt2 = '' . $event->trntt2;
				if (!empty($x_trntt2)) {
					$event_texts[] = $x_trntt2;
				}

				// short info
				$x_trntt3 = '' . $event->trntt3;
				if (!empty($x_trntt3)) {
					$event_texts[] = $x_trntt3;
				}

				// * additional notice
				// notice
				$x_evett1 = '' . $event->evett1;
				if (!empty($x_evett1)) {
					$event_texts[] = $x_evett1;
				}

				// notice
				$x_evett2 = '' . $event->evett2;
				if (!empty($x_evett2)) {
					$event_texts[] = $x_evett2;
				}

				// notice
				$x_evett3 = '' . $event->evett3;
				if (!empty($x_evett3)) {
					$event_texts[] = $x_evett3;
				}

				$event_info['event_texts'] = $event_texts;

				// Links

				$event_web = null;

				// web link for event, usually empty
				$x_evelnk = '' . $event->evelnk;
				if (!empty($x_evelnk)) {
					$event_web = $x_evelnk;
				}

				// web link for turnus, usually empty
				$x_trnlnk = '' . $event->trnlnk;
				if (empty($event_web)) {
					if (!empty($x_trnlnk)) {
						$event_web = $x_trnlnk;
					}
				}

				// location web url
				$x_locurl = '' . $event->locurl;
				if (empty($event_web)) {
					if (!empty($x_locurl)) {
						$event_web = $x_locurl;
					}
				}

				$event_info['event_web'] = $event_web;

				// location email address
				$x_locema = '' . $event->locema;
				if (empty($x_locema)) {
					$x_locema = null;
				}
				$event_info['event_email'] = $x_locema;

				// location phone number
				$x_loctel = '' . $event->loctel;
				if (empty($x_loctel)) {
					$x_loctel = null;
				}
				$event_info['event_phone'] = $x_loctel;

				// Multimedia
				// * list (usually by newlines) of links plus names (space separated)
				// images

				$event_images = array();

				$x_eveimg = '' . $event->eveimg;
				if (!empty($x_eveimg)) {
					$x_eveimg = trim($x_eveimg);
					$x_eveimg_arr = explode("\n", $x_eveimg);
					for ($x_eveimg_arr as $one_image) {
						$one_image = trim($one_image);
						if ('http' != substr($one_image, 0, 4)) {
							continue;
						}
						$one_image_desc_start = strpos($one_image, ' ');
						if (false === $one_image_desc_start) {
							$event_images[] = array('url' => $one_image, 'label' = null);
							continue;
						}
						$one_image_desc = trim(substr($one_image, $one_image_desc_start));
						if ('' == $one_image_desc) {
							$one_image_desc = null;
						}
						if ('default image' == strtolower($one_image_desc)) {
							$one_image_desc = null;
						}

						$event_images[] = array('url' => substr($one_image, 0, ($one_image_desc_start - 1)), 'label' = $one_image_des);
					}
				}

				$event_info['event_images'] = $event_images;

				// videos
				$x_evevid = '' . $event->evevid;
				// audios
				$x_eveaud = '' . $event->eveaud;


                $p_events[] = $event_info;
			}

		}

        return $p_events;
    } // fn parse
} // class EventData_Parser_SimpleXML


/*
$known_categories = array(
	1 => array('name' => 'theater',
		       'nicks' => array('theater', 'theatre'),
		       ),
	2 => array('name' => 'gallery',
			   'nicks' => array('gallery'),
			   ),
	3 => array('name' => 'exhibition',
			   'nicks' => array('exhibition', 'ausstellungen'),
			   ),
	4 => array('name' => 'party',
			   'nicks' => array('party'),
			   ),
	5 => array('name' => 'music',
			   'nicks' => array('music', 'musik'),
			   ),
	6 => array('name' => 'concert',
			   'nicks' => array('concert', 'konzerte'),
			   ),
	7 => array('name' => 'cinema',
			   'nicks' => array('cinema'),
			   ),
);


$provider_id = 1;
$fname = 'eventexport.xml';
$ed_parser = new EventData_Parser();
$ed_parser->parse($provider_id, $fname, $known_categories);
*/

