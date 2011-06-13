<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * @Acl(ignore="1")
 */
class Admin_LegacyController extends Zend_Controller_Action
{
    public function indexAction()
    {
        global $controller, $Campsite, $ADMIN_DIR, $ADMIN, $g_user, $g_ado_db, $prefix;
        $controller = $this;

        $no_menu_scripts = array(
            $prefix . 'login.php',
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
            '/articles/empty.php',
            '/articles/post.php',
            '/comments/ban.php',
            '/comments/do_ban.php',
            '/imagearchive/do_add.php',
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

        // detect extended login/logout files
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

            $this->view->legacy = true;
            $needs_menu = ! (in_array($call_script, $no_menu_scripts) || Input::Get('p_no_menu', 'boolean', false, true));
            if (!$needs_menu) {
                $this->_helper->layout->disableLayout();
            }

            // Verify the file exists
            $path_name = $Campsite['HTML_DIR'] . "/$ADMIN_DIR/$call_script";

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

            // render view
            require_once $path_name;
            return;

        } elseif (file_exists($Campsite['HTML_DIR'] . "/$ADMIN_DIR/$call_script")) {
            readfile($Campsite['HTML_DIR'] . "/$ADMIN_DIR/$call_script");
            exit;
        }

        header("HTTP/1.1 404 Not found");
        exit;
    }

    public function postDispatch()
    {
        camp_html_clear_msgs(true);
    }
}
