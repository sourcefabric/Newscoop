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
require_once($g_documentRoot.'/classes/AudioclipXMLMetadata.php');
require_once($g_documentRoot.'/classes/AudioclipDatabaseMetadata.php');
require_once('HTTP/Client.php');


$metatagLabel = array(
    'dc:title' => 'Title',
    'dc:title' => 'Title',
    'dc:creator' => 'Creator',
    'dc:type' => 'Genre',
    'dc:format' => 'File format',
    'dcterms:extent' => 'Length',
    'dc:title' => 'Title',
    'dc:creator' => 'Creator',
    'dc:source' => 'Album',
    'ls:year' => 'Year',
    'dc:type' => 'Genre',
    'dc:description' => 'Description',
    'dc:format' => 'Format',
    'ls:bpm' => 'BPM',
    'ls:rating' => 'Rating',
    'dcterms:extent' => 'Length',
    'ls:encoded_by' => 'Encoded by',
    'ls:track_num' => 'Track number',
    'ls:disc_num' => 'Disc number',
    'ls:mood' => 'Mood',
    'dc:publisher' => 'Label',
    'ls:composer' => 'Composer',
    'ls:bitrate' => 'Bitrate',
    'ls:channels' => 'Channels',
    'ls:samplerate' => 'Sample rate',
    'ls:encoder' => 'Encoder software used',
    'ls:crc' => 'Checksum',
    'ls:lyrics' => 'Lyrics',
    'ls:orchestra' => 'Orchestra or band',
    'ls:conductor' => 'Conductor',
    'ls:lyricist' => 'Lyricist',
    'ls:originallyricist' => 'Original lyricist',
    'ls:radiostationname' => 'Radio station name',
    'ls:audiofileinfourl' => 'Audio file information web page',
    'ls:artisturl' => 'Artist web page',
    'ls:audiosourceurl' => 'Audio source web page',
    'ls:radiostationurl' => 'Radio station web page',
    'ls:buycdurl' => 'Buy CD web page',
    'ls:isrcnumber' => 'ISRC number',
    'ls:catalognumber' => 'Catalog number',
    'ls:originalartist' => 'Original artist',
    'dc:rights' => 'Copyright',
    'dc:title' => 'Title',
    'dcterms:temporal' => 'Report date/time',
    'dcterms:spatial' => 'Report location',
    'dcterms:entity' => 'Report organizations',
    'dc:description' => 'Description',
    'dc:creator' => 'Creator',
    'dc:subject' => 'Subject',
    'dc:type' => 'Genre',
    'dc:format' => 'Format',
    'dc:contributor' => 'Contributor',
    'dc:language' => 'Language',
    'dc:rights' => 'Copyright',
    'dc:title' => 'Title',
    'dc:creator' => 'Creator',
    'dcterms:extent' => 'Length',
    'dc:description' => 'Description',
    'ls:url'    => 'Stream URL'
);

