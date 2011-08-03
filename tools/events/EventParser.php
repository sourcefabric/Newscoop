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
    function parse($p_file) {

        $parser = new ED_Parser_SimpleXML;
        $result = $parser->parse($p_file);

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
    function parse($p_file) {
        //$authors = $posts = $categories = $categories_by_slug = $categories_slugs_by_name = $tags = $terms = array();

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
				// Ids
				// number, event id, shall be unique
				$eveid = $event->eveid;
				// number, turnus id, shall be shared among events of particular repeated actions
				$trnid = $event->trnid;
				// number, town id
				$loctwn = $event->loctwn;
				// number, location id
				$locid = $event->locid;
				// string, location key
				$lockey = $event->lockey;
				// string, some event category group
				$catgrp = $event->catgrp;


				// Categories
				// * main type fields
				// event category
				$catnam = $event->catnam;
				// event subcategory
				$catsub = $event->catsub;
				// * minor type fields
				// location type (club, museum, ...)
				$loctyp = $event->loctyp;
				// turnus category
				$trncao = $event->trncao;
				// * additional usually empty category info
				// minimal age of turnus visitors
				$trnage = $event->trnage;
				// turnus language
				$trnlan = $event->trnlan;

				// Names
				// * main display provider name
				// event location name
				$locnam = $event->locnam;
				// * minor display provider name
				// event location organizer
				$trnorg = $event->trnorg;
				// * main display turnus name
				// event name
				$trnnam = $event->trnnam;
				echo $trnnam . "\n";

				// Prices, mostly empty, frequently inconsistent
				// number, better to ignore
				$evepri = $event->evepri;
				// number, better to ignore
				$trnpri = $event->trnpri;
				// plain text, may be used
				$eveptx = $event->eveptx;
				// plain text, may be used
				$trnptx = $event->trnptx;

				// Locations
				// !!! no country info
				// * main location info
				// town name
				$twnnam = $event->twnnam;
				// zip code
				$loczip = $event->loczip;
				// street address, free form, but usually 'street_name house_number'
				$locadr = $event->locadr;
				// * minor location info
				// other location specification
				$locade = $event->locade;
				// other location specification
				$eveloz = $event->eveloz;
				// directions to the location
				$locacc = $event->locacc;

				// Date, time
				// * main date-time info
				// year, four digits
				$evedatyeanum2 = $event->evedatyeanum2;
				// month, short form
				$evedatmonnum1 = $event->evedatmonnum1;
				// day, short form
				$evedatdaynum1 = $event->evedatdaynum1;
				// hours, like '14.30'
				$eveda2 = $event->eveda2;
				// * plaint text days/hours span info
				// long-term days
				$trntxx = $event->trntxx;
				// long-term hours
				$lochou = $event->lochou;
				// hour span
				$evehou = $event->evehou;
				// hour span
				$evemtx = $event->evemtx;

				// Descriptions
				// * usually the main texts
				// short description
				$trntxs = $event->trntxs;
				// middle description
				$trntxm = $event->trntxm;
				// long description
				$trntxl = $event->trntxl;
				// * short additional info
				// short info
				$trntt1 = $event->trntt1;
				// short info
				$trntt2 = $event->trntt2;
				// short info
				$trntt3 = $event->trntt3;
				// * additional notice
				// notice
				$evett1 = $event->evett1;
				// notice
				$evett2 = $event->evett2;
				// notice
				$evett3 = $event->evett3;

				// Links
				// location web url
				$locurl = $event->locurl;
				// location email address
				$locema = $event->locema;
				// location phone number
				$loctel = $event->loctel;
				// web link for event, usually empty
				$evelnk = $event->evelnk;
				// web link for turnus, usually empty
				$trnlnk = $event->trnlnk;


				// Multimedia
				// * list (usually by newlines) of links plus names (space separated)
				// images
				$eveimg = $event->eveimg;
				// videos
				$evevid = $event->evevid;
				// audios
				$eveaud = $event->eveaud;

			}


		}

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


$fname = 'eventexport.xml';
$ed_parser = new EventParser_Parser();
$ed_parser->parse($fname);


