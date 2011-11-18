<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

if (empty($options)) {
    register_shutdown_function('ns_cli_shutdown');
}
else {
    if (!in_array('--keep-session', $options, true)) {
        register_shutdown_function('ns_cli_shutdown');
    }
}

if (!defined('APPLICATION_PATH')) {
    require_once __DIR__ . '/../application.php';
    $application->bootstrap();
}

// check if script is included
if (!empty($GLOBALS['g_campsiteDir'])) {
    $CAMPSITE_DIR = $GLOBALS['g_campsiteDir'];
    if (!defined('WWW_DIR')) {
        define('WWW_DIR', $GLOBALS['g_campsiteDir']);
    }
    return;
}

// set
set_document_root();


/**
 * Set document root
 * @return void
 */
function set_document_root()
{
    global $CAMPSITE_DIR;

    // get document root via -d switch
    $document_root = dirname(dirname(__FILE__));
    for ($i = 1; $i < $_SERVER['argc'] - 1; $i++) {
        if ($_SERVER['argv'][$i] == '-d') {
            $document_root = $_SERVER['argv'][$i + 1];
            break;
        }
    }

    // check if document_root exists
    if (!is_dir($document_root)) {
        $file = basename($_SERVER['argv'][0]);
        echo "$file error: Directory '$document_root' does not exist.\n";
        echo "Please provide valid Newscoop document_root path via: $file -d path\n";
        exit(1);
    }

    // set global variables, constants
    $GLOBALS['g_campsiteDir'] = $CAMPSITE_DIR = realpath($document_root);
    if (!defined('WWW_DIR')) {
        define('WWW_DIR', $GLOBALS['g_campsiteDir']);
    }
}

function ns_cli_shutdown()
{
    $sessionId = session_id();
    if (!empty($sessionId)) {
        session_destroy();
    }
}

