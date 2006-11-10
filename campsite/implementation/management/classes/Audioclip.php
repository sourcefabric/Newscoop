<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
// is not defined in these cases.
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/db_connect.php');
require_once($g_documentRoot.'/classes/XR_CcClient.php');
require_once($g_documentRoot.'/classes/Log.php');
require_once($g_documentRoot.'/classes/Article.php');
require_once('HTTP/Client.php');

/**
 * @package Campsite
 */
class Audioclip {

    /*
     *
     */
    function Audioclip()
    {

    } // constructor

} // class Image

?>