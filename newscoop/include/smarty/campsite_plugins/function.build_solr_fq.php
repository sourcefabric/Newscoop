<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Builds the Solr FQ query
 *
 * Type:     function
 * Name:     build_solr_fq
 * Purpose:
 *
 * @param array $p_params
 *
 * @return string $solrFq
 *      The Solr FQ requested
 *
 * @example
 *  {{ list_search_results_solr fq={{build_solr_fq type=$smarty.get.type published=$smarty.get.published from=$smarty.get.from to=$smarty.get.to }} }}
 *
 */
function smarty_function_build_solr_fq($p_params = array(), &$p_smarty)
{
    $solrFq = '';
    if (array_key_exists('type', $p_params) && !empty($p_params['type'])) {
        $solrFq .= 'type:'.$p_params['type'];
    }

    if (array_key_exists('published', $p_params) && !empty($p_params['published'])) {
        $published = '';

        switch ($p_params['published']) {
            case '24h':
                $published = '[NOW-1DAY/HOUR TO *]';
                break;
            case '7d':
                $published = '[NOW-7DAY/DAY TO *]';
                break;
            case '1y':
                $published = '[NOW-1YEAR/DAY TO *]';
                break;
            default:
                $published = '';
                break;
        }
    }

    if (array_key_exists('from', $p_params) && !empty($p_params['from'])) {
        $fromDate = date_create_from_format('d.m.y', $p_params['from']);
        $solrFromDate = date_format($fromDate, 'Y-m-d');
    }

    if (array_key_exists('to', $p_params) && !empty($p_params['to'])) {
        $toDate = date_create_from_format('d.m.y', $p_params['to']);
        $solrToDate = date_format($toDate, 'Y-m-d');
    }

    if (!empty($solrFromDate) && !empty($solrToDate)) {
        $published = '['. $solrFromDate .' TO '. $solrToDate . ']';
    } else if (!empty($solrFromDate)) {
        $published = '['. $solrFromDate .' TO *]';
    } else if (!empty($solrToDate)) {
        $published = '[* TO '. $solrToDate .']';
    }

    if (!empty($solrFq)) {
        $solrFq .= ' AND ';
    }
    
    if (!empty($published)) {
        $solrFq .= 'published:' . $published;
    }

    return $solrFq;
} // fn smarty_function_build_solr_fq

?>
