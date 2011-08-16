<?php
/**
 * Parsing the 'eventexport.xml' file
 */

/**
 * EventData Importer class for managing parsing of WXR files.
 */
class EventParser_Parser {

    /**
     * Parses EventData data (by ED_Parser_SimplXML)
     *
     * @param string $p_file file name of the wxr file
     * @return array
     */
    function parse($p_provider, $p_file, $p_categories) {

        $parser = new ED_Parser_SimpleXML;
        $result = $parser->parse($p_provider, $p_file, $p_categories);

        return $result;
    } // fn parse
} // class Event_Parser

/**
 * WXR Parser that makes use of the SimpleXML PHP extension.
 */
class ED_Parser_SimpleXML {

    /**
     * Parses EventData data (by SimplXML)
     *
     * @param string $p_file file name of the eventdata file
     * @return array
     */
    function parse($p_provider, $p_file, $p_categories) {
        //$authors = $posts = $categories = $categories_by_slug = $categories_slugs_by_name = $tags = $terms = array();

		$events = array();
		//$all_cats = array();

        libxml_clear_errors();
        $internal_errors = libxml_use_internal_errors(true);
        $xml = simplexml_load_file($p_file);

//echo "\n<pre>\n";
//var_dump($xml->date[0]->entry);
//var_dump($xml->date[0]->entry[0]);
//var_dump($xml->date[0]->entry[1]);
//var_dump($xml->date[0]);
//exit(0);

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
			echo "\n" . $recdate . "\n";

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
				// * main type fields
				// event category
				$x_catnam = strtolower('' . $event->catnam);
				$event_info['event_type_id'] = 0;
				$event_info['event_type'] = 'miscellaneous';
				foreach ($p_categories as $one_category_id => $one_category) {
					if (array_key_exists($x_catnam, $one_category['nicks'])) {
						$event_info['event_type_id'] = $one_category_id;
						$event_info['event_type'] = $one_category['name'];
						break;
					}
				}

				//if (!array_key_exists($x_catnam, $all_cats)) {
				//	$all_cats[$x_catnam] = true;
				//}

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

				//echo $x_trnnam . "\n";

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

				$event_info['event_open'] = array();

				// * plaint text days/hours span info
				// long-term days
				$x_trntxx = '' . $event->trntxx;
				if (!empty($x_trntxx)) {
					$event_texts[] = $x_trntxx;
				}

				// long-term hours
				$x_lochou = '' . $event->lochou;
				if (!empty($x_lochou)) {
					$event_texts[] = $x_lochou;
				}

				// hour span
				$x_evehou = '' . $event->evehou;
				if (!empty($x_evehou)) {
					$event_texts[] = $x_evehou;
				}

				// hour span
				$x_evemtx = '' . $event->evemtx;



				$event_texts = array();

				// Descriptions
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

			}


		}

		//echo "\ncats:\n";
		//var_dump($all_cats);

//exit(0);




