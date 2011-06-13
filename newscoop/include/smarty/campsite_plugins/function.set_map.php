<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Auxiliar function for parsing area map constraints
 *
 * @param string
 *     $p_areaSpec The area(s) constraint
 * @param array
 *     $p_areaCons The array with parsed area constraints
 */
function aux_parser_set_map_area($p_areaSpec, &$p_areaCons)
{
    $area_val = strtolower(str_replace('"', '""', trim($p_areaSpec)));
    $area_val_tmp = explode(' ', $area_val);
    $area_val_arr = array();
    foreach ($area_val_tmp as $area_part) {
        $area_part = strtolower(trim($area_part));
        if (0 < strlen($area_part)) {
            $area_val_arr[] = $area_part;
        }
    }

    $parsed_polygon_name = '';
    $parsed_polygon_values = array();
    $inside_area = false;

    $known_area_types = array('rectangle' => true, 'polygon' => true);
    $cur_corner = array('latitude' => '', 'longitude' => '');
    $cur_corner_stage = 'latitude';

    foreach ($area_val_arr as $area_token) {
        $area_token = trim($area_token, ',;:');
        if (array_key_exists($area_token, $known_area_types)) {
            if ($inside_area) {
                $p_areaCons[] = array($parsed_polygon_name => $parsed_polygon_values);
            }
            $inside_area = true;
            $parsed_polygon_values = array();
            $parsed_polygon_name = $area_token;
            $cur_corner_stage = 'latitude';
            continue;
        }
        if (!$inside_area) {break;} // wrong area spec.

        $cur_corner[$cur_corner_stage] = $area_token;
        if ('latitude' == $cur_corner_stage) {
            $cur_corner_stage = 'longitude';
        }
        else {
            $cur_corner_stage = 'latitude';
            $parsed_polygon_values[] = $cur_corner;
        }
    }

    if ($inside_area) {
        $p_areaCons[] = array($parsed_polygon_name => $parsed_polygon_values);
    }

} // fn aux_parser_set_map_area

