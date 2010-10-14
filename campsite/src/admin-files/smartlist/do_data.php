<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

header('Content-type: application/json');
require_once dirname(__FILE__) . '/Smartlist.php';
require_once($GLOBALS['g_campsiteDir']. "/classes/Article.php");

// start >= 0
$start = max(0,
    empty($_REQUEST['iDisplayStart']) ? 0 : (int) $_REQUEST['iDisplayStart']);

// results num >= 10 && <= 100
$limit = min(100, max(10,
    empty($_REQUEST['iDisplayLength']) ? 0 : (int) $_REQUEST['iDisplayLength']));

// filters - common
$articlesParams = array();
$filters = array(
    'publication' => array('is', 'integer'),
    'issue' => array('is', 'integer'),
    'section' => array('is', 'integer'),
    'language' => array('is', 'integer'),
    'publish_date' => array('is', 'date'),
    'publish_date_from' => array('greater_equal', 'date'),
    'publish_date_to' => array('smaller_equal', 'date'),
    'author' => array('is', 'integer'),
    'topic' => array('is', 'integer'),
    'workflow_status' => array('is', 'string'),
    'creator' => array('is', 'integer'),
);

// mapping form name => db name
$fields = array(
    'publish_date_from' => 'publish_date',
    'publish_date_to' => 'publish_date',
    'language' => 'idlanguage',
    'creator' => 'iduser',
);

foreach ($filters as $name => $opts) {
    if (!empty($_REQUEST[$name])) {
        $field = !empty($fields[$name]) ? $fields[$name] : $name;
        $articlesParams[] = new ComparisonOperation($field, new Operator($opts[0], $opts[1]), $_REQUEST[$name]);
    }
}

// search
if (isset($_REQUEST['sSearch']) && strlen($_REQUEST['sSearch']) > 0) {
    $search_phrase = $_REQUEST['sSearch'];
    $articlesParams[] = new ComparisonOperation('search_phrase', new Operator('is', 'integer'), $search_phrase);
}

// sorting
$sortOptions = array(
    0 => 'bynumber',
    2 => 'bysectionorder',
    3 => 'byname',
    12 => 'bycomments',
    13 => 'bypopularity',
    15 => 'bypublishdate',
    16 => 'bycreationdate',
);

$sortBy = 'bysectionorder';
$sortDir = 'asc';
$sortingCols = min(1, (int) $_REQUEST['iSortingCols']);
for ($i = 0; $i < $sortingCols; $i++) {
    $sortOptionsKey = (int) $_REQUEST['iSortCol_' . $i];
    if (!empty($sortOptions[$sortOptionsKey])) {
        $sortBy = $sortOptions[$sortOptionsKey];
        $sortDir = $_REQUEST['sSortDir_' . $i];
        break;
    }
}

// get articles
$articles = Article::GetList($articlesParams, array(array('field' => $sortBy, 'dir' => $sortDir)), $start, $limit, $articlesCount, true);

$return = array();
foreach($articles as $article) {
    $return[] = Smartlist::ProcessArticle($article);
}

echo(json_encode(array(
    'iTotalRecords' => Article::GetTotalCount(),
    'iTotalDisplayRecords' => $articlesCount,
    'sEcho' => $_GET['sEcho'],
    'aaData' => $return,
)));
exit;
