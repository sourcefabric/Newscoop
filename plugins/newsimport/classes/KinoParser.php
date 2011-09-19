<?php
/**
 * Parsing the 'kinodatenCH.xml', 'test_mov.xml' files
 */

/**
 * KinoData Importer class for managing parsing of KinoData files.
 */
class KinoData_Parser {

    /**
     * Parses KinoData data (by KinoData_Parser_SimpleXML)
     *
     * @param string $p_file file name of the kino file
     * @return array
     */
    function parse($p_provider, $p_file, $p_categories) {

        $parser = new KinoData_Parser_SimpleXML;
        $result = $parser->parse($p_provider, $p_file, $p_categories);

        return $result;
    } // fn parse

    //function readCategories() {
    //    ;
    //}

} // class KinoData_Parser

/**
 * KinoData Parser that makes use of the SimpleXML PHP extension.
 */
class KinoData_Parser_SimpleXML {

    private function setSourceFiles($p_fileNamesIn, &$p_fileNamesOut, &$p_filesToUnlink)
    {
        if (empty($p_filesToUnlink)) {
            $p_filesToUnlink = array();
        }
        if (empty($p_fileNamesOut)) {
            $p_fileNamesOut = array();
        }
        if (empty($p_fileNamesIn)) {
            return;
        }

        foreach ($p_fileNamesIn as $one_file_name) {
            $one_file_name_arr = explode('.', $one_file_name);
            $one_suffix = strtolower($one_file_name_arr[count($one_file_name_arr) - 1]);

            if ('zip' == $one_suffix) {
                $zip_hnd = zip_open($one_file_name);
                if (is_numeric($zip_hnd)) {continue;}
                while (true) {
                    $zip_entry = zip_read($zip_hnd);
                    if ((!$zip_entry) || is_numeric($zip_entry)) {
                        break;
                    }
                    if (!zip_entry_open($zip_hnd, $zip_entry, 'rb')) {
                        continue;
                    }
                    $entry_content = '';
                    $entry_name = zip_entry_name($zip_entry);
                    $entry_name_arr = explode('.', $entry_name);
                    $entry_name_suff = strtolower($entry_name_arr[count($entry_name_arr) - 1]);
                    $is_valid = false;
                    $add_suffix = '';
                    if ('xml' == $entry_name_suff) {
                        $entry_is_valid = true;
                        $entry_add_suffix = '.xml';
                    }
                    elseif ('gz' == $entry_name_suff) {
                        if (2 <= count($entry_name_arr)) {
                            $entry_name_suff_sub = strtolower($entry_name_arr[count($entry_name_arr) - 2]);
                            if ('xml' == $entry_name_suff_sub) {
                                $entry_is_valid = true;
                                $entry_add_suffix = '.xml.gz';
                            }
                        }
                    }
                    if ($entry_is_valid) {
                        $entry_content = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                    }
                    zip_entry_close($zip_entry);
                    if (!$entry_is_valid) {
                        continue;
                    }

//var_dump($entry_name);
                    try {
                        //$zip_temp_name = tempnam(sys_get_temp_dir(), '' . mt_rand(100, 999)) . '-' . basename($entry_name) . '-' . $entry_add_suffix;
                        $zip_temp_name_ini = tempnam(sys_get_temp_dir(), '' . mt_rand(100, 999));
                        $zip_temp_name = $zip_temp_name_ini . $entry_add_suffix;
//var_dump($zip_temp_name);
                        $zip_temp_hnd = fopen($zip_temp_name, 'wb');
                        fwrite($zip_temp_hnd, $entry_content);
                        fclose($zip_temp_hnd);

                        $p_fileNamesOut[] = $zip_temp_name;
                        $p_filesToUnlink[] = $zip_temp_name;
                        if ($zip_temp_name != $zip_temp_name_ini) {
                            $p_filesToUnlink[] = $zip_temp_name_ini;
                        }
                    }
                    catch (Exception $exc) {
                        //var_dump($exc);
                        continue;
                    }

                }
                zip_close($zip_hnd);
                continue;
            }

            if ('gz' == $one_suffix) {
                $p_fileNamesOut[] = 'compress.zlib://' . $one_file_name;
                continue;
            }

            $p_fileNamesOut[] = $one_file_name;
        }

    }