/*

        $wxr_version = $xml->xpath('/rss/channel/wp:wxr_version');
        if (!$wxr_version) {
            return array("correct" => false, "errormsg" => "missing wxr version");
        }

        $wxr_version = (string) trim($wxr_version[0]);
        // confirm that we are dealing with the correct file format
        if (!preg_match('/^\d+\.\d+$/', $wxr_version)) {
            return array("correct" => false, "errormsg" => "missing/invalid wxr version");
        }
        $base_url = $xml->xpath('/rss/channel/wp:base_site_url');
        $base_url = (string) trim($base_url[0]);

        $namespaces = $xml->getDocNamespaces();
        if (!isset($namespaces['wp'])) {
            $namespaces['wp'] = 'http://wordpress.org/export/1.1/';
        }
        if (!isset( $namespaces['excerpt'])) {
            $namespaces['excerpt'] = 'http://wordpress.org/export/1.1/excerpt/';
        }
        // grab authors
        foreach ($xml->xpath('/rss/channel/wp:author') as $author_arr) {
            $a = $author_arr->children($namespaces['wp']);
            $login = (string) $a->author_login;
            $authors[$login] = array(
                'author_id' => (int) $a->author_id,
                'author_login' => $login,
                'author_email' => (string) $a->author_email,
                'author_display_name' => (string) $a->author_display_name,
                'author_first_name' => (string) $a->author_first_name,
                'author_last_name' => (string) $a->author_last_name
            );
        }

        // grab cats, tags and terms
        foreach ($xml->xpath('/rss/channel/wp:category') as $term_arr) {
            $t = $term_arr->children($namespaces['wp']);

            $one_cat_slug = str_replace(array("\"", ":", "/"), array("-", "-", "-"), (string) $t->category_nicename);
            $one_cat_name = (string) $t->cat_name;
            //$one_cat_slug = (string) $t->category_nicename;
            $one_cat_data = array(
                'term_id' => (int) $t->term_id,
                'category_nicename' => $one_cat_slug,
                'category_parent' => (string) $t->category_parent,
                'cat_name' => $one_cat_name,
                'category_description' => (string) $t->category_description
            );
            $categories[] = $one_cat_data;
            $categories_by_slug[$one_cat_slug] = $one_cat_data;
            $categories_slugs_by_name[$one_cat_name] = $one_cat_slug;
        }

        foreach ($xml->xpath('/rss/channel/wp:tag') as $term_arr) {
            $t = $term_arr->children($namespaces['wp']);
            $tags[] = array(
                'term_id' => (int) $t->term_id,
                'tag_slug' => (string) $t->tag_slug,
                'tag_name' => (string) $t->tag_name,
                'tag_description' => (string) $t->tag_description
            );
        }

        foreach ($xml->xpath('/rss/channel/wp:term') as $term_arr) {
            $t = $term_arr->children( $namespaces['wp'] );
            $terms[] = array(
                'term_id' => (int) $t->term_id,
                'term_taxonomy' => (string) $t->term_taxonomy,
                'slug' => (string) $t->term_slug,
                'term_parent' => (string) $t->term_parent,
                'term_name' => (string) $t->term_name,
                'term_description' => (string) $t->term_description
            );
        }

        // grab posts
        foreach ($xml->channel->item as $item) {
            $post = array(
                'post_title' => (string) $item->title,
                'guid' => (string) $item->guid,
            );

            $dc = $item->children('http://purl.org/dc/elements/1.1/');
            $post['post_author'] = (string) $dc->creator;

            $content = $item->children('http://purl.org/rss/1.0/modules/content/');
            $excerpt = $item->children($namespaces['excerpt']);
            $post['post_content'] = (string) $content->encoded;
            $post['post_excerpt'] = (string) $excerpt->encoded;

            $wp = $item->children($namespaces['wp']);
            $post['post_id'] = (int) $wp->post_id;
            $post['post_date'] = (string) $wp->post_date;
            $post['post_date_gmt'] = (string) $wp->post_date_gmt;
            $post['comment_status'] = (string) $wp->comment_status;
            $post['ping_status'] = (string) $wp->ping_status;
            $post['post_name'] = (string) $wp->post_name;
            $post['status'] = (string) $wp->status;
            $post['post_parent'] = (int) $wp->post_parent;
            $post['menu_order'] = (int) $wp->menu_order;
            $post['post_type'] = (string) $wp->post_type;
            $post['post_password'] = (string) $wp->post_password;
            $post['is_sticky'] = (int) $wp->is_sticky;

            if (isset($item->link)) {
                $post['link'] = (string) $item->link;
            }

            if (isset($wp->attachment_url)) {
                $post['attachment_url'] = (string) $wp->attachment_url;
            }
            foreach ($item->category as $c) {
                $att = $c->attributes();
                if (isset($att['nicename'])) {
                    $post['terms'][] = array(
                        'name' => (string) $c,
                        'slug' => str_replace(array("\"", ":", "/"), array("-", "-", "-"), (string) $att['nicename']),
                        //'slug' => (string) $att['nicename'],
                        'domain' => (string) $att['domain']
                    );
                }
            }

            foreach ( $wp->postmeta as $meta ) {
                $post['postmeta'][] = array(
                    'key' => (string) $meta->meta_key,
                    'value' => (string) $meta->meta_value,
                );
            }

            foreach ( $wp->comment as $comment ) {
                $post['comments'][] = array(
                    'comment_id' => (int) $comment->comment_id,
                    'comment_author' => (string) $comment->comment_author,
                    'comment_author_email' => (string) $comment->comment_author_email,
                    'comment_author_IP' => (string) $comment->comment_author_IP,
                    'comment_author_url' => (string) $comment->comment_author_url,
                    'comment_date' => (string) $comment->comment_date,
                    'comment_date_gmt' => (string) $comment->comment_date_gmt,
                    'comment_content' => (string) $comment->comment_content,
                    'comment_approved' => (string) $comment->comment_approved,
                    'comment_type' => (string) $comment->comment_type,
                    'comment_parent' => (string) $comment->comment_parent,
                    'comment_user_id' => (int) $comment->comment_user_id,
                );
            }

            $posts[] = $post;
        }

        $wxr_title = "";
        $wxr_title_arr = $xml->xpath('/rss/channel/title');
        if ($wxr_title_arr) {
            $wxr_title = $wxr_title_arr[0];
        }

        $wxr_link = "";
        $wxr_link_arr = $xml->xpath('/rss/channel/link');
        if ($wxr_link_arr) {
            $wxr_link = $wxr_link_arr[0];
        }

        return array(
            'correct' => true,
            'errormsg' => "",
            'authors' => $authors,
            'posts' => $posts,
            'categories' => $categories,
            'categories_by_slug' => $categories_by_slug,
            'categories_slugs_by_name' => $categories_slugs_by_name,
            'tags' => $tags,
            'terms' => $terms,
            'base_url' => $base_url,
            'version' => $wxr_version,
            'title' => $wxr_title,
            'link' => $wxr_link,
        );
*/

    } // fn parse
} // class WXR_Parser_SimpleXML


$known_categories = array(
	1 => array('name' => 'theater',
		       'nicks' => array('theater', 'theatre'),
		       ),
	2 => array('name' => 'gallery',
			   'nicks' => array('gallery'),
			   ),
	3 => array('name' => 'exhibition',
			   'nicks' => array('exhibition', 'ausstellung', 'ausstellungen'),
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
$ed_parser = new EventParser_Parser();
$ed_parser->parse($provider_id, $fname, $known_categories);


