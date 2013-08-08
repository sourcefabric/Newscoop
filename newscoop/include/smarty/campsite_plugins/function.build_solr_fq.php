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
 *  {{ list_search_results_solr fq="{{ build_solr_fq }}" }}
 *  {{ list_search_results_solr fq="{{ build_solr_fq type=$smarty.post.type }}" }}
 *
 */
function smarty_function_build_solr_fq($p_params = array(), &$p_smarty)
{
    $solrFq = '';

    // The $p_params override the $_GET
    $acceptedParams = array('type', 'published', 'from', 'to');
    $cleanParam = array();

    foreach ($acceptedParams as $key) {
        if (array_key_exists($key, $p_params) && !empty($p_params[$key])) {
            $cleanParam[$key] = $p_params[$key];
        } else if (array_key_exists($key, $_GET) && !empty($_GET[$key])) {
            $cleanParam[$key] = $_GET[$key];
        }
    }

    if (array_key_exists('published', $cleanParam) && !empty($cleanParam['published'])) {
        $published = '';

        switch ($cleanParam['published']) {
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

    if (array_key_exists('type', $cleanParam) && !empty($cleanParam['type'])) {
        $solrFq .= 'type:'.$cleanParam['type'];
    }

    if (array_key_exists('fqfrom', $cleanParam) && !empty($cleanParam['fqfrom'])) {
        $fromDate = date_create_from_format($cleanParam['fqdateformat'], $cleanParam['fqfrom']);
        if ($fromDate instanceof \DateTime) {
            $solrFromDate = date_format($fromDate, 'Y-m-d').'T00:00:00Z/DAY';
        }
    }

    if (array_key_exists('fqto', $cleanParam) && !empty($cleanParam['fqto'])) {
        $toDate = date_create_from_format($cleanParam['fqdateformat'], $cleanParam['fqto']);
        if ($toDate instanceof \DateTime) {
            $solrToDate = date_format($toDate, 'Y-m-d').'T00:00:00Z/DAY';
        }
    }

    if (!empty($solrFromDate) && !empty($solrToDate)) {
        $published = '['. $solrFromDate .' TO '. $solrToDate . ']';
    } else if (!empty($solrFromDate)) {
        $published = '['. $solrFromDate .' TO *]';
    } else if (!empty($solrToDate)) {
        $published = '[* TO '. $solrToDate .']';
    }

    if (!empty($published)) {
        if (!empty($solrFq)) {
            $solrFq .= ' AND ';
        }
        
        $solrFq .= 'published:' . $published;
    }

    return $solrFq;
} // fn smarty_function_build_solr_fq

?>
