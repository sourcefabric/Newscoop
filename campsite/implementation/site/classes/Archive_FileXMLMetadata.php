<?php
/**
 * @package Campsite
 */

global $ADMIN_DIR;

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/lib_campsite.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/XR_CcClient.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Archive_FileMetadataEntry.php');


/**
 * @package Campsite
 */
class Archive_FileXMLMetadata
{
    /**
     * The gunid (Global Unique ID) of the file
     */
    var $m_gunId = null;
    var $m_metaData = array();
    var $m_exists = false;


    /**
     * Constructor
     *
     * @param string $p_gunId
     *      The file global unique identifier
     */
    public function __construct($p_gunId = null)
    {
        global $mdefs;

        $this->xrc = XR_CcClient::Factory($mdefs, true);
        if (!is_null($p_gunId)) {
            $this->m_gunId = $p_gunId;
            $this->fetch();
        }
    } // constructor


    /**
     * Returns true if a file having this metadata exists
     *
     * @return boolean
     */
    public function exists()
    {
    	return $this->m_exists;
    }


    /**
     * Returns the file unique identifier
     *
     * @return unknown
     */
    public function getGunId()
    {
    	return $this->m_gunId;
    }


    /**
     * Fetch all the metadata for the file
     *
     * @param string $p_gunId
     *      The file global unique identifier
     *
     * @return array $metaData
     *      An array of Archive_FileMetadataEntry objects
     */
    public function fetch($p_gunId = null)
    {
        if (!is_null($p_gunId)) {
            $this->m_gunId = $p_gunId;
        }
        if (is_null($this->m_gunId)) {
            return false;
        }

	// TODO: set the proper session name here
        $sessid = camp_session_get(CS_FILEARCHIVE_SESSION_VAR_NAME, '');
        if (empty($sessid)) {
            return new PEAR_Error(getGS('Can not fetch audioclip metadata: the connection to Campcaster was not established.'));
        }
        $res = $this->xrc->xr_existsAudioClip($sessid, $this->m_gunId);
        if (PEAR::isError($res)) {
            return $res;
        }
        if ($res['exists']) {
            $xmlMetaData = $this->xrc->xr_getAudioClip($sessid, $this->m_gunId);
            if (PEAR::isError($xmlMetaData)) {
                return $xmlMetaData;
            }

            $xmlParser = xml_parser_create();
            $result = xml_parse_into_struct($xmlParser, $xmlMetaData['metadata'], $values);
            foreach ($values as $value) {
                if (!Archive_FileMetadataEntry::IsValidNamespace($value['tag'])) {
                    continue;
                }
                $recordSet['gunid'] = $this->m_gunId;
                $recordSet['predicate_ns'] = Archive_FileMetadataEntry::GetTagNS($value['tag']);
                $recordSet['predicate'] = Archive_FileMetadataEntry::GetTagName($value['tag']);
                $recordSet['object'] = $value['value'];
                $tmpMetadataObj = new Archive_FileMetadataEntry($recordSet);
                $this->m_metaData[strtolower($value['tag'])] = $tmpMetadataObj;
            }
            $this->m_exists = true;
            return $this->m_metaData;
        }
        return null;
    } // fn fetch


    /**
     * Uploads a file to the storage server
     *
     * @param string $p_sessId
     *      The session Id on the storage server
     * @param string $p_gunId
     *      The file global unique identifier
     * @param string $p_metaData
     *      A XML string of file metadata
     * @param string $p_filePath
     *      The full path to the file
     * @param string $p_checkSum
     *      The md5 check sum of the file
     *
     * @return string|PEAR_Error
     *		Audioclip gunid on success, PEAR_Error on failure
     */
    public static function Upload($p_sessId, $p_filePath, $p_gunId,
				  $p_metaData, $p_checkSum, $p_type)
    {
        global $mdefs;

        $xrcObj = XR_CcClient::Factory($mdefs, true);
        $r = $xrcObj->xr_storeMediaFileOpen($p_sessId, $p_gunId, $p_metaData,
                                            basename($p_filePath), $p_checkSum,
					    $p_type);
        if (PEAR::isError($r)) {
	    return $r;
        } else {
	    $fh = fopen($p_filePath, 'r');
	    $ch = curl_init($r['url']);
	    curl_setopt($ch, CURLOPT_PUT, true);
	    curl_setopt($ch, CURLOPT_INFILE, $fh);
            curl_setopt($ch, CURLOPT_INFILESIZE, filesize($p_filePath));
            $res = curl_exec($ch);
            curl_close($ch);
        }
        $mFileData = $xrcObj->xr_storeMediaFileClose($p_sessId, $r['token']);
        if (PEAR::isError($mFileData)) {
            return $mFileData;
        }
    	return $mFileData['gunid'];
    } // fn Upload


    /**
     * Updates metadata on storage server
     *
     * @param array $p_metaData
     *      An array of Archive_FileMetadataEntry objects
     *
     * @return boolean
     *      TRUE on success, FALSE on failure
     */
    public function update($p_metaData)
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
        foreach($p_metaData as $key => $metaDataEntry) {
            $xmlStr .= '<'.$key.'>'.$metaDataEntry->getValue().'</'.$key.'>';
        }
        $xmlStr .= '</metadata>
        </audioClip>';

        $sessid = camp_session_get(CS_FILEARCHIVE_SESSION_VAR_NAME, '');
        $res = $this->xrc->xr_updateAudioClipMetadata($sessid, $this->m_gunId, $xmlStr);
        if (PEAR::isError($res)) {
	    return $res;
        }
        return $res['status'];
    } // fn update

} // class Archive_FileXMLMetadata

?>