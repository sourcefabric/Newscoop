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
    var $m_exists = false;


    /**
     * Constructor
     *
     * @param string $p_gunId
     *      The audioclip global unique identifier
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
     * Returns true if an audioclip having this metadata exists
     *
     * @return boolean
     */
    function exists()
    {
    	return $this->m_exists;
    }
    
    
    /**
     * Returns the audioclip unique identifier
     *
     * @return unknown
     */
    function getGunId()
    {
    	return $this->m_gunId;
    }


    /**
     * Fetch all the metadata for the audioclip
     *
     * @param string $p_gunId
     *      The audioclip global unique identifier
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
            foreach ($values as $value) {
                if (!AudioclipMetadataEntry::IsValidNamespace($value['tag'])) {
                    continue;
                }
                $recordSet['gunid'] = $this->m_gunId;
                $recordSet['predicate_ns'] = AudioclipMetadataEntry::GetTagNS($value['tag']);
                $recordSet['predicate'] = AudioclipMetadataEntry::GetTagName($value['tag']);
                $recordSet['object'] = $value['value'];
                $tmpMetadataObj =& new AudioclipMetadataEntry($recordSet);
                $this->m_metaData[strtolower($value['tag'])] = $tmpMetadataObj;
            }
            $this->m_exists = true;
            return $this->m_metaData;
        }
        return null;
    } // fn fetch

    
    /**
     * Uploads an audioclip to the storage server
     * 
     * @param string $p_sessId
     *      The session Id on the Campcaster storage server
     * @param string $p_gunId
     *      The audioclip global unique identifier
     * @param string $p_metaData
     *      A XML string of audioclip metadata
     * @param string $p_filePath
     *      The full path to the audioclip file
     * @param string $p_checkSum
     *      The md5 check sum of the audioclip file
     * 
     * @return string|PEAR_Error
     *		Audioclip gunid on success, PEAR_Error on failure
     */
    function Upload($p_sessId, $p_filePath, $p_gunId, $p_metaData, $p_checkSum)
    {
        global $mdefs;

        $xrcObj =& XR_CcClient::Factory($mdefs);
        $r = $xrcObj->xr_storeAudioClipOpen($p_sessId, $p_gunId, $p_metaData,
                                            basename($p_filePath), $p_checkSum);
        if (PEAR::isError($r)) {
        	return $r;
        } else {
            exec(trim('curl -T ' . escapeshellarg($p_filePath) . ' ' . $r['url']));
        }
        $aClipData = $xrcObj->xr_storeAudioClipClose($p_sessId, $r['token']);
        if (PEAR::isError($aClipData)) {
            return $aClipData;
        }
    	return $aClipData['gunid'];
    } // fn Upload


    /**
     * Updates metadata on storage server
     *
     * @param array $p_metaData
     *      An array of AudioclipMetadataEntry objects
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
            $xmlStr .= '<'.$key.'>'.$metaDataEntry->getValue().'</'.$key.'>';
        }
        $xmlStr .= '</metadata>
        </audioClip>';

        $sessid = camp_session_get('cc_sessid', '');
        $res = $this->xrc->xr_updateAudioClipMetadata($sessid, $this->m_gunId, $xmlStr);
        if (PEAR::isError($res)) {
        	return $res;
        }
        return $res['status'];
    } // fn update

} // class AudioclipXMLMetadata

?>