$mask = array(
    'pages' => array(
        'Main'  => array(
            array(
                'element' => 'dc:title',
                'type' => 'text',
                'required' => TRUE,
            ),
            array(
                'element' => 'dc:creator',
                'type' => 'text',
                'label' => 'Creator',
                'required' => TRUE,
            ),
            array(
                'element'=> 'dc:type',
                'type' => 'text',
                'required' => TRUE,
            ),
            array(
                'element' => 'dc:format',
                'type' => 'select',
                'required' => TRUE,
                'options' => array(
                                'File' => 'Audioclip',
                                'live stream' => 'Webstream'
                               ),
                'attributes'=> array('disabled' => 'on'),
            ),
            array(
                'element' => 'dcterms:extent',
                'type' => 'text',
                'attributes' => array('disabled' => 'on'),
            ),
        ),
        'Music'  => array(
            array(
                'element' => 'dc:title',
                'type' => 'text',
            ),
            array(
                'element' => 'dc:creator',
                'type' => 'text',
            ),
            array(
                'element' => 'dc:source',
                'type' => 'text',
                'id3' => array('Album')
            ),
            array(
                'element' => 'ls:year',
                'type' => 'select',
                'options' => '', //_getNumArr(1900, date('Y')+5),
            ),
            array(
                'element' => 'dc:type',
                'type' => 'text',
            ),
            array(
                'element' => 'dc:description',
                'type' => 'textarea',
            ),
            array(
                'element' => 'dc:format',
                'type' => 'select',
                'options' => array(
                                'File' => 'Audioclip',
                                'live stream' => 'Webtream'
                               ),
                'attributes'=> array('disabled' => 'on'),
            ),
            array(
                'element' => 'ls:bpm',
                'type' => 'text',
                'rule' => 'numeric',
            ),
            array(
                'element' => 'ls:rating',
                'type' => 'text',
                'rule' => 'numeric',
            ),
            array(
                'element' => 'dcterms:extent',
                'type' => 'text',
                'attributes' => array('disabled' => 'on'),
            ),
            array(
                'element' => 'ls:encoded_by',
                'type' => 'text',
            ),
            array(
                'element' => 'ls:track_num',
                'type' => 'select',
                'options' => '', //_getNumArr(0, 99),
            ),
            array(
                'element' => 'ls:disc_num',
                'type' => 'select',
                'options' => '', //_getNumArr(0, 20),
            ),
            array(
                'element' => 'ls:mood',
                'type' => 'text',
            ),
            array(
                'element' => 'dc:publisher',
                'type' => 'text',
            ),
            array(
                'element' => 'ls:composer',
                'type' => 'text',
            ),
            array(
                'element' => 'ls:bitrate',
                'type' => 'text',
                'rule' => 'numeric',
            ),
            array(
                'element' => 'ls:channels',
                'type' => 'select',
                'options' => array(
                                '' => '',
                                1 => 'Mono',
                                2 => 'Stereo',
                                6 => '5.1'
                               ),
            ),
            array(
                'element' => 'ls:samplerate',
                'type' => 'text',
                'rule' => 'numeric',
                'attributes'=> array('disabled' => 'on'),
            ),
            array(
                'element' => 'ls:encoder',
                'type' => 'text',
            ),
            array(
                'element' => 'ls:crc',
                'type' => 'text',
                'rule' => 'numeric',
            ),
            array(
                'element' => 'ls:lyrics',
                'type' => 'textarea',
            ),
            array(
                'element' => 'ls:orchestra',
                'type' => 'text',
            ),
            array(
                'element' => 'ls:conductor',
                'type' => 'text',
            ),
            array(
                'element' => 'ls:lyricist',
                'type' => 'text',
            ),
            array(
                'element' => 'ls:originallyricist',
                'type' => 'text',
            ),
            array(
                'element' => 'ls:radiostationname',
                'type' => 'text',
            ),
            array(
                'element' => 'ls:audiofileinfourl',
                'type' => 'text',
                'attributes' => array('maxlength' => 256)
            ),
            array(
                'rule' => 'regex',
                'element' => 'ls:audiofileinfourl',
                'format' => '', //UI_REGEX_URL,
                'rulemsg' => 'Audio file information web page seems not to be valid URL'
            ),
            array(
                'element' => 'ls:artisturl',
                'type' => 'text',
                'attributes' => array('maxlength' => 256)
            ),
            array(
                'rule' => 'regex',
                'element' => 'ls:artisturl',
                'format' => '', //UI_REGEX_URL,
                'rulemsg' => 'Artist web page seems not to be valid URL'
            ),
            array(
                'element' => 'ls:audiosourceurl',
                'type' => 'text',
                'attributes' => array('maxlength' => 256)
            ),
            array(
                'rule' => 'regex',
                'element' => 'ls:audiosourceurl',
                'format' => '', //UI_REGEX_URL,
                'rulemsg' => 'Audio source web page seems not to be valid URL'
            ),
            array(
                'element' => 'ls:radiostationurl',
                'type' => 'text',
                'attributes' => array('maxlength' => 256)
            ),
            array(
                'rule' => 'regex',
                'element' => 'ls:radiostationurl',
                'format' => '', //UI_REGEX_URL,
                'rulemsg' => 'Radio station web page seems not to be valid URL'
            ),
            array(
                'element' => 'ls:buycdurl',
                'type' => 'text',
                'attributes' => array('maxlength' => 256)
            ),
            array(
                'rule' => 'regex',
                'element' => 'ls:buycdurl',
                'format' => '', //UI_REGEX_URL,
                'rulemsg' => 'Buy CD web page seems not to be valid URL'
            ),
            array(
                'element' => 'ls:isrcnumber',
                'type' => 'text',
                'rule' => 'numeric',
            ),
            array(
                'element' => 'ls:catalognumber',
                'type' => 'text',
                'rule' => 'numeric',
            ),
            array(
                'element' => 'ls:originalartist',
                'type' => 'text',
            ),
            array(
                'element' => 'dc:rights',
                'type' => 'text',
            ),
        ),
        'Voice' => array(
            array(
                'element' => 'dc:title',
                'type' => 'text',
            ),
            array(
                'element' => 'dcterms:temporal',
                'type' => 'text',
            ),
            array(
                'element' => 'dcterms:spatial',
                'type' => 'textarea',
            ),
            array(
                'element' => 'dcterms:entity',
                'type' => 'textarea',
            ),
            array(
                'element' => 'dc:description',
                'type' => 'textarea',
            ),
            array(
                'element' => 'dc:creator',
                'type' => 'text',
            ),
            array(
                'element' => 'dc:subject',
                'type' => 'text',
            ),
            array(
                'element' => 'dc:type',
                'type' => 'text',
            ),
            array(
                'element' => 'dc:format',
                'type' => 'select',
                'options' => array(
                                'File' => 'Audioclip',
                                'live stream' => 'Webstream'
                                ),
                'attributes'=> array('disabled' => 'on')
            ),
            array(
                'element' => 'dc:contributor',
                'type' => 'text',
            ),
            array(
                'element' => 'dc:language',
                'type' => 'text',
            ),
            array(
                'element' => 'dc:rights',
                'type' => 'text',
            ),
        )
    )
);


