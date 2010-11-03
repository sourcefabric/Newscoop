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

require_once WWW_DIR . '/classes/User.php';

// permission action mapping
// callback => required permissions
// callback is Class::method or function
$rules = array(
    'ArticleList::doAction' => '', // checked per action
    'ArticleList::doData' => '', // everyone can see articles
    'ArticleList::doOrder' => 'Publish',
);

// parse callback
$callback = $_REQUEST['callback'];
$args = (array) $_REQUEST['args'];

try {
    // check token
    if (!SecurityToken::isValid()) {
        throw new Exception(getGS('Invalid security token.'));
    }

    // check permissions
    if (is_array($callback)) {
        $action = implode('::', $callback);
    } else {
        $action = (string) $callback;
    }
    if (!isset($rules[$action])
        || (!empty($rules[$action])
            && !$g_user->hasPermission($rules[$action]))) {
        throw new Exception(getGS('Access denied.'));
    }

    // include valid callbacks files
    // TODO replace with autoloading
    require_once dirname(__FILE__) . '/libs/ArticleList/ArticleList.php';
    require_once $GLOBALS['g_campsiteDir'] . '/classes/Extension/WidgetManager.php';

    // call func
    $result = call_user_func_array($callback, $args);
    if ($result === FALSE) {
        throw new Exception('Unknown');
    }

    // return
    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(array(
        'error' => TRUE,
        'message' => getGS('Error:') . ' ' . $e->getMessage(),
    ));
}

exit;
