<?php
/**
 * This file gets called before any file in the "admin-files" directory is executed.
 * Think of it as a wrapper for all admin interface scripts.
 * Here you can set up anything that should be applied globally to all scripts.
 */

define('WWW_DIR', dirname(__FILE__));
define('LIBS_DIR', WWW_DIR . '/admin-files/libs');

$GLOBALS['g_campsiteDir'] = WWW_DIR;

header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Content-Type: text/html; charset=UTF-8");

require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'include'
    .DIRECTORY_SEPARATOR.'campsite_constants.php');
require_once(CS_PATH_CONFIG.DIR_SEP.'install_conf.php');

// goes to install process if configuration files does not exist yet
if (!file_exists(CS_PATH_CONFIG.DIR_SEP.'configuration.php')
        || !file_exists(CS_PATH_CONFIG.DIR_SEP.'database_conf.php')) {
    header('Location: '.$Campsite['SUBDIR'].'/install/');
    exit(0);
}

// start zend session first to prevent exception
require_once 'Zend/Session.php';
Zend_Session::start();

require_once(CS_PATH_SITE.DIR_SEP.'include'.DIR_SEP.'campsite_init.php');
require_once(CS_PATH_CONFIG.DIR_SEP.'liveuser_configuration.php');
require_once(CS_PATH_SITE.DIR_SEP.'classes'.DIR_SEP.'CampTemplateCache.php');

if (file_exists(CS_PATH_SITE.DIR_SEP.'upgrade.php')) {
    camp_display_message("Site is down for upgrade. Please initiate upgrade process.");
    echo '<META HTTP-EQUIV="Refresh" content="10">';
    exit(0);
}

// detect extended login/logout files
$prefix = file_exists(CS_PATH_SITE.DIR_SEP.'admin-files'.DIR_SEP.'ext_login.php') ? '/ext_' : '/';

global $ADMIN_DIR;
global $ADMIN;
global $g_user;
require_once(CS_PATH_SITE.DIR_SEP.$ADMIN_DIR.DIR_SEP.'camp_html.php');
require_once(CS_PATH_CLASSES.DIR_SEP.'SecurityToken.php');

// load if possible before setting camp_report_bug error handler
// to prevent error messages
include_once 'HTML/QuickForm.php';
include_once 'HTML/QuickForm/RuleRegistry.php';
include_once 'HTML/QuickForm/group.php';

camp_set_error_handler("camp_report_bug");

camp_load_translation_strings("api");
$plugins = CampPlugin::GetEnabled(true);
foreach ($plugins as $plugin) {
    camp_load_translation_strings("plugin_".$plugin->getName());
}

// Load common translation strings
camp_load_translation_strings('globals');

require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/init_content.php");

// run zend
require_once dirname(__FILE__) . '/public/index.php';

$no_menu_scripts = array(
    $prefix . 'login.php',
    $prefix . 'do_login.php',
    $prefix . 'logout.php',
    '/issues/preview.php',
    '/issues/empty.php',
    '/ad_popup.php',
    '/articles/preview.php',
    '/articles/autopublish.php',
    '/articles/autopublish_do_add.php',
    '/articles/images/popup.php',
    '/articles/images/view.php',
    '/articles/topics/popup.php',
    '/articles/files/popup.php',
    '/articles/audioclips/campcaster_login.php',
    '/articles/audioclips/popup.php',
    '/articles/audioclips/edit.php',
    '/articles/empty.php',
    '/articles/post.php',
    '/comments/ban.php',
    '/comments/do_ban.php',
    '/imagearchive/do_add.php',
    '/smartlist/assets/php/dynamicfilter/data.php',
    '/smartlist/assets/load_filterby_menu.php',
    '/smartlist/assets/dt_actions.php',
    '/users/authors_ajax/detail.php',
    '/users/authors_ajax/grid.php',
    $prefix . 'password_recovery.php',
    $prefix . 'password_check_token.php',
    '/articles/locations/popup.php',
    '/articles/locations/preview.php',
    '/articles/locations/search.php',
    );

CampPlugin::ExtendNoMenuScripts($no_menu_scripts);

$request_uri = $_SERVER['REQUEST_URI'];
$call_script = substr($request_uri, strlen("/$ADMIN"));

// Remove any GET parameters
if (($question_mark = strpos($call_script, '?')) !== false) {
    $call_script = substr($call_script, 0, $question_mark);
}

// Remove all attempts to get at other parts of the file system
$call_script = str_replace('/../', '/', $call_script);
if ($call_script == '/logout.php') $call_script = $prefix . 'logout.php';