    //private function parseMoviesInfo($p_moviesInfosFiles, $p_moviesGenresFiles, $p_moviesLinksFiles)
    public function parseMoviesInfo($p_moviesInfosFiles, $p_moviesGenresFiles, $p_moviesLinksFiles)
    {
        // movies table: movie_id(int), movie_key(string), title(string), genres(json:strings), tim(int), url(string), images(json:id,tim,name,rank,label,url,w,h,updated), trailers(w,h,time,format,url,updated), 
        //parse_movies_info();

        $movies_infos_files = array();
        $movies_genres_files = array();
        $movies_links_files = array();

        $files_to_unlink = array();

        if (!empty($p_moviesInfosFiles)) {
            $this->setSourceFiles($p_moviesInfosFiles, $movies_infos_files, $files_to_unlink);

        }
        if (!empty($p_moviesGenresFiles)) {
            $this->setSourceFiles($p_moviesGenresFiles, $movies_genres_files, $files_to_unlink);
/*
            foreach ($p_moviesGenresFiles as $one_file_name) {
                $one_file_name_arr = explode('.', $one_file_name);
                if ('gz' == strtolower($one_file_name_arr[count($one_file_name_arr) - 1])) {
                    $one_file_name = 'compress.zlib://' . $one_file_name;
                }
                $movies_genres_files[] = $one_file_name;
            }
*/
        }
        if (!empty($p_moviesLinksFiles)) {
            $this->setSourceFiles($p_moviesLinksFiles, $movies_links_files, $files_to_unlink);
/*
            foreach ($p_moviesLinksFiles as $one_file_name) {
                $one_file_name_arr = explode('.', $one_file_name);
                if ('gz' == strtolower($one_file_name_arr[count($one_file_name_arr) - 1])) {
                    $one_file_name = 'compress.zlib://' . $one_file_name;
                }
                $movies_links_files[] = $one_file_name;
            }
*/
        }

        $movies_genres = array();
        $movies_infos = array();

        // movies general info
        $mov_infos_parts = array('imdb' => 'movimb', 'spec' => 'movspc', 'key' => 'movkey', 'title' => 'movtit', 'directed' => 'movdir', 'url' => 'movlnk', 'trailer' => 'movtra',);
        foreach($movies_infos_files as $one_mov_file) {
            $one_mov_xml = simplexml_load_file($one_mov_file);
            foreach ($one_mov_xml->movie as $one_movie) {
                $one_mov_id = trim('' . $one_movie->movid);
                if (empty($one_mov_id)) {
                    continue;
                }
                //$one_mov_tim = trim('' . $one_movie->movtim);
                $one_mov_info = array();
                if (isset($movies_infos[$one_mov_id])) {
                    $one_mov_info = $movies_infos[$one_mov_id];
                }

                $one_mov_desc = trim('' . $one_movie->movsyd);
                if (empty($one_mov_desc)) {
                    $one_mov_desc = trim('' . $one_movie->movcgd);
                }
                if (empty($one_mov_desc)) {
                    $one_mov_desc = trim('' . $one_movie->movlea);
                }
                if ((!isset($one_mov_info['desc'])) || (empty($one_mov_info['desc']))) {
                    $one_mov_info['desc'] = $one_mov_desc;
                }

                foreach ($mov_infos_parts as $one_mov_infos_key => $one_mov_infos_spec) {
                    $one_mov_infos_value = trim('' . $one_movie->$one_mov_infos_spec);
                    if ((!isset($one_mov_info[$one_mov_infos_key])) || (empty($one_mov_info[$one_mov_infos_key]))) {
                        $one_mov_info[$one_mov_infos_key] = $one_mov_infos_value;
                    }
                }

                $movies_infos[$one_mov_id] = $one_mov_info;

                //$one_mov_imdb = trim('' . $one_movie->movimb);
                //$one_mov_spec = trim('' . $one_movie->movspc);
                //$one_mov_key = trim('' . $one_movie->movkey);
                //$one_mov_title = trim('' . $one_movie->movtit);
                //$one_mov_directed = trim('' . $one_movie->movdir);
                //$one_mov_link = trim('' . $one_movie->movlnk);
                //$one_mov_trailer = trim('' . $one_movie->movtra);

            }
        }


        // movies genres info
        foreach($movies_genres_files as $one_mov_file) {
            $one_mov_xml = simplexml_load_file($one_mov_file);
            foreach ($one_mov_xml->genre as $one_genre) {
                $one_gen_id = trim('' . $one_genre->genid);
                if (empty($one_gen_id)) {
                    continue;
                }
                $one_gen_info = array();
                if (isset($movies_genres[$one_gen_id])) {
                    $one_gen_info = $movies_infos[$one_gen_id];
                }

                $one_gen_de = trim('' . $one_genre->gennad);
                if (!empty($one_gen_de)) {
                    $one_gen_info['de'] = $one_gen_de;
                }
                $one_gen_en = trim('' . $one_genre->gennae);
                if (!empty($one_gen_en)) {
                    $one_gen_info['en'] = $one_gen_en;
                }

                $movies_genres[$one_gen_id] = $one_gen_info;
            }

            foreach ($one_mov_xml->movie as $one_movie) {
                $one_mov_id = trim('' . $one_movie->movid);
//if ('100' == ('' . $one_mov_id)) {
//    var_dump($one_movie);
//}
                if (empty($one_mov_id)) {
                    continue;
                }
                $one_mov_info = array();
                if (isset($movies_infos[$one_mov_id])) {
                    $one_mov_info = $movies_infos[$one_mov_id];
                }
//if ('100' == ('' . $one_mov_id)) {
//    var_dump($one_mov_info);
//}

                //$one_mov_key = trim('' . $one_movie->movkey);
                //$one_mov_title = trim('' . $one_movie->movtit);
                $one_mov_genres = array();
                foreach ($one_movie->movgen->genreid as $one_mov_gen) {
                    $one_mov_gen_id = trim('' . $one_mov_gen);
//if ('100' == ('' . $one_mov_id)) {
//    var_dump($one_mov_gen_id);
//    var_dump($movies_genres[$one_mov_gen_id]);
//}
                    if (isset($movies_genres[$one_mov_gen_id])) {
                        $one_genres_names = $movies_genres[$one_mov_gen_id];
                        if ((isset($one_genres_names['de'])) && (!empty($one_genres_names['de']))) {
                            $one_mov_genres[$one_mov_gen_id] = $one_genres_names['de'];
                        }
                        elseif ((isset($one_genres_names['en'])) && (!empty($one_genres_names['en']))) {
                            $one_mov_genres[$one_mov_gen_id] = $one_genres_names['en'];
                        }
                    }
                }

                if (!empty($one_mov_genres)) {
                    $one_mov_info['genres'] = $one_mov_genres;
                    $movies_infos[$one_mov_id] = $one_mov_info;
                }

                //$movies_infos[$one_mov_id] = array('key' => $one_mov_key, 'title' => $one_mov_title, 'genres' => $one_mov_genres);
            }
        }

        // movies links info
        foreach($movies_links_files as $one_lnk_file) {
            $one_lnk_xml = simplexml_load_file($one_lnk_file);
            foreach ($one_lnk_xml->movie as $one_movie) {
                $one_mov_id = trim('' . $one_movie->m_movid);
                if (empty($one_mov_id)) {
                    continue;
                }
                $one_mov_info = array();
                if (isset($movies_infos[$one_mov_id])) {
                    $one_mov_info = $movies_infos[$one_mov_id];
                }
                $one_mov_url = trim('' . $one_movie->m_movurl);
                if (!empty($one_mov_url)) {
                    $one_mov_info['link_url'] = $one_mov_url;
                    $movies_infos[$one_mov_id] = $one_mov_info;
                }
            }

            foreach ($one_lnk_xml->image as $one_lnk_image) {
                $one_mov_id = trim('' . $one_lnk_image->i_movid);
                if (empty($one_mov_id)) {
                    continue;
                }
                $one_mov_info = array();
                if (isset($movies_infos[$one_mov_id])) {
                    $one_mov_info = $movies_infos[$one_mov_id];
                }
                $one_mov_info_images = array();
                if (isset($one_mov_info['link_images'])) {
                    $one_mov_info_images = $one_mov_info['link_images'];
                }

                $one_img_id = trim('' . $one_lnk_image->i_imgid);
                if (empty($one_img_id)) {
                    continue;
                }

                $one_img_url = trim('' . $one_lnk_image->i_imgurl);
                $one_img_w = trim('' . $one_lnk_image->i_imgsizsxm);
                $one_img_h = trim('' . $one_lnk_image->i_imgsizsym);

                if (!empty($one_img_url)) {
                    $one_mov_info_images[$one_img_id] = array('url' => $one_img_url, 'width' => $one_img_w, 'height' => $one_img_h);
                    $one_mov_info['link_images'] = $one_mov_info_images;
                    $movies_infos[$one_mov_id] = $one_mov_info;
                }
            }

            foreach ($one_lnk_xml->trailer as $one_lnk_trailer) {
                $one_mov_id = trim('' . $one_lnk_trailer->t_movid);
                if (empty($one_mov_id)) {
                    continue;
                }
                $one_mov_info = array();
                if (isset($movies_infos[$one_mov_id])) {
                    $one_mov_info = $movies_infos[$one_mov_id];
                }
                $one_trl_w = trim('' . $one_lnk_trailer->t_movtrw);
                $one_trl_h = trim('' . $one_lnk_trailer->t_movtrh);
                $one_trl_format = trim('' . $one_lnk_trailer->t_movtrc);
                $one_trl_url = trim('' . $one_lnk_trailer->t_traurl);
                if (!empty($one_trl_url)) {
                    $one_mov_info['link_trailer'] = array('url' => $one_trl_url, 'width' => $one_trl_w, 'height' => $one_trl_h, 'format' => $one_trl_format);
                    $movies_infos[$one_mov_id] = $one_mov_info;
                }
            }
        }


        // unlink tmp (zip extracted) files
        foreach ($files_to_unlink as $one_temp_name) {
            try {
                unlink($one_temp_name);
            }
            catch (Exception $exc) {
                continue;
            }
        }

//foreach($movies_infos as $m_id => $m_info) {
//    if (!is_numeric($m_id)) {
//        var_dump($m_id);
//    }
//}

        // return parsed info on movies
        return $movies_infos;

    }

