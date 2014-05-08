<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Legacy controller
 */
class LegacyController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout->disableLayout();
    }

    public function preDispatch()
    {
        if ($this->getRequest()->getParam('logout') === 'true') {
            $this->_forward('logout', 'auth', 'default', array(
                'url' => $this->getRequest()->getPathInfo(),
            ));
        }
    }

    public function indexAction()
    {
        global $controller;
        $controller = $this;
	
        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'include'
            .DIRECTORY_SEPARATOR.'campsite_constants.php');
        require_once(CS_PATH_CONFIG.DIR_SEP.'install_conf.php');

        $local_path = dirname(__FILE__) . '/include';
        set_include_path($local_path . PATH_SEPARATOR . get_include_path());

        require_once(CS_PATH_INCLUDES.DIR_SEP.'campsite_init.php');

        if (file_exists(CS_PATH_SITE . DIR_SEP . 'reset_cache')) {
            CampCache::singleton()->clear('user');
            @unlink(CS_PATH_SITE . DIR_SEP . 'reset_cache');
        }

        // initializes the campsite object
        $campsite = new CampSite();

        // loads site configuration settings
        $campsite->loadConfiguration(CS_PATH_CONFIG.DIR_SEP.'configuration.php');

        // starts the session
        $campsite->initSession();

        if (file_exists(CS_PATH_SITE.DIR_SEP.'conf'.DIR_SEP.'upgrading.php')) {
            $this->upgrade();
            exit(0);
        }

        // initiates the context
        $campsite->init();

        // dispatches campsite
        $campsite->dispatch();

        // triggers an event before render the page.
        // looks for preview language if any.
        $previewLang = $campsite->event('beforeRender');
        if (!is_null($previewLang)) {
            require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/SyntaxError.php');
            set_error_handler('templateErrorHandler');

        } else {
	        set_error_handler(create_function('', 'return true;'));
        }

        // renders the site
        $campsite->render();

        // triggers an event after displaying
        $campsite->event('afterRender');
    }

    public function postDispatch()
    {}

    private function upgrade()
    {
        header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");

        $year = _(date("Y"));
        $message = <<<EOF
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Newscoop - upgrade message</title>

    <style type="text/css">
        body {
            background-color:#fafafa;
            font: 12px/20px Arial, Helvetica, sans-serif;
            color: #696969;
            padding-top: 70px;
        }
        .pageWrapper {
            width:460px;
            margin:0 auto;
            padding: 15px;
            overflow: hidden;
        }
        .content {
            background: #f8f8f8;
            background: -webkit-linear-gradient(#f8f8f8 0%, #f2f2f2 100%);
            background: -moz-linear-gradient(#f8f8f8 0%, #f2f2f2 100%);
            background: -o-linear-gradient(#f8f8f8 0%, #f2f2f2 100%);
            background: linear-gradient(#f8f8f8 0%, #f2f2f2 100%);
            border: 1px solid #e4e4e4;
            padding: 16px;
        }
        .footer {
            clear:both;
            padding: 5px 0 0;
            text-align: center;
            margin-top: 4px;
            font-size: 11px;
            color: #666;
            line-height: 16px;
        }
        .logo {
            text-align: center;
            margin: 10px 0 30px 0;
        }
        a { color: #007fb3; text-decoration:none; }
        a:hover { text-decoration:underline; }
        h2 { 
            background: #f6f6f6;
            background: -webkit-linear-gradient(#fafafa 0%, #f6f6f6 100%);
            background: -moz-linear-gradient(#fafafa 0%, #f6f6f6 100%);
            background: -o-linear-gradient(#fafafa 0%, #f6f6f6 100%);
            background: linear-gradient(#fafafa 0%, #f6f6f6 100%);
            border:1px solid #afafaf;
            border-top-color:#FFF;
            border-left-color:#e5e5e5;
            border-right-color:#e5e5e5;
            margin:0 0 10px 0;
            padding:8px 10px;
            font-size:15px;
            color:#444;
            text-align:left;
            -moz-box-shadow: 0 2px 2px rgba(0,0,0,.10);
            -webkit-box-shadow: 0 2px 2px rgba(0,0,0,.10);
            box-shadow: 0 2px 2px rgba(0,0,0,.10); 
        }
        p {
            margin: 0 0 10px 0;
        }
    </style>
</head>
<body>  
<div class="pageWrapper">
    <div class="content">
        <div class="logo"><img src="data:image/gif;base64,R0lGODlh6gBEAMQQAAB/sz2dxHq61LjX5Obu8Q+Gt5nJ3B+Ou9bm7cff6S6Vv1yrzKjQ4E2kyInB2Gyz0P///wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C1hNUCBEYXRhWE1QPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS4wLWMwNjAgNjEuMTM0Nzc3LCAyMDEwLzAyLzEyLTE3OjMyOjAwICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1IFdpbmRvd3MiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MTA1ODM0QzIxRkRDMTFFMEIzRjE5NEE2RUM3QkJEN0EiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MTA1ODM0QzMxRkRDMTFFMEIzRjE5NEE2RUM3QkJEN0EiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDoxMDU4MzRDMDFGREMxMUUwQjNGMTk0QTZFQzdCQkQ3QSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDoxMDU4MzRDMTFGREMxMUUwQjNGMTk0QTZFQzdCQkQ3QSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/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" border="0" alt="Newscoop logo">
        </div>
        <h2>The website you are trying to view is currently down for maintenance. Normal service will resume shortly.</h2>
    </div>
    <div class="footer">
        <a href="http://newscoop.sourcefabric.org/" target="_blank">
            Newscoop</a>, the open content management system for professional journalists.
         <br>
            ©&nbsp;$year&nbsp;<a href="http://www.sourcefabric.org" target="_blank">Sourcefabric z.ú.</a>&nbsp;Newscoop       is distributed under GNU GPL v.3    
    </div>
</div>
</body>
</html>
EOF;

        camp_display_message($message);
        echo '<META HTTP-EQUIV="Refresh" content="10">';
    }
}
