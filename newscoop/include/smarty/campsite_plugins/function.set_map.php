<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

function aux_parser_set_map_area($p_areaSpec, &$p_areaCons)
{
    $area_val = strtolower(str_replace('"', '""', trim($p_areaSpec)));
    $area_val_tmp = explode(" ", $area_val);
    $area_val_arr = array();
    foreach ($area_val_tmp as $area_part) {
        $area_part = strtolower(trim($area_part));
        if (0 < strlen($area_part)) {
            $area_val_arr[] = $area_part;
        }
    }

    $parsed_polygon_name = "";
    $parsed_polygon_values = array();
    $inside_area = false;

    $known_area_types = array("rectangle" => true, "polygon" => true);
    $cur_corner = array("latitude" => "", "longitude" => "");
    $cur_corner_stage = "latitude";

    foreach ($area_val_arr as $area_token) {
        $area_token = trim($area_token, ",;:");
        if (array_key_exists($area_token, $known_area_types)) {
            if ($inside_area) {
                $p_areaCons[] = array($parsed_polygon_name => $parsed_polygon_values);
            }
            $inside_area = true;
            $parsed_polygon_values = array();
            $parsed_polygon_name = $area_token;
            $cur_corner_stage = "latitude";
            continue;
        }
        if (!$inside_area) {break;} // wrong area spec.

        $cur_corner[$cur_corner_stage] = $area_token;
        if ("latitude" == $cur_corner_stage) {
            $cur_corner_stage = "longitude";
        }
        else {
            $cur_corner_stage = "latitude";
            $parsed_polygon_values[] = $cur_corner;
        }
    }

    if ($inside_area) {
        $p_areaCons[] = array($parsed_polygon_name => $parsed_polygon_values);
    }

}

/**
 * Campsite set_map function plugin
 *
 * Type:     function
 * Name:     set_map
 * Purpose:
 *
 * @param array
 *     $p_params[name] The Name of the article to be set
 *     $p_params[number] The Number of the article to be set
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_set_map($p_params, &$p_smarty)
{
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('gimme');

    $parameters = array();

    $con_authors = array();
    $con_articles = array();
    $con_issues_num = array();
    //$con_issues_str = array();
    $con_sections_num = array();
    $con_sections_str = array();
    $con_topics = array();
    $con_match_all_topics = array();
    //$con_match_any_topic = array();
    $con_has_multimedia = array();
    $con_start_date = array();
    $con_end_date = array();
    $con_date = array();
    //$con_area = array();
    $con_areas = array();
    $con_match_any_area = array();
    //$con_areas_match = array();

    if (isset($p_params['authors'])) {
        foreach (explode(",", $p_params['authors']) as $one_author) {
            //$one_author = str_replace('"', '""', trim($one_author));
            $one_author = trim($one_author);
            if (0 < strlen($one_author)) {
                $con_authors[] = $one_author;
            }
        }
    }
    // $con_authors_str = '"' . implode('", "', $con_authors) . '"';
    // SELECT DISTINCT id FROM Authors WHERE trim(concat(first_name, " ", last_name)) IN ($con_authors_str) OR trim(concat(last_name, " ", first_name) IN ($con_authors_str));
    // SELECT DISTINCT id FROM AuthorAliases WHERE trim(alias) IN ($con_authors_str);

    if (isset($p_params['articles'])) {
        foreach (explode(",", "" . $p_params['articles']) as $one_article) {
            $one_article = trim($one_article);
            if (is_numeric($one_article)) {
                $con_articles[] = $one_article;
            }
        }
    }

    if (isset($p_params['issues'])) {
        foreach (explode(",", $p_params['issues']) as $one_issue) {
            $one_issue = trim($one_issue);
            if (is_numeric($one_issue)) {
                $con_issues_num[] = $one_issue;
            }
            //elseif (0 < strlen($one_issue)) {
            //    $one_issue = str_replace('"', '""', trim($one_issue));
            //    $con_issues_str[] = $one_issue;
            //}
        }
    }

    if (isset($p_params['sections'])) {
        foreach (explode(",", $p_params['sections']) as $one_section) {
            $one_section = trim($one_section);
            if (is_numeric($one_section)) {
                $con_sections_num[] = $one_section;
            }
            elseif (0 < strlen($one_section)) {
                //$one_section = str_replace('"', '""', trim($one_section));
                $one_section = trim($one_section);
                $con_sections_str[] = $one_section;
            }
        }
    }

    // SELECT DISTINCT fk_topic_id FROM TopicNames WHERE name IN (...);
    // then the Topic::BuildAllSubtopicsQuery on those ids
    if (isset($p_params['topics'])) {
        foreach (explode(",", $p_params['topics']) as $one_topic) {
            $one_topic = trim($one_topic);
            if (0 < strlen($one_topic)) {
                //$one_topic = str_replace('"', '""', trim($one_topic));
                $one_topic = trim($one_topic);
                $con_topics[] = $one_topic;
            }
        }
    }

    $values_known_yes = array("true" => true, "yes" => true);
    $values_known_no = array("false" => true, "no" => true);

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
                $con_has_multimedia[0] = "any";
            }
            else {
                $con_has_multimedia = array();
            }
        }
        elseif (is_string($has_multimedia_val)) {
            $has_multimedia_val = trim(strtolower($has_multimedia_val));
            if (array_key_exists($has_multimedia_val, $values_known_yes)) {$con_has_multimedia[0] = "any";}
            if (array_key_exists($has_multimedia_val, $values_known_no)) {$con_has_multimedia = array();}
            if ("video" == $has_multimedia_val) {$con_has_multimedia[0] = "video";};
            if ("image" == $has_multimedia_val) {$con_has_multimedia[0] = "image";};
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

    //$known_area_match_types = array("intersection" => true, "union" => true);
    if (isset($p_params['area_match'])) {

        $area_match_val = $p_params['area_match'];
        if (is_string($area_match_val)) {

            $area_match_val = strtolower($area_match_val);
            //if (array_key_exists($area_match_val, $known_area_match_types)) {
            //    $con_areas_match[0] = trim(strtolower($area_match_val));
            //}
            if ("intersection" == $area_match_val) {$con_match_any_area = false;}
            if ("union" == $area_match_val) {$con_match_any_area = true;}
        }
    }

    // to put the read constarints into parameters list

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

/*
    foreach ($con_issues_str as $one_issue) {
        $leftOperand = 'issue_name';
        $rightOperand = $one_issue;
        $operator = new Operator('is', 'sql');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;
    }
*/

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

    if (0 < count($con_areas_match)) {
        $leftOperand = 'matchanyarea';
        $rightOperand = $con_match_any_area[0];
        $operator = new Operator('is', 'sql');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;
    }

    $campsite->map_dynamic = $parameters;

} // fn smarty_function_set_map

?>