    public function updateMoviesInfo($p_moviesInfosFiles, $p_moviesGenresFiles, $p_moviesLinksFiles)
    {
        $movies_info = $this->parseMoviesInfo($p_moviesInfosFiles, $p_moviesGenresFiles, $p_moviesLinksFiles);

        $sqlite_name = '/tmp/movies001.sqlite';
        $table_name = 'movies';

        ksort($movies_info);
//var_dump(count($movies_info));
        $fh = fopen('/tmp/save001.json', 'w');
        fwrite($fh, json_encode($movies_info));
        fclose($fh);


        $cre_req = 'CREATE TABLE IF NOT EXISTS ' . $table_name . ' (movie_id PRIMARY KEY, movie_key TEXT UNIQUE, movie_info TEXT)';
        $ins_req = 'INSERT OR REPLACE INTO ' . $table_name . ' (movie_id, movie_key, movie_info) VALUES (:movie_id, :movie_key, :movie_info)';

        @$db = new PDO ('sqlite:' . $sqlite_name);
        $stmt = $db->prepare($cre_req);
        $res = $stmt->execute();
        if (!$res) {
            echo ' wtf create ';
            return false;
        }

        $db->beginTransaction();
        $stmt = $db->prepare($ins_req);
        foreach ($movies_info as $mov_id => $mov_info) {
            $mov_key = '';
            if (isset($mov_info['key'])) {
                $mov_key = $mov_info['key'];
            }
            $mov_info = json_encode($mov_info);

            $stmt->bindParam(':movie_id', $mov_id, PDO::PARAM_INT);
            $stmt->bindParam(':movie_key', $mov_key, PDO::PARAM_STR);
            $stmt->bindParam(':movie_info', $mov_info, PDO::PARAM_STR);

            $res = $stmt->execute();
            if (!$res) {
                echo ' wtf insert ';
                return false;
            }
        }
        $db->commit();


    }



