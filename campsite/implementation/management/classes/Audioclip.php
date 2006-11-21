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
require_once($g_documentRoot.'/classes/AudioclipLocalMetadata.php');
require_once('HTTP/Client.php');


$mask = array(
    'pages' => array(
        'Main'  => array(
            array(
                'element'   => 'dc:title',
                'type'      => 'text',
                'label'     => 'Title',
                'required'  => TRUE,
            ),
            array(
                'element'   => 'dc:creator',
                'type'      => 'text',
                'label'     => 'Creator',
                'required'  => TRUE,
            ),
            array(
                'element'   => 'dc:type',
                'type'      => 'text',
                'label'     => 'Genre',
                'required'  => TRUE,
            ),
            array(
                'element'   => 'dc:format',
                'type'      => 'select',
                'label'     => 'File format',
                'required'  => TRUE,
                'options'   => array(
                                'File'          => 'Audioclip',
                                'live stream'   => 'Webstream'
                               ),
                'attributes'=> array('disabled' => 'on'),
            ),
            array(
                'element'   => 'dcterms:extent',
                'type'      => 'text',
                'label'     => 'Length',
                'attributes'=> array('disabled' => 'on'),
            ),
        ),
        'Music'  => array(
            array(
                'element'   => 'dc:title',
                'type'      => 'text',
                'label'     => 'Title',
            ),
            array(
                'element'   => 'dc:creator',
                'type'      => 'text',
                'label'     => 'Creator',
            ),
            array(
                'element'   => 'dc:source',
                'type'      => 'text',
                'label'     => 'Album',
                'id3'       => array('Album')
            ),
            array(
                'element'   => 'ls:year',
                'type'      => 'select',
                'label'     => 'Year',
                'options'   => '', //_getNumArr(1900, date('Y')+5),
            ),
            array(
                'element'   => 'dc:type',
                'type'      => 'text',
                'label'     => 'Genre',
            ),
            array(
                'element'   => 'dc:description',
                'type'      => 'textarea',
                'label'     => 'Description',
            ),
            array(
                'element'   => 'dc:format',
                'type'      => 'select',
                'label'     => 'Format',
                'options'   => array(
                                'File'          => 'Audioclip',
                                'live stream'   => 'Webtream'
                               ),
                'attributes'=> array('disabled' => 'on'),
            ),
            array(
                'element'   => 'ls:bpm',
                'type'      => 'text',
                'label'     => 'BPM',
                'rule'      => 'numeric',
            ),
            array(
                'element'   => 'ls:rating',
                'type'      => 'text',
                'label'     => 'Rating',
                'rule'      => 'numeric',
            ),
            array(
                'element'   => 'dcterms:extent',
                'type'      => 'text',
                'label'     => 'Length',
                'attributes'=> array('disabled' => 'on'),
            ),
            array(
                'element'   => 'ls:encoded_by',
                'type'      => 'text',
                'label'     => 'Encoded by',
            ),
            array(
                'element'   => 'ls:track_num',
                'type'      => 'select',
                'label'     => 'Track number',
                'options'   => '', //_getNumArr(0, 99),
            ),
            array(
                'element'   => 'ls:disc_num',
                'type'      => 'select',
                'label'     => 'Disc number',
                'options'   => '', //_getNumArr(0, 20),
            ),
            array(
                'element'   => 'ls:mood',
                'type'      => 'text',
                'label'     => 'Mood',
            ),
            array(
                'element'   => 'dc:publisher',
                'type'      => 'text',
                'label'     => 'Label',
            ),
            array(
                'element'   => 'ls:composer',
                'type'      => 'text',
                'label'     => 'Composer',
            ),
            array(
                'element'   => 'ls:bitrate',
                'type'      => 'text',
                'label'     => 'Bitrate',
                'rule'      => 'numeric',
            ),
            array(
                'element'   => 'ls:channels',
                'type'      => 'select',
                'label'     => 'Channels',
                'options'   => array(
                                ''  => '',
                                1   => 'Mono',
                                2   => 'Stereo',
                                6   => '5.1'
                               ),
            ),
            array(
                'element'   => 'ls:samplerate',
                'type'      => 'text',
                'label'     => 'Sample rate',
                'rule'      => 'numeric',
                'attributes'=> array('disabled' => 'on'),
            ),
            array(
                'element'   => 'ls:encoder',
                'type'      => 'text',
                'label'     => 'Encoder software used',
            ),
            array(
                'element'   => 'ls:crc',
                'type'      => 'text',
                'label'     => 'Checksum',
                'rule'      => 'numeric',
            ),
            array(
                'element'   => 'ls:lyrics',
                'type'      => 'textarea',
                'label'     => 'Lyrics',
            ),
            array(
                'element'   => 'ls:orchestra',
                'type'      => 'text',
                'label'     => 'Orchestra or band',
            ),
            array(
                'element'   => 'ls:conductor',
                'type'      => 'text',
                'label'     => 'Conductor',
            ),
            array(
                'element'   => 'ls:lyricist',
                'type'      => 'text',
                'label'     => 'Lyricist',
            ),
            array(
                'element'   => 'ls:originallyricist',
                'type'      => 'text',
                'label'     => 'Original lyricist',
            ),
            array(
                'element'   => 'ls:radiostationname',
                'type'      => 'text',
                'label'     => 'Radio station name',
            ),
            array(
                'element'   => 'ls:audiofileinfourl',
                'type'      => 'text',
                'label'     => 'Audio file information web page',
                'attributes'=> array('maxlength' => 256)
            ),
            array(
                'rule'      => 'regex',
                'element'   => 'ls:audiofileinfourl',
                'format'    => '', //UI_REGEX_URL,
                'rulemsg'   => 'Audio file information web page seems not to be valid URL'
            ),
            array(
                'element'   => 'ls:artisturl',
                'type'      => 'text',
                'label'     => 'Artist web page',
                'attributes'=> array('maxlength' => 256)
            ),
            array(
                'rule'      => 'regex',
                'element'   => 'ls:artisturl',
                'format'    => '', //UI_REGEX_URL,
                'rulemsg'   => 'Artist web page seems not to be valid URL'
            ),
            array(
                'element'   => 'ls:audiosourceurl',
                'type'      => 'text',
                'label'     => 'Audio source web page',
                'attributes'=> array('maxlength' => 256)
            ),
            array(
                'rule'      => 'regex',
                'element'   => 'ls:audiosourceurl',
                'format'    => '', //UI_REGEX_URL,
                'rulemsg'   => 'Audio source web page seems not to be valid URL'
            ),
            array(
                'element'   => 'ls:radiostationurl',
                'type'      => 'text',
                'label'     => 'Radio station web page',
                'attributes'=> array('maxlength' => 256)
            ),
            array(
                'rule'      => 'regex',
                'element'   => 'ls:radiostationurl',
                'format'    => '', //UI_REGEX_URL,
                'rulemsg'   => 'Radio station web page seems not to be valid URL'
            ),
            array(
                'element'   => 'ls:buycdurl',
                'type'      => 'text',
                'label'     => 'Buy CD web page',
                'attributes'=> array('maxlength' => 256)
            ),
            array(
                'rule'      => 'regex',
                'element'   => 'ls:buycdurl',
                'format'    => '', //UI_REGEX_URL,
                'rulemsg'   => 'Buy CD web page seems not to be valid URL'
            ),
            array(
                'element'   => 'ls:isrcnumber',
                'type'      => 'text',
                'label'     => 'ISRC number',
                'rule'      => 'numeric',
            ),
            array(
                'element'   => 'ls:catalognumber',
                'type'      => 'text',
                'label'     => 'Catalog number',
                'rule'      => 'numeric',
            ),
            array(
                'element'   => 'ls:originalartist',
                'type'      => 'text',
                'label'     => 'Original artist',
            ),
            array(
                'element'   => 'dc:rights',
                'type'      => 'text',
                'label'     => 'Copyright',
            ),
        ),
        'Voice'   => array(
            array(
                'element'   => 'dc:title',
                'type'      => 'text',
                'label'     => 'Title',
            ),
            array(
                'element'   => 'dcterms:temporal',
                'type'      => 'text',
                'label'     => 'Report date/time',
            ),
            array(
                'element'   => 'dcterms:spatial',
                'type'      => 'textarea',
                'label'     => 'Report location',
            ),
            array(
                'element'   => 'dcterms:entity',
                'type'      => 'textarea',
                'label'     => 'Report organizations',
            ),
            array(
                'element'   => 'dc:description',
                'type'      => 'textarea',
                'label'     => 'Description',
            ),
            array(
                'element'   => 'dc:creator',
                'type'      => 'text',
                'label'     => 'Creator',
            ),
            array(
                'element'   => 'dc:subject',
                'type'      => 'text',
                'label'     => 'Subject',
            ),
            array(
                'element'   => 'dc:type',
                'type'      => 'text',
                'label'     => 'Genre',
            ),
            array(
                'element'   => 'dc:format',
                'type'      => 'select',
                'label'     => 'Format',
                'options'   => array(
                                'File'          => 'Audioclip',
                                'live stream'   => 'Webstream'
                                ),
                'attributes'=> array('disabled' => 'on')
            ),
            array(
                'element'   => 'dc:contributor',
                'type'      => 'text',
                'label'     => 'Contributor',
            ),
            array(
                'element'   => 'dc:language',
                'type'      => 'text',
                'label'     => 'Language',
            ),
            array(
                'element'   => 'dc:rights',
                'type'      => 'text',
                'label'     => 'Copyright',
            ),
        )
    )
);


