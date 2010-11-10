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

// include valid callbacks
// TODO replace with autoloading
require_once dirname(__FILE__) . '/libs/ArticleList/ArticleList.php';


// TODO check rights

// parse callback
$callback = $_REQUEST['callback'];
$args = (array) $_REQUEST['args'];

try {
    if (!SecurityToken::isValid()) {
        throw new Exception(getGS('Invalid security token.'));
    }

    $result = call_user_func_array($callback, $args);
    if ($result === FALSE) {
        throw new Exception('Unknown');
    }

    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(array(
        'error' => TRUE,
        'message' => getGS('Error:') . ' ' . $e->getMessage(),
    ));
}

exit;