    //private function parseKinosInfo($p_kinosInfosFiles, $p_moviesDatabase)
    public function parseKinosInfo($p_kinosInfosFiles, $p_moviesDatabase)
    {
        // movies table: movie_id(int), movie_key(string), title(string), genres(json:strings), tim(int), url(string), images(json:id,tim,name,rank,label,url,w,h,updated), trailers(w,h,time,format,url,updated), 
        //parse_movies_info();

        $kinos_infos_files = array();
        $files_to_unlink = array();

        $movies_screens = array();

        if (!empty($p_kinosInfosFiles)) {
            $this->setSourceFiles($p_kinosInfosFiles, $kinos_infos_files, $files_to_unlink);
        }

        $other_desc_parts = array('weewee', 'weelea', 'weetxs');
        foreach ($kinos_infos_files as $one_kino_file) {
            $one_kino_xml = simplexml_load_file($one_kino_file);
            foreach ($one_kino_xml->kino as $one_kino) {
                //$one_kino_id = trim('' . $one_kino->theaterid);
                //if (empty($one_kino_id)) {
                //    continue;
                //}
                foreach ($one_kino->movie as $one_movie) {
                    $one_movie_id = trim('' . $one_movie->filmid);
                    if (empty($one_movie_id)) {
                        continue;
                    }
                    $one_movie_key = trim('' . $one_movie->filmkey); // connector to the movies data, but can be empty!
                    $one_movie_title = trim('' . $one_movie->filmtitle);
                    $one_movie_desc = trim('' . $one_movie->filmcig_d);
                    $one_movie_other = array();
                    foreach ($other_desc_parts as $one_desc_part) {
                        $one_movie_oth_one = trim('' . $one_movie->$one_desc_part);
                        if (!empty($one_movie_oth_one)) {
                            $one_movie_other[] = $one_movie_oth_one;
                        }
                    }
                    $one_movie_dates = trim('' . $one_movie->prolis);
                    if (empty($one_movie_dates)) {
                        continue;
                    }
                    $one_movie_dates_arr = explode(';', $one_movie_dates);
                    $one_movie_dates_cnt = count($one_movie_dates_arr) - 1;
                    //$one_screen_date_last = null;
                    $one_screen_dates = array();
                    foreach ($one_movie_dates_arr as $one_movie_screen) {
                        $one_movie_screen_arr = explode(':', $one_movie_screen);
                        if (2 > count($one_movie_screen_arr)) {
                            continue;
                        }
                        $one_movie_screen_info = array();
                        if (4 <= count($one_movie_screen_arr)) {
                            $one_movie_screen_info['flag'] = $one_movie_screen_arr[3];
                        }
                        if (3 <= count($one_movie_screen_arr)) {
                            $one_movie_screen_info['lang'] = $one_movie_screen_arr[2];
                        }
                        $one_movie_screen_info['time'] = $one_movie_screen_arr[1];

                        $one_movie_screen_date = $one_movie_screen_arr[0];
                        $one_movie_screen_date_arr = explode('.', $one_movie_screen_date);
                        if (3 != count($one_movie_screen_date_arr)) {
                            continue;
                        }
                        $one_screen_date_use = $one_movie_screen_date_arr[2] . '-' . $one_movie_screen_date_arr[1] . '-' . $one_movie_screen_date_arr[0];
                        if (isset($one_screen_dates[$one_screen_date_use])) {
                            $one_screen_dates[$one_screen_date_use][] = $one_movie_screen_info;
                        }
                        else {
                            $one_screen_dates[$one_screen_date_use] = array($one_movie_screen_info);
                        }
                    }

                    $movies_screens[] = array(
                        // 'kino_id' => $one_kino_id, // kino id, address, ...
                        'movie_id' => $one_movie_id,
                        'movie_key' => $one_movie_key,
                        'title' => $one_movie_title,
                        'desc' => $one_movie_desc,
                        'other' => $one_movie_other,
                        'dates' => $one_screen_dates,
                    );
                }

            }
        }


        return $movies_screens;
    }