/**
 * @package Campsite
 */
class Audioclip {
    var $m_gunid = null;
    var $m_metaData = array();
    var $m_fileTypes = array('.mp3','.ogg','.wav');


    function Audioclip($p_gunid = null)
    {
        global $mdefs;

        $this->xrc =& XR_CcClient::factory($mdefs);
        if (!is_null($p_gunid)) {
            $sessid = $_SESSION['cc_sessid'];
            if ($this->xrc->xr_existsAudioClip($sessid, $p_gunid)) {
                $this->m_metaData = $this->xr_getAudioClip($sessid, $p_gunid);
                $this->m_gunid = $p_gunid;
            }
        }
    } // constructor


    function getAudioclipGunid()
    {
        return $this->m_gunid;
    } // fn getAudioclipGunid


    /**
     * Stores the Audioclip into the Campcaster storage server.
     *
     * @param string $p_fileName the name of the audioclip
     * @param array $p_xrParams params to send to the XML RPC method
     *
     * @return mixed Audioclip on success, PEAR Error on failure
     */
    function storeAudioclip($p_fileName, $p_xrParams)
    {
        if (file_exists($p_fileName) == false) {
            return new PEAR_Error(getGS('File $1 does not exist', $p_fileName));
        }

        $sessid = $_SESSION['cc_sessid'];
        $r = $this->xrc->xr_storeAudioClipOpen($sessid, $p_xrParams['gunid'], $p_xrParams['mdata'], $p_xrParams['fname'], $p_xrParams['chsum']);
        if (empty($r['url']) || empty($r['token'])) {
            return false; // PEAR Error
        } else {
            exec(trim('curl -T ' . escapeshellarg($p_fileName)
                      . ' ' . $r['url']));
        }
        $aData = $this->xrc->xr_storeAudioClipClose($sessid, $r['token']);
        return $aData['gunid'];
    } // fn storeAudioclip


