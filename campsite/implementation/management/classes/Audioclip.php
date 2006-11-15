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
    var $m_metaData = array();
    var $m_fileTypes = array('mp3','ogg');


    /**
     * 
     */
    function Audioclip($p_gunId = null)
    {
        global $mdata;

        $this->xrc =& XR_CcClient::factory($mdata);
        if (!is_null($p_gunId)) {
            $sessid = $_SESSION['cc_sessid'];
            if ($this->xrc->xr_existsAudioClip($sessid, $p_gunId)) {
                $this->m_metaData = $this->xr_getAudioClip($sessid, $p_gunId);
            }
        }
    } // constructor

    /**
     * Store the Audioclip into the Campcaster storage server.
     *
     * @param string $p_fileName the name of the audioclip
     * @param array $p_xrParams params to send to the XML RPC method
     *
     * @return string the gunid
     */
    function storeAudioclip($p_fileName, $p_xrParams)
    {
        global $Campsite;

        $fullPathToAudioFile = $Campsite['TMP_DIRECTORY'] . $p_fileName;
        if (file_exists($fullPathToAudioFile) == false) {
            return false; // PEAR Error
        }

        $sessid = $_SESSION['cc_sessid'];
        $r = $this->xrc->xr_storeAudioClipOpen($sessid, $p_xrParams['gunid'], $p_xrParams['mdata'], $p_xrParams['fname'], $p_xrParams['chsum']);
        if (empty($r['url']) || empty($r['token'])) {
            return false; // PEAR Error
        } else {
            exec(trim('curl -T ' . escapeshellarg($fullPathToAudioFile)
                      . ' ' . $r['url']));
        }
        return $this->xrc->xr_storeAudioClipClose($sessid, $r['token']);
    } // fn storeAudioclip

    /**
     * 
     */
    function storeMetadata()
    {
        // TO BE DONE
    } // fn storeMetadata

    /**
     * This function should be called when an audioclip is uploaded.
     * It will save the audioclip file to the temporary directory on
     * the disk before to be sent to the Campcaster storage server.
     *
     * @param array $p_fileVar the audioclip file submited
     *
     * @return mixed TRUE on success, PEAR Error on failure
     */
    function OnFileUpload($p_fileVar)
    {
        global $Campsite;

        if (!is_array($p_fileVar)) {
			return null;
		}

        // Verify its a valid file.
		$filesize = filesize($p_fileVar['tmp_name']);
		if ($filesize === false) {
			return new PEAR_Error("Audioclip::OnFileUpload(): invalid parameters received.");
		}
        if ($this->isValidFileType($p_fileVar['name']) == FALSE) {
            return new PEAR_Error("Audioclip::OnFileUpload(): invalid file type.");
        }
        $target = $Campsite['TMP_DIRECTORY'] . $p_fileVar['name'];
        if (!move_uploaded_file($p_fileVar['tmp_name'], $target)) {
            return new PEAR_Error(camp_get_error_message(CAMP_ERROR_CREATE_FILE, $target), CAMP_ERROR_CREATE_FILE);
        }
        chmod($target, 0644);
        return TRUE;
    } // fn OnFileUpload

    /**
     * Validate an audioclip file by its extension.
     *
     * @param $p_fileName the name of the audioclip file
     *
     * @return bool TRUE on success, FALSE on failure
     */
    function isValidFileType($p_fileName)
    {
        foreach ($this->m_fileTypes as $t) {
            if (preg_match('/'.str_replace('/', '\/', $t).'$/i', $p_fileName))
                return TRUE;
        }
        return FALSE;
    } // fn isValidFileType

} // class Audioclip

?>