    /**
     * read new cinema files
     * ... load info about movies
     * ... load info about genres
     * ... load (info about) images
     */
    public function prepare($p_categories, $p_limits, $p_cancels)
    {
        //updateMoviesInfo($p_moviesInfosFiles, $p_moviesGenresFiles, $p_$moviesLinksFiles);
    }


    /**
     * Parses KinoData data (by SimplXML)
     *
     * @param string $p_file file name of the eventdata file
     * @return array
     */
    private function parse($p_provider, $p_file, $p_categories) {

		$events = array();

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


        // day of event set (event record)
        $recdate = $xml->export->date;

		// $xml->kino is an array of (per-kino) movie sets
		foreach ($xml->kino as $event_kino) {

			// array of events info
			$movie_set = $event_kino->movie;

			// one event is object of simple properties, many of them are empty
			foreach ($movie_set as $event) {
				$event_info = array('provider_id' => $p_provider);

				// Ids
				// number, event id, shall be unique
                // shall be assigned utomatically during insertion into database, but beware that
                // a single screening (cinema/movie/date/time) can probably occur at multiple kino-daten files (at different days)
				$event_info['event_id'] = null;

				// string, movie key, shall be shared among events of particular repeated actions
				$x_filmkey = '' . $event->filmkey;
				if (empty($x_filmkey)) {
					$x_filmkey = null;
				}
				$event_info['turnus_key'] = $x_filmkey;

				// number, movie id
                // will be taken from the movie xml file, according to turnus key,
                // but it may happen that we will not get an id - then shall be autogenerated
				$event_info['turnus_id'] = null;

				// number, location id
				$x_theaterid = '' . $event_kino->theaterid;
				if (empty($x_theaterid)) {
					$x_theaterid = null;
				}
				$event_info['location_id'] = $x_theaterid;


				// Categories
				// * main type fields
				$event_info['event_type_id'] = 7;
				$event_info['event_type'] = 'cinema';

				// Names
				// * main display provider name
				$x_theatername = '' . $event_kino->theatername;
				if (empty($x_theatername)) {
					$x_theatername = null;
				}
				$event_info['event_location'] = $x_theatername;

				// * main display turnus name
				// event name
				$x_filmtitle = '' . $event->filmtitle;
				if (empty($x_filmtitle)) {
					$x_filmtitle = null;
				}
				$event_info['event_name'] = $x_filmtitle;

                $x_theatertelcost = '' . $event->theatertelcost;
                if (empty($x_theatertelcost)) {
                    $x_theatertelcost = null;
                }
                $event_info['fee_price'] = $x_theatertelcost;

				// Locations
				// !!! no country info
				// * main location info
				// town name
				$x_theatertown = '' . $event_kino->theatertown;
				if (empty($x_theatertown)) {
					$x_theatertown = null;
				}
				$event_info['location_town'] = $x_theatertown;

				// zip code
				$x_theaterzip = '' . $event_kino->theaterzip;
				if (empty($x_theaterzip)) {
					$x_theaterzip = null;
				}
				$event_info['location_zip'] = $x_theaterzip;

				// street address, free form, but usually 'street_name house_number'
				$x_theateradress = '' . $event_kino->theateradress;
				if (empty($x_theateradress)) {
					$x_theateradress = null;
				}
				$event_info['location_street'] = $x_theateradress;

				// Date, time
				// * main date-time info

				$event_screening = array();

                $x_prolis = '' . $event->prolis;
                if (!empty($x_prolis)) {
                    foreach(explode(';', trim($x_prolis)) as $one_screening) {
                        $one_screening = trim($one_screening);
                        if (empty($one_screening)) {
                            continue;
                        }
                        // one screening info shall be: "DD.MM.YYYY:HH.mm:L/n/g:"
                        $one_screening_arr = explode(':', $one_screening);
                        if (2 > count($one_screening_arr)) {
                            continue;
                        }
                        $one_screening_date_arr = explode('.', $one_screening_arr[0]);
                        if (3 != count($one_screening_date_arr)) {
                            continue;
                        }
                        $one_screening_date = substr($one_screening_date_arr, 6, 4) . '-' . substr($one_screening_date_arr, 3, 2) . '-' . substr($one_screening_date_arr, 0, 2);
                        if (10 != strlen($one_screening_date)) {
                            continue;
                        }

                        $one_screening_time = trim($one_screening_arr[1]);
                        if (empty($one_screening_time)) {
                            $one_screening_time = null;
                        }

                        $one_screening_lang = null;
                        if (3 <= count($one_screening_arr)) {
                            $one_screening_lang = trim($one_screening_arr);
                            if (empty($one_screening_lang)) {
                                $one_screening_lang = null;
                            }
                        }

                        if (!array_key_exists($one_screening_date, $event_screening)) {
                            $event_screening[$one_screening_date] = array();
                        }
                        $event_screening[$one_screening_date][] = array('time' => $one_screening_time, 'lang' => $one_screening_lang);
                    }
                }

                // no screening means no event
                if (0 == count($event_screening)) {
                    continue;
                }

/*
				$event_info['event_date'] = null;
				$event_info['event_time'] = null;
				$event_info['event_open'] = null;
*/

				// Descriptions
                // shall be taken from the movies file, if the movie is there
				$event_info['event_texts'] = array();

				// Links

				// web link for cinema
				$x_theaterurl = '' . $event_kino->theaterurl;
				if (empty($x_theaterurl)) {
					$x_theaterurl = null;
				}
				$event_info['event_web'] = $x_theaterurl;

				// location email address, none here
				$event_info['event_email'] = null;

				// location phone number
				$x_theaterphone = '' . $event_kino->theaterphone;
				if (empty($x_theaterphone)) {
					$x_theaterphone = null;
				}
				$event_info['event_phone'] = $x_theaterphone;

				// Multimedia
				$event_info['event_images'] = array();

			}


		}

    } // fn parse
} // class KinoData_Parser_SimpleXML