    /**
     * @param string $p_file
     *
     * @return array
     */
    function analyzeFile($p_file)
    {
        require_once($_SERVER['DOCUMENT_ROOT'].'/include/getid3/getid3.php');

        $getid3Obj = new getID3;
        return $getid3Obj->analyze($p_file);
    } // fn analyzeFile


    /**
     * This function should be called when an audioclip is uploaded.
     * It will save the audioclip file to the temporary directory on
     * the disk before to be sent to the Campcaster storage server.
     *
     * @param array $p_fileVar the audioclip file submited
     *
     * @return mixed
     *         string full pathname to the file
     *         PEAR Error on failure
     */
    function OnFileUpload($p_fileVar)
    {
        global $Campsite;

        if (!is_array($p_fileVar)) {
			return null; // PEAR Error
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
        return $target;
    } // fn OnFileUpload


    /**
     * This function should be called when an audioclip has been
     * successfully sent to the Storage server. It deletes the
     * temporary audio file on Local.
     *
     * @param string $p_fileName
     */
    function OnFileStore($p_fileName)
    {
        if (file_exists($p_fileName)) {
            @unlink($p_fileName);
        }
    } // fn OnFileStore


    /**
     * Validates an audioclip file by its extension.
     *
     * @param $p_fileName the name of the audioclip file
     *
     * @return bool TRUE on success, FALSE on failure
     */
    function isValidFileType($p_fileName)
    {
        foreach ($this->m_fileTypes as $t) {
            if (preg_match('/'.str_replace('/', '\/', $t).'$/i', $p_fileName))
                return true;
        }
        return false;
    } // fn isValidFileType


    /**
     * Changes audioclip metadata on both storage and local servers.
     *
     * @param array $p_formData
     *
     * @return mixed TRUE on success, PEAR Error on failure
     */
    function editMetadata($p_formData)
    {
        global $mask;

        if (!is_array($p_formData)) {
            return new PEAR_Error(getGS('Invalid parameter given to Audioclip::editMetadata()'));
        }

        foreach($mask['pages'] as $key => $val) {
            foreach($mask['pages'][$key] as $k => $v) {
                $element_encode = str_replace(':','_',$v['element']);
                $p_formData['f_'.$key.'_'.$element_encode] ? $mData[$v['element']] = $p_formData['f_'.$key.'_'.$element_encode] : NULL;
            }
        }

        if (count($mData) < 1) return;

        if ($this->__editStorageServerMetadata($mData) == false) {
            return new PEAR_Error(getGS('Cannot update audioclip metadata on storage server'));
        }
        if ($this->__editLocalMetadata($mData) == false) {
            return new PEAR_Error(getGS('Cannot update audioclip metadata on Campsite'));
        }
        return true;
    } // fn editMetadata


    /**
     * Updates metadata on storage server.
     *
     * @param array $p_mData
     *
     * @return bool TRUE on success, FALSE on failure
     */
    function __editStorageServerMetadata($p_mData)
    {
        $xmlStr = '<?xml version="1.0" encoding="utf-8"?>
        <audioClip>
            <metadata
                xmlns="http://mdlf.org/campcaster/elements/1.0/"
                xmlns:ls="http://mdlf.org/campcaster/elements/1.0/"
                xmlns:dc="http://purl.org/dc/elements/1.1/"
                xmlns:dcterms="http://purl.org/dc/terms/"
                xmlns:xml="http://www.w3.org/XML/1998/namespace"
            >';
        foreach($p_mData as $key => $val) {
            $xmlStr .= '<'.$key.'>'.$val.'</'.$key.'>';
        }
        $xmlStr .= '</metadata>
        </audioClip>';

        $sessid = $_SESSION['cc_sessid'];
        $res = $this->xrc->xr_updateAudioClipMetadata($sessid, $this->m_gunid, $xmlStr);
        return $res['status'];
    } // fn __editStorageServerMetadata


    /**
     * Updates metadata on local server.
     *
     * @param array $p_mData
     *
     * @return bool TRUE on success, FALSE on failure
     */
    function __editLocalMetadata($p_mData)
    {
        return AudioclipLocalMetadata::editMetadata($p_mData);
    } // fn __editLocalMetadata

} // class Audioclip

?>