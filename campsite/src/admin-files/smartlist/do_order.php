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

require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/articles/article_common.php");

if (!SecurityToken::isValid()) {
    echo json_encode(array(
        'success' => false,
        'message' => getGS('Invalid security token!'),
    ));
    exit;
}

// get input
$f_language = Input::Get('language', 'int', null, true);
$f_order = Input::Get('order', 'array', array(), true);
if (!Input::IsValid()) {
    echo json_encode(array(
        'success' => false,
        'message' => getGS('Invalid input.'),
    ));
    exit;
}

$success = FALSE;
$message = 'non';
$article = new Article();
foreach ($f_order as $order => $item) {
    list($prefix, $articleId) = explode('_', $item);
    $article->Article($f_language, $articleId);
    $article->setProperty('ArticleOrder', $order + 1);
}

$success = TRUE;
$message = getGS('Articles order updated.');
echo json_encode(array(
    'success' => $success,
    'message' => is_array($message) ? implode("\n", $message) : $message,
));
exit;