$extension = '';
if (($extension_start = strrpos($call_script, '.')) !== false) {
    $extension = strtolower(substr($call_script, $extension_start));
}

if (($extension == '.php') || ($extension == '')) {
    // If its not a PHP file, assume its a directory.
    if ($extension != '.php') {
        // If its a directory
        if (($call_script != '') && ($call_script[strlen($call_script)-1] != '/') ) {
            $call_script .= '/';
        }
        $call_script .= 'index.php';
    }
    $needs_menu = ! (in_array($call_script, $no_menu_scripts) || Input::Get('p_no_menu', 'boolean', false, true));

    // Verify the file exists
    $path_name = $Campsite['HTML_DIR'] . "/$ADMIN_DIR/$call_script";

    // Clean up the global namespace before we call the script
    unset($access);
    unset($extension);
    unset($extension_start);
    unset($question_mark);
    unset($no_menu_scripts);
    unset($request_uri);

    if (file_exists($Campsite['HTML_DIR'] . '/reset_cache')) {
        CampCache::singleton()->clear('user');
        unlink($GLOBALS['g_campsiteDir'] . '/reset_cache');
    }

    if (!file_exists($path_name)) {

        foreach (CampPlugin::GetEnabled() as $CampPlugin) {
            $plugin_path_name = dirname(APPLICATION_PATH) . '/'.$CampPlugin->getBasePath()."/$ADMIN_DIR/$call_script";
            if (file_exists($plugin_path_name)) {
                $path_name = $plugin_path_name;

                // possible plugin include paths
                $include_paths = array(
                    '/classes',
                    '/template_engine/classes',
                    '/template_engine/metaclasses',
                );

                // set include paths for plugin
                foreach ($include_paths as $path) {
                    $path = dirname(APPLICATION_PATH) . '/' . $CampPlugin->getBasePath() . $path;
                    if (file_exists($path)) {
                        set_include_path(implode(PATH_SEPARATOR, array(
                            realpath($path),
                            get_include_path(),
                        )));
                    }
                }

               break;
           }
        }

        if (!file_exists($path_name)) {
            header("HTTP/1.1 404 Not found");
            echo '<html><head><title>404 Not Found</title></head><body>';
            echo '<h1>Not Found</h1>';
            echo '<p>The requested URL ', $_SERVER['REQUEST_URI'], ' was not found on this server.</p>';
            echo '</body></html>';
            exit;
        }
    }

    // Get the main content
    ob_start();
    require_once($path_name);
    $content = ob_get_clean();

    // We create the top menu AFTER the main content because
    // of the localizer screen.  It will update the strings, which
    // need to be reflected immediately in the menu.
    $_top_menu = '';
    if ($needs_menu) {
        ob_start();
        require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/menu.php");
        $_top_menu = ob_get_clean();
    }

    $content =  $_top_menu . $content;

    //if ($needs_menu) {
        //$content .= "</td></tr>\n</table>\n";
    //}
    //$content .= "</html>\n";
    echo $content;

    camp_html_clear_msgs(true);
} elseif (file_exists($Campsite['HTML_DIR'] . "/$ADMIN_DIR/$call_script")) {
    readfile($Campsite['HTML_DIR'] . "/$ADMIN_DIR/$call_script");
} else {
    header("HTTP/1.1 404 Not found");
    exit;
}

// run internal cron scheduler
if (SystemPref::Get('ExternalCronManagement') == 'N') {
    flush();
    camp_cron();
}

/**
 * Sets a user-defined error function.
 *
 *  The function set_error_handler() works differently in PHP 4 & 5.
 *  This function is a wrapper interface, to both versions of
 *  set_error_handler.
 *
 * @param $p_function The function to execute on error
 * @return void
 */
function camp_set_error_handler($p_function)
{
    // --- In PHP 5, the error handler-default is set at E_STRICT,
    //     which captures all legacy based errors.  Unfortunately, this is
    //     completely incompatible with PHP 4. ---
    if ( version_compare( phpversion(), "5.0.0", ">=" ) ) {
        set_error_handler($p_function, E_ALL);

    } else {
        // -- Meanwhile, the error-handler flag argument is not
        //    available in PHP 4, which always assumes it's value to be
        //    E_ALL --
        set_error_handler($p_function);
    }
} // fn camp_set_error_handler


/**
 * Called for all Campsite errors.
 */
function camp_report_bug($p_number, $p_string, $p_file, $p_line)
{
    global $ADMIN_DIR, $Campsite;
    require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/bugreporter/bug_handler_main.php");
    camp_bug_handler_main($p_number, $p_string, $p_file, $p_line);
} // fn camp_report_bug

?>
