<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */

/**
 * Includes
 *
 * We indirectly reference the DOCUMENT_ROOT so we can enable
 * scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
 * is not defined in these cases.
 */
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/db_connect.php');
require_once($g_documentRoot.'/template_engine/classes/CampRequest.php');

/**
 * Class CampGetImage
 */
class CampGetImage
{
    /**
     * Class constructor.
     *
     * @param integer $p_imageNr
     *      The image number within the article
     * @param integer $p_articleNr
     *      The article number
     */
    public function __construct($p_imageNr, $p_articleNr)
    {
        global $g_ado_db;

        if (empty($p_articleNr) || empty($p_imageNr)
        || !is_numeric($p_articleNr) || !is_numeric($p_imageNr)) {
            self::ExitError('Invalid parameters');
        }

        $query = 'SELECT IdImage FROM ArticleImages '
            . "WHERE NrArticle = '" . $g_ado_db->Escape($p_articleNr)."'"
            . " AND Number = '".$g_ado_db->Escape($p_imageNr)."'";
        $idImage = $g_ado_db->GetOne($query);

        if (empty($idImage)) {
            self::ExitError('Image ' . $p_imageNr
                           .' not found for article ' . $p_articleNr);
        }

        $query = 'SELECT ImageFileName, URL, ContentType FROM Images '
            . "WHERE Id = '".$g_ado_db->Escape($idImage)."'";
        $imageMetaData = $g_ado_db->GetRow($query);

        if (empty($imageMetaData)) {
            self::ExitError('Image with id ' . $idImage . ' not found');
        }

        if (!empty($imageMetaData['URL'])) {
            self::ReadFileFromURL($imageMetaData);
        } else {
            $filePath = $_SERVER['DOCUMENT_ROOT'].'/images/'.$imageMetaData['ImageFileName'];
            if (!file_exists($filePath)) {
                self::ExitError('Image file ' . $filePath . ' does not exist');
            }

            self::PushFile($filePath, $imageMetaData['ContentType']);
        }

    } // fn __construct


    /**
     * Reads an image file from given URL and displays it.
     *
     * @param array $p_imageMetaData
     */
    private static function ReadFileFromURL($p_imageMetaData)
    {
        $fp = @fopen($p_imageMetaData['URL'], 'r');
        if ($fp == false) {
            self::ExitError('Error reading ' . $p_imageMetaData['URL']);
        }

        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Content-type: ' . $p_imageMetaData['ContentType']);
        readfile($p_imageMetaData['URL']);

        /*
         * this is the version using curl library functions.
         * we keep this out as it is not built-in PHP feature, but it
         * can be worth using this option as it is better way to do this.
         *
         * $curlHandler = curl_init();
         * if ($curlHandler) {
         *   curl_setopt($curlHandler, CURLOPT_URL, $p_imageMetaData['URL']);
         *   header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
         *   header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
         *   header('Cache-Control: no-store, no-cache, must-revalidate');
         *   header('Cache-Control: post-check=0, pre-check=0', false);
         *   header('Pragma: no-cache');
         *   header('Content-type: ' . $p_imageMetaData['ContentType']);
         *   curl_exec ($curlHandler);
         *   curl_close ($curlHandler);
         * }
         */
    } // fn ReadFileFromURL


    /**
     * Reads an image file from local server and displays it.
     *
     * @param string $p_filePath
     *      The full path to the image file
     * @param string $p_contentType
     *      The mime content type for the image file
     */
    private static function PushFile($p_filePath, $p_contentType)
    {
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Content-length: ' . filesize($p_filePath));
        header('Content-type: ' . $p_contentType);
        readfile($p_filePath);
    } // fn PushFile


    /**
     * Writes the given error message and exit.
     *
     * @param string $p_errorMessage
     */
    private static function ExitError($p_errorMessage)
    {
        header('Content-type: text/html; charset=utf-8');
        print($p_errorMessage);
        exit;
    } // fn ExitError

} // class CampGetImage

?>