/**
 * @package Campsite
 */
class Audioclip {
    var $m_gunId = null;
    var $m_metaData = array();
    var $m_fileTypes = array('.mp3','.ogg','.wav');


    /**
     *
     */
    function Audioclip($p_gunId = null)
    {
        if (!is_null($p_gunId) && is_numeric($p_gunId)) {
            $aclipDbaseMdataObj =& new AudioclipDatabaseMetadata($p_gunId);
            $this->m_metaData = $aclipDbaseMdataObj->fetch();
            if (sizeof($this->m_metaData) == 0) {
                $aclipXMLMdataObj =& new AudioclipXMLMetadata($p_gunId);
                $this->m_metaData = $aclipXMLMdataObj->fetch();

                // We call AudioclipDatabase write() method to save
                // metadata in local cache. Parameter is $this->aclipMetadata
                // which is an array of AudioclipMetadataEntry objects
                //
                // $aclipDbaseMdataObj->write($this->aclipMetadata);
            }
            $this->m_gunId = $p_gunId;
        }
    } // constructor


    /**
     * Returns the value of the clip's unique identifier
     *
     * @return string
     *      the clip unique identifier
     */
    function getGunId()
    {
    	return $this->m_gunId;
    } // fn getGunId


    /**
     * Returns the value of the give meta tag
     *
     * @param string $p_tagName
     *      the name of the meta tag
     *
     * @return string
     *      the meta tag value
     */
    function getMetatagValue($p_tagName)
    {
    	$namespaces = array('dc', 'ls', 'dcterms');
    	
		$p_tagName = trim(strtolower($p_tagName));
    	if (is_null($this->m_gunId) || sizeof($this->m_metaData) == 0) {
    		return null;
    	}
    	$tagNs = strstr($p_tagName, ':');
    	if ($tagNs !== false) {
    		if (!array_key_exists($tagNs, $namespaces)) {
	    		return PEAR_Error::PEAR_Error("Invalid metatag namespace.");
    		}
    		if (!array_key_exists($p_tagName, $this->m_metaData)) {
	    		return null;
    		}
    		return $this->m_metaData[$p_tagName]->getValue();
    	}
    	foreach ($namespaces as $namespace) {
    		$tag = $namespace . ':' . $p_tagName;
    		if (array_key_exists($tag, $this->m_metaData)) {
    			return $this->m_metaData[$tag]->getValue();
    		}
    	}
    	return null;
    } // fn getMetaTagValue


