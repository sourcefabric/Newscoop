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

require_once($g_documentRoot.'/classes/XR_CcClient.php');
require_once($g_documentRoot.'/classes/AudioclipMetadataEntry.php');


/**
 * @package Campsite
 */
class AudioclipXMLMetadata {
    /**
     * The gunid (Global Unique ID) of the audioclip file
     */
    var $m_gunId = null;
    var $m_metaData = array();


    /**
     * Constructor
     */
    function AudioclipXMLMetadata($p_gunId = null)
    {
        global $mdefs;

        $this->xrc =& XR_CcClient::Factory($mdefs);
        if (!is_null($p_gunId)) {
            $this->m_gunId = $p_gunId;
            $this->fetch();
        }
    } // constructor


    /**
     * Fetch all the metadata for the audioclip.
     *
     * @return array $metaData
     *      An array of AudioclipMetadataEntry objects
     */
    function fetch($p_gunId = null)
    {
        if (!is_null($p_gunId)) {
        	$this->m_gunId = $p_gunId;
        }
        if (is_null($this->m_gunId)) {
        	return false;
        }

        $sessid = camp_session_get('cc_sessid', '');
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
            $metaData = array();
            foreach ($values as $value) {
            	if (!AudioclipMetadataEntry::IsValidNamespace($value['tag'])) {
            		continue;
            	}
            	$recordSet['gunid'] = $this->m_gunId;
				$recordSet['predicate_ns'] = AudioclipMetadataEntry::GetTagNS($value['tag']);
				$recordSet['predicate'] = AudioclipMetadataEntry::GetTagName($value['tag']);
				$recordSet['object'] = $value['value'];
                $tmpMetadataObj =& new AudioclipMetadataEntry($recordSet);
                $metaData[strtolower($value['tag'])] = $tmpMetadataObj;
            }
            $this->m_metaData = $metaData;
            return $metaData;
        }
        return false;
    } // fn fetch
    
    /**
     * Uploads an audioclip to the storage server
     * 
     * @param string $p_sessionId
     * @param string $p_gunId
     * @param string $p_metaData
     * @param string $p_fileName
     * @param string $p_checksum
     * 
     * @return string or PEAR_Error
     *		gunId on success, PEAR_Error on failure
     */
    function Upload($p_sessionId, $p_filePath, $p_gunId, $p_metaData, $p_checksum)
    {
        global $mdefs;

        $xrcObj =& XR_CcClient::Factory($mdefs);
        $r = $xrcObj->xr_storeAudioClipOpen($p_sessionId, $p_gunId, $p_metaData,
        									basename($p_filePath), $p_checksum);
        if (PEAR::isError($r)) {
        	return $r;
        } else {
            exec(trim('curl -T ' . escapeshellarg($p_filePath) . ' ' . $r['url']));
        }
        $aData = $xrcObj->xr_storeAudioClipClose($p_sessionId, $r['token']);
        if (PEAR::isError($aData)) {
            return $aData;
        }
    	return $aData['gunid'];
    } // fn Upload


    /**
     * Updates metadata on storage server.
     *
     * @param array $p_metaData
     *      An array with all the audioclip metadata like:
     *      $array[namespace:predicate] = object
     *
     * @return boolean
     *      TRUE on success, FALSE on failure
     */
    function update($p_metaData)
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
            $xmlStr .= '<'.$key.'>'.$metaDataEntry->getTagValue().'</'.$key.'>';
        }
        $xmlStr .= '</metadata>
        </audioClip>';

        $sessid = camp_session_get('cc_sessid', '');
        $res = $this->xrc->xr_updateAudioClipMetadata($sessid, $this->m_gunid, $xmlStr);
        return $res['status'];
    } // fn update

} // class AudioclipXMLMetadata

?>