/**
 * Campsite set_map function plugin
 *
 * Type:     function
 * Name:     set_map
 * Purpose:
 *
 * @param array
 *     $p_params the map constarints set at the template
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_set_map($p_params, &$p_smarty)
{
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('gimme');
    $run_article = ($campsite->article->defined) ? $campsite->article : null;
    $run_language = $campsite->language;

    $parameters = array();
    $running = '_current';

    $map_label = '';
    $map_max_points = 0;

    $con_authors = array();
    $con_articles = array();
    $con_issues_num = array();
    $con_sections_num = array();
    $con_sections_str = array();
    $con_topics = array();
    $con_match_all_topics = array();
    $con_has_multimedia = array();
    $con_start_date = array();
    $con_end_date = array();
    $con_date = array();
    $con_areas = array();
    $con_match_any_area = array();
    $con_exact_area = array();
    $con_icons = array();

    if (isset($p_params['label'])) {
        $map_label = trim('' . $p_params['label']);
    }
    if (isset($p_params['max_points'])) {
        $map_max_points = trim('' . $p_params['max_points']);
        if (!is_numeric($map_max_points)) { $map_max_points = 0; }
        $map_max_points = 0 + $map_max_points;
    }
    $campsite->map_dynamic_max_points = $map_max_points;

    if (isset($p_params['authors'])) {
        foreach (explode(',', $p_params['authors']) as $one_author) {
            $one_author = trim('' . $one_author);
            if (0 < strlen($one_author)) {
                if ($running == $one_author) {
                    if ($run_article) {
                        $run_authors = $run_article->authors;
                        foreach ($run_authors as $art_author) {
                            $con_authors[] = $art_author->name;
                        }
                    }
                }
                else {
                    $con_authors[] = $one_author;
                }
            }
        }
    }

    if (isset($p_params['articles'])) {
        foreach (explode(',', '' . $p_params['articles']) as $one_article) {
            $one_article = trim('' . $one_article);
            if (is_numeric($one_article)) {
                $con_articles[] = $one_article;
            }
        }
    }

    if (isset($p_params['issues'])) {
        foreach (explode(',', $p_params['issues']) as $one_issue) {
            $one_issue = trim('' . $one_issue);
            if ($running == $one_issue) {
                if ($run_article) {
                    $run_issue = $run_article->issue;
                    if ($run_issue) {
                        $con_issues_num[] = $run_issue->number;
                    }
                }
            }
            else {
                if (is_numeric($one_issue)) {
                    $con_issues_num[] = $one_issue;
                }
            }
        }
    }

    if (isset($p_params['sections'])) {
        foreach (explode(',', $p_params['sections']) as $one_section) {
            $one_section = trim('' . $one_section);
            if ($running == $one_section) {
                if ($run_article) {
                    $run_section = $run_article->section;
                    if ($run_section) {
                        $con_sections_num[] = $run_section->number;
                    }
                }
            }
            elseif (is_numeric($one_section)) {
                $con_sections_num[] = $one_section;
            }
            elseif (0 < strlen($one_section)) {
                $one_section = trim($one_section);
                $con_sections_str[] = $one_section;
            }
        }
    }

    if (isset($p_params['topics'])) {
        foreach (explode(',', $p_params['topics']) as $one_topic) {
            $one_topic = trim('' . $one_topic);
            if (0 < strlen($one_topic)) {
                if ($running == $one_topic) {
                    if ($run_article) {
                        $run_topics = $run_article->topics;
                        foreach ($run_topics as $art_topic) {
                            $con_topics[] = $art_topic . ':' . $run_language->code;
                        }
                    }
                }
                else {
                    $one_topic = trim($one_topic);
                    $con_topics[] = $one_topic;
                }
            }
        }
    }

    $values_known_yes = array('true' => true, 'yes' => true);
    $values_known_no = array('false' => true, 'no' => true);

    if (isset($p_params['match_all_topics'])) {
        $match_all_topics_val = $p_params['match_all_topics'];
        if (is_bool($match_all_topics_val)) {
            $con_match_all_topics[0] = $match_all_topics_val;
        }
        elseif (is_string($match_all_topics_val)) {
            $match_all_topics_val = trim(strtolower($match_all_topics_val));
            if (array_key_exists($match_all_topics_val, $values_known_yes)) {$con_match_all_topics[0] = true;}
            if (array_key_exists($match_all_topics_val, $values_known_no)) {$con_match_all_topics[0] = false;}
        }
    }

    if (isset($p_params['match_any_topic'])) {
        $match_any_topic_val = $p_params['match_any_topic'];
        if (is_bool($match_any_topic_val)) {
            $con_match_all_topics[0] = !$match_any_topic_val;
        }
        elseif (is_string($match_any_topic_val)) {
            $match_any_topic_val = trim(strtolower($match_any_topic_val));
            if (array_key_exists($match_any_topic_val, $values_known_yes)) {$con_match_all_topics[0] = false;}
            if (array_key_exists($match_any_topic_val, $values_known_no)) {$con_match_all_topics[0] = true;}
        }
    }

    if (isset($p_params['has_multimedia'])) {
        $has_multimedia_val = $p_params['has_multimedia'];
        if (is_bool($has_multimedia_val)) {
            if ($has_multimedia_val) {
                $con_has_multimedia[0] = 'any';
            }
            else {
                $con_has_multimedia = array();
            }
        }
        elseif (is_string($has_multimedia_val)) {
            $has_multimedia_val = trim(strtolower($has_multimedia_val));
            if (array_key_exists($has_multimedia_val, $values_known_yes)) {$con_has_multimedia[0] = 'any';}
            if (array_key_exists($has_multimedia_val, $values_known_no)) {$con_has_multimedia = array();}
            if ('video' == $has_multimedia_val) {$con_has_multimedia[0] = 'video';};
            if ('image' == $has_multimedia_val) {$con_has_multimedia[0] = 'image';};
        }
    }

    if (isset($p_params['start_date'])) {
        $start_date_val = trim($p_params['start_date']);
        $start_date_val = str_replace('"', '""', trim($start_date_val));
        if (0 < strlen($start_date_val)) {
            $con_start_date[0] = $start_date_val;
        }
    }

    if (isset($p_params['end_date'])) {
        $end_date_val = trim($p_params['end_date']);
        $end_date_val = str_replace('"', '""', trim($end_date_val));
        if (0 < strlen($end_date_val)) {
            $con_end_date[0] = $end_date_val;
        }
    }

    if (isset($p_params['date'])) {
        $date_val = trim($p_params['date']);
        $date_val = str_replace('"', '""', trim($date_val));
        if (0 < strlen($date_val)) {
            $con_date[0] = $date_val;
        }
    }

    if (isset($p_params['area'])) {
        $area_val = trim($p_params['area']);

        if (0 < strlen($area_val)) {
            aux_parser_set_map_area($area_val, $con_areas);
        }

    }

    if (isset($p_params['areas'])) {
        $area_val = trim($p_params['areas']);

        if (0 < strlen($area_val)) {
            aux_parser_set_map_area($area_val, $con_areas);
        }

    }

    if (isset($p_params['area_match'])) {

        $area_match_val = $p_params['area_match'];
        if (is_string($area_match_val)) {
            $area_match_val = strtolower($area_match_val);
            if ('intersection' == $area_match_val) {$con_match_any_area[0] = false;}
            if ('union' == $area_match_val) {$con_match_any_area[0] = true;}
        }
    }

    if (isset($p_params['area_exact'])) {

        $area_exact_val = $p_params['area_exact'];
        if (is_string($area_exact_val)) {
            $area_exact_val = strtolower($area_exact_val);
            if ('false' == $area_exact_val) {$con_exact_area[0] = false;}
            if ('true' == $area_exact_val) {$con_exact_area[0] = true;}
        }
        else {
            if ($area_exact_val) {$con_exact_area[0] = true;}
            else {$con_exact_area[0] = false;}
        }
    }

    if (isset($p_params['icons'])) {
        $icons_val = trim($p_params['icons']);
        foreach (explode(',', $icons_val) as $cur_icon) {
            $cur_icon = str_replace('"', '""', trim($cur_icon));
            if (0 < strlen($cur_icon)) {
                $con_icons[] = $cur_icon;
            }
        }
    }

    // to put the read constraints into parameters list

    foreach ($con_authors as $one_author) {
        $leftOperand = 'author';
        $rightOperand = $one_author;
        $operator = new Operator('is', 'sql');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;
    }

    foreach ($con_articles as $one_article) {
        $leftOperand = 'article';
        $rightOperand = $one_article;
        $operator = new Operator('is', 'sql');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;
    }

    foreach ($con_issues_num as $one_issue) {
        $leftOperand = 'issue';
        $rightOperand = $one_issue;
        $operator = new Operator('is', 'sql');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;
    }

    foreach ($con_sections_num as $one_section) {
        $leftOperand = 'section';
        $rightOperand = $one_section;
        $operator = new Operator('is', 'sql');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;
    }

    foreach ($con_sections_str as $one_section) {
        $leftOperand = 'section_name';
        $rightOperand = $one_section;
        $operator = new Operator('is', 'sql');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;
    }

    foreach ($con_topics as $one_topic) {
        $leftOperand = 'topic_name';
        $rightOperand = $one_topic;
        $operator = new Operator('is', 'sql');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;
    }

    if (1 == count($con_match_all_topics)) {
        $leftOperand = 'matchalltopics';
        $rightOperand = $con_match_all_topics[0];
        $operator = new Operator('is', 'sql');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;
    }

    foreach ($con_has_multimedia as $one_multimedia) {
        $leftOperand = 'multimedia';
        $rightOperand = $one_multimedia;
        $operator = new Operator('is', 'php');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;
    }

    if (0 < count($con_start_date)) {
        $leftOperand = 'date';
        $rightOperand = $con_start_date;
        $operator = new Operator('greater_equal', 'sql');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;
    }

    if (0 < count($con_end_date)) {
        $leftOperand = 'date';
        $rightOperand = $con_end_date;
        $operator = new Operator('smaller_equal', 'sql');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;
    }

    if (0 < count($con_date)) {
        $leftOperand = 'date';
        $rightOperand = $con_date;
        $operator = new Operator('is', 'sql');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;
    }

    foreach ($con_areas as $one_area) {
        $leftOperand = 'area';
        $rightOperand = json_encode($one_area);
        $operator = new Operator('is', 'php');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;
    }

    if (0 < count($con_match_any_area)) {
        $leftOperand = 'matchanyarea';
        $rightOperand = $con_match_any_area[0];
        $operator = new Operator('is', 'sql');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;
    }

    if (0 < count($con_exact_area)) {
        $leftOperand = 'exactarea';
        $rightOperand = $con_exact_area[0];
        $operator = new Operator('is', 'sql');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;
    }

    foreach ($con_icons as $one_icon) {
        $leftOperand = 'icon';
        $rightOperand = $one_icon;
        $operator = new Operator('is', 'sql');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;
    }

    // publication id has to be set for all multi-maps
    {
        $run_publicationId = 0;
        if (CampTemplate::singleton()) {
            $Context = CampTemplate::singleton()->context();
            if ($Context->publication) {
                $run_publicationId = $Context->publication->identifier;
            }
        }

        $leftOperand = 'publication';
        $rightOperand = $run_publicationId;
        $operator = new Operator('is', 'sql');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;

    }

    $campsite->map_dynamic_constraints = $parameters;
    if (!empty($con_areas)) {
        $campsite->map_dynamic_areas = json_encode($con_areas);
    }
    else {
        $campsite->map_dynamic_areas = null;
    }
    $campsite->map_dynamic_map_label = $map_label;

    // to retrieve the points for next usage
    {

        $leftOperand = 'as_array';
        $rightOperand = true;
        $operator = new Operator('is', 'php');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;

        $leftOperand = 'active_only';
        $rightOperand = true;
        $operator = new Operator('is', 'php');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;

        $leftOperand = 'text_only';
        $rightOperand = false;
        $operator = new Operator('is', 'php');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;

        $leftOperand = 'language';
        $rightOperand = (int) $run_language->number;
        $operator = new Operator('is', 'php');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;

        $leftOperand = 'constrained';
        $rightOperand = true;
        $operator = new Operator('is', 'php');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;

        $poi_count = 0;
        $poi_array = array();
        $poi_objects = Geo_MapLocation::GetListExt($parameters, array(), 0, $map_max_points, $poi_count, false, $poi_array);

        $campsite->map_dynamic_tot_points = $poi_count;

        $map_art_ids = array();
        $art_backlinks = array();
        $meta_art_objs = array();

        $arts_per_pois = array();

        $poi_retrieved_count = count($poi_array);
        for ($poi_idx = 0; $poi_idx < $poi_retrieved_count; $poi_idx++) {
            $poi_arts = array();

            $articleNos = $poi_array[$poi_idx]['art_numbers'];
            if (0 < strlen($articleNos)) {
                foreach (explode(',', $articleNos) as $one_art) {
                    $one_art = trim($one_art);
                    if (0 == strlen($one_art)) {continue;}
                    if (!is_numeric($one_art)) {continue;}
                    $one_art = 0 + $one_art;

                    $poi_arts[] = $one_art;
                    $map_art_ids[$one_art] = true;
                }
            }

            if (0 == count($poi_arts)) {
                $articleNo = $poi_array[$poi_idx]['art_number'];
                if (is_numeric($articleNo)) {
                    $articleNo = 0 + $articleNo;
                    if (0 < $articleNo) {
                        $poi_arts[] = $articleNo;
                        $map_art_ids[$articleNo] = true;
                    }
                }
            }
            $arts_per_pois[] = $poi_arts;

        }

        foreach ($map_art_ids as $one_art_id => $one_art_aux) {
            $myArticle = new MetaArticle((int) $run_language->number, $one_art_id);
            $meta_art_objs[$one_art_id] = $myArticle;
            $url = CampSite::GetURIInstance();
            $url->publication = $myArticle->publication;
            $url->issue = $myArticle->issue;
            $url->section = $myArticle->section;
            $url->article = $myArticle;
            $articleURI = $url->getURI('article');

            $art_backlinks[$one_art_id] = $articleURI;
        }
        krsort($meta_art_objs);

        for ($poi_idx = 0; $poi_idx < $poi_retrieved_count; $poi_idx++) {
            $curr_back_list = array();

            $curr_art_ids = $arts_per_pois[$poi_idx];
            foreach ($curr_art_ids as $one_art_id) {
                $curr_back_list[] = $art_backlinks[$one_art_id];
            }
            if (0 < count($curr_back_list)) {
                $poi_array[$poi_idx]['backlinks'] = $curr_back_list;
            }

        }


        $campsite->map_dynamic_points_raw = $poi_array;
        $campsite->map_dynamic_points_objects = $poi_objects;
        $campsite->map_dynamic_meta_article_objects = $meta_art_objs;

        $campsite->map_dynamic_id_counter += 1;

    }

} // fn smarty_function_set_map

?>
