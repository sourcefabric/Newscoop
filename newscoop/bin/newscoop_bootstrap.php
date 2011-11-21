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
    // Define path to application directory
    defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

    // Define application environment
    defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

    // Ensure library/ is on include_path
    set_include_path(implode(PATH_SEPARATOR, array(
        realpath(APPLICATION_PATH . '/../library'),
        realpath(APPLICATION_PATH . '/../include'),
        get_include_path(),
    )));

    if (!is_file('Zend/Application.php')) {
	    // include libzend if we dont have zend_application
	    set_include_path(implode(PATH_SEPARATOR, array(
		    '/usr/share/php/libzend-framework-php',
		    get_include_path(),
	    )));
    }

    /** Zend_Application */
    require_once 'Zend/Application.php';

    // Create application, bootstrap, and run
    $application = new Zend_Application(
        APPLICATION_ENV,
        APPLICATION_PATH . '/configs/application.ini'
    );

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