    /**
     * Returns an array containing available meta tags
     *
     * @return string
     *      the meta tag value
     */
    function getAvailableMetaTags()
    {
    	if (is_null($this->m_gunId) || sizeof($this->m_metaData) == 0) {
    		return null;
    	}
    	return array_keys($this->m_metaData);
    } // fn getAvailableMetaTags


    /**
     * Retrieve a list of Audioclip objects based on the given constraints
     *
     * @param array $conditions
     *      array of struct with fields:
     *          cat: string - metadata category name
     *          op: string - operator, meaningful values:
     *              'full', 'partial', 'prefix',
     *              '=', '<', '<=', '>', '>='
     *          val: string - search value
     * @param string $operator
     *      type of conditions join (any condition matches /
     *      all conditions match), meaningful values: 'and', 'or', ''
     *      (may be empty or ommited only with less then 2 items in
     *      "conditions" field)
     * @param int $limit
     *      limit for result arrays (0 means unlimited)
     * @param int $offset
     *      starting point (0 means without offset)
     * @param string $orderby
     *      string - metadata category for sorting (optional) or array
     *      of strings for multicolumn orderby
     *      [default: dc:creator, dc:source, dc:title]
     * @param bool $desc
     *      boolean - flag for descending order (optional) or array of
     *      boolean for multicolumn orderby (it corresponds to elements
     *      of orderby field)
     *      [default: all ascending]
     *
     * @return array
     *      Array of Audioclip objects
     */
    function SearchAudioclips($offset = 0, $limit = 0, $conditions = array(),
                              $operator = 'and',
    						  $orderby = 'dc:creator, dc:source, dc:title',
                              $desc = false)
    {
        global $mdefs;

        $xrc =& XR_CcClient::Factory($mdefs);
		if (PEAR::isError($xrc)) {
			return $xrc;
		}
        $sessid = camp_session_get('cc_sessid', '');
		$criteria = array('filetype' => 'audioclip',
						  'operator' => $operator,
						  'limit' => $limit,
						  'offset' => $offset,
						  'orderby' => $orderby,
						  'desc' => $desc,
						  'conditions' => $conditions
						  );
		$result = $xrc->xr_searchMetadata($sessid, $criteria);
		if (PEAR::isError($result)) {
			return $result;
		}
		$clips = array();
		foreach ($result['results'] as $clip) {
			$clips[] = new Audioclip($clip['gunid']);
		}
    	return $clips;
    } // fn SearchAudioclips


    /**
     * Stores the Audioclip into the Campcaster storage server.
     *
     * @param string $p_fileName
     *      The name of the audioclip
     * @param array $p_xrParams
     *      Array of params to send to the XML RPC method
     *
     * @return Audioclip|PEAR_Error
     */
    function StoreAudioclip($p_filePath, $p_xrParams)
    {
        if (file_exists($p_filePath) == false) {
            return new PEAR_Error(getGS('File $1 does not exist', $p_fileName));
        }

        $sessid = camp_session_get('cc_sessid', '');
        $gunId = AudioclipXMLMetadata::Upload($sessid, $p_filePath, $p_xrParams['gunid'],
                                              $p_xrParams['mdata'], $p_xrParams['chsum']);
        if (PEAR::isError($gunid)) {
            return $gunId;
        }
        return $gunId;
    } // fn StoreAudioclip


    /**
     * Use getid3 to retrieve all the metatags for the given audio file.
     *
     * @param string $p_file
     *      The file to analyze
     *
     * @return array
     *      An array with all the id3 metatags
     */
    function AnalyzeFile($p_file)
    {
        require_once($_SERVER['DOCUMENT_ROOT'].'/include/getid3/getid3.php');

        $getid3Obj = new getID3;
        return $getid3Obj->analyze($p_file);
    } // fn AnalyzeFile