/**
 * MovieData Importer class for managing parsing of MovieData files.
 */
class MovieData_Parser {

    /**
     * Parses MovieData data (by MovieData_Parser_SimpleXML)
     *
     * @param string $p_file file name of the movie file
     * @return array
     */
    function parse($p_provider, $p_file, $p_categories) {

        $parser = new MovieData_Parser_SimpleXML;
        $result = $parser->parse($p_provider, $p_file, $p_categories);

        return $result;
    } // fn parse
} // class MovieData_Parser

/**
 * MovieData Parser that makes use of the SimpleXML PHP extension.
 */
class MovieData_Parser_SimpleXML {

    /**
     * Parses MovieData data (by SimplXML)
     *
     * @param string $p_file file name of the eventdata file
     * @return array
     */
    function parse($p_provider, $p_file, $p_categories) {

		$movies = array();

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


        // day of event set (event record)
        $recdate = $xml->export->date;

		// $xml->movie is an array of movie descs
		foreach ($xml->movie as $movie_info) {

            $movie_info = array('provider_id' => $p_provider);

            $x_movid = '' . $movie->movid;
            if (empty($x_movid)) {
                $x_movid = null;
            }

            $x_movkey = '' . $movie->movkey;
            if (empty($x_movkey)) {
                $x_movkey = null;
            }

            if (empty($x_movid) || empty($x_movkey)) {
                continue;
            }

            $movie_key = $x_movkey;
            $movie_info['movie_id'] = $x_movid;

            $movie_de = null;
            $movie_fr = null;
            $movie_it = null;

            $x_movcgd = '' . $movie->movcgd;
            if (!empty($x_movcgd)) {
                $movie_de = $x_movcgd;
            }
            if (empty($movie_de)) {
                $x_movsyd = '' . $movie->movsyd;
                if (!empty($x_movsyd)) {
                    $movie_de = $x_movsyd;
                }
            }

            $x_movcgf = '' . $movie->movcgf;
            if (!empty($x_movcgf)) {
                $movie_fr = $x_movcgf;
            }
            if (empty($movie_fr)) {
                $x_movsyf = '' . $movie->movsyf;
                if (!empty($x_movsyf)) {
                    $movie_fr = $x_movsyf;
                }
            }

            $x_movcgi = '' . $movie->movcgi;
            if (!empty($x_movcgi)) {
                $movie_it = $x_movcgi;
            }
            if (empty($movie_it)) {
                $x_movsyi = '' . $movie->movsyi;
                if (!empty($x_movsyi)) {
                    $movie_it = $x_movsyi;
                }
            }

            $movie_info['movie_text'] = array('de' => $movie_de, 'fr' => $movie_fr, 'it' => $movie_it);

            $movies[$movie_key] = $movie_info;

		}

    } // fn parse
} // class MovieData_Parser_SimpleXML






/*

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


$provider_id = 2;

$kino_name = 'kinodatenCH.xml';
$kd_parser = new KinoData_Parser();
$kd_parser->parse($provider_id, $kino_name, $known_categories);

$movie_name = 'test_mov.xml';
$md_parser = new MovieData_Parser();
$md_parser->parse($provider_id, $movie_name, $known_categories);

*/




