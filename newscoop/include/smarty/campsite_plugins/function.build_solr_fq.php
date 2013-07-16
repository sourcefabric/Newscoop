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
 *           builds the filter query for Solr
 *
 * @param array $p_params
 *        fqtype        - type of article to search
 *        fqpublished   - 24h, 7d, 1m, 1y
 *        fqfrom        - custom starting date
 *        fqto          - custom ending date
 *        fqdateformat  - custom date format, defaults to ISO (Y-m-d) if left empty
 *
 * @return string $solrFq
 *      The Solr FQ requested
 *
 * @example
 *  {{ list_search_results_solr fq="{{ build_solr_fq }}" }}
 *  {{ list_search_results_solr fq="{{ build_solr_fq fqtype=$smarty.post.type }}" }}
 *
 */
function smarty_function_build_solr_fq($p_params = array(), &$p_smarty)
{
    $solrFq = '';

    // The $p_params override the $_GET
    $acceptedParams = array('fqtype', 'fqpublished', 'fqfrom', 'fqto', 'fqdateformat');
    $cleanParam = array();

    foreach ($acceptedParams as $key) {
        if (array_key_exists($key, $p_params) && !empty($p_params[$key])) {
            $cleanParam[$key] = $p_params[$key];
        } else if (array_key_exists($key, $_GET) && !empty($_GET[$key])) {
            $cleanParam[$key] = $_GET[$key];
        }
    }

    if (array_key_exists('fqpublished', $cleanParam) && !empty($cleanParam['fqpublished'])) {
        $published = '';

        switch ($cleanParam['fqpublished']) {
            case '24h':
                $published = '[NOW-1DAY/HOUR TO *]';
                break;
            case '7d':
                $published = '[NOW-7DAY/DAY TO *]';
                break;
            case '1m':
                $published = '[NOW-1MONTH/DAY TO *]';
                break;
            case '1y':
                $published = '[NOW-1YEAR/DAY TO *]';
                break;
            default:
                $published = '';
                break;
        }
    }

    if (!array_key_exists('fqdateformat', $cleanParam) || empty($cleanParam['fqdateformat'])) {
        $cleanParam['fqdateformat'] = 'Y-m-d';
    }

    if (array_key_exists('fqtype', $cleanParam) && !empty($cleanParam['fqtype'])) {
        $solrFq .= 'type:'.$cleanParam['fqtype'];
    }

    if (array_key_exists('fqfrom', $cleanParam) && !empty($cleanParam['fqfrom'])) {
        $fromDate = date_create_from_format($cleanParam['fqdateformat'], $cleanParam['fqfrom']);
        var_dump($fromDate);
        $solrFromDate = date_format($fromDate, 'Y-m-d').'T00:00:00Z/DAY';
    }

    if (array_key_exists('fqto', $cleanParam) && !empty($cleanParam['fqto'])) {
        $toDate = date_create_from_format($cleanParam['fqdateformat'], $cleanParam['fqto']);
        $solrToDate = date_format($toDate, 'Y-m-d').'T00:00:00Z/DAY';
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