    /**
     * This function should be called when an audioclip is uploaded.
     * It will save the audioclip file to the temporary directory on
     * the disk before to be sent to the Campcaster storage server.
     *
     * @param array $p_fileVar
     *      The audioclip file submited
     *
     * @return string|PEAR_Error
     *      The full pathname to the file or Error
     */
    function onFileUpload($p_fileVar)
    {
        global $Campsite;

        if (!is_array($p_fileVar)) {
			return null; // PEAR Error
		}

        // Verify its a valid file.
		$filesize = filesize($p_fileVar['tmp_name']);
		if ($filesize === false) {
			echo "l1";
			return new PEAR_Error("Audioclip::OnFileUpload(): invalid parameters received.");
		}
        if ($this->isValidFileType($p_fileVar['name']) == FALSE) {
			echo "l2";
            return new PEAR_Error("Audioclip::OnFileUpload(): invalid file type.");
        }
        $target = $Campsite['TMP_DIRECTORY'] . $p_fileVar['name'];
        if (!move_uploaded_file($p_fileVar['tmp_name'], $target)) {
			echo "l3";
            return new PEAR_Error(camp_get_error_message(CAMP_ERROR_CREATE_FILE, $target), CAMP_ERROR_CREATE_FILE);
        }
        chmod($target, 0644);
        return $target;
    } // fn onFileUpload


    /**
     * This function should be called when an audioclip has been
     * successfully sent to the Storage server. It deletes the
     * temporary audio file on Local.
     *
     * @param string $p_fileName
     *      The temporary file to delete after stored in the Storage server
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
     * @param $p_fileName
     *      The name of the audioclip file
     *
     * @return boolean
     *      TRUE on success, FALSE on failure
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
     *      The form data submited with all the audioclip metadata
     *
     * @return boolean|PEAR_Error
     *      TRUE on success, PEAR Error on failure
     */
    function editMetadata($p_formData)
    {
        global $mask;

        if (!is_array($p_formData)) {
            return new PEAR_Error(getGS('Invalid parameter given to Audioclip::editMetadata()'));
        }

        $metaData = array();
        foreach($mask['pages'] as $key => $val) {
            foreach($mask['pages'][$key] as $k => $v) {
                $element_encode = str_replace(':','_',$v['element']);
                $p_formData['f_'.$key.'_'.$element_encode] ? $metaData[$v['element']] = $p_formData['f_'.$key.'_'.$element_encode] : NULL;
            }
        }

        if (sizeof($metaData) == 0) return;

        $aclipXMLMdataObj =& new AudioclipXMLMetadata($this->m_gunId);
        if ($aclipXMLMdataObj->write($metaData) == false) {
            return new PEAR_Error(getGS('Cannot update audioclip metadata on storage server'));
        }
        // $metaData has to be an array of AudioclipMetadataEntry objects here
        $aclipDbaseMdataObj =& new AudioclipDatabaseMetadata($this->m_gunId);
        if ($aclipDbaseMdataObj->write($metaData) == false) {
            return new PEAR_Error(getGS('Cannot update audioclip metadata on Campsite'));
        }
        return true;
    } // fn editMetadata


    /**
     * Create a XML text file from an array coming from the
     * Audioclip metadata edit form submited.
     *
     * @param array $p_formData
     *      the form data submited
     *
     * @return string $xmlTextFile
     *      the XML string
     */
    function CreateXMLTextFile($p_formData)
    {
        global $mask;

        $xmlTextFile = '<?xml version="1.0" encoding="utf-8"?>
        <audioClip>
        <metadata
            xmlns="http://mdlf.org/campcaster/elements/1.0/"
            xmlns:ls="http://mdlf.org/campcaster/elements/1.0/"
            xmlns:dc="http://purl.org/dc/elements/1.1/"
            xmlns:dcterms="http://purl.org/dc/terms/"
            xmlns:xml="http://www.w3.org/XML/1998/namespace"
        >';

        foreach($mask['pages'] as $key => $val) {
            foreach($mask['pages'][$key] as $k => $v) {
                $element_encode = str_replace(':','_',$v['element']);
                $p_formData['f_'.$key.'_'.$element_encode] ? $metaData[$v['element']] = $p_formData['f_'.$key.'_'.$element_encode] : NULL;
            }
        }
        foreach($metaData as $key => $val) {
            $xmlTextFile .= '<'.$key.'>'.$val.'</'.$key.'>';
        }
        $xmlTextFile .= '</metadata>
        </audioClip>';
        return $xmlTextFile;
    } // fn CreateXMLTextFile

} // class Audioclip

?>