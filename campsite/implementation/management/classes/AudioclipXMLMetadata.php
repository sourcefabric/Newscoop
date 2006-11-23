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


    /**
     * Constructor
     */
    function AudioclipXMLMetadata($p_gunId = null)
    {
        global $mdefs;

        $this->xrc =& XR_CcClient::Factory($mdefs);
        if (!is_null($p_gunId)) {
            $this->m_gunId = $p_gunId;
        }
    } // constructor


    /**
     * Fetch all the metadata for the audioclip.
     *
     * @return array $metaData
     *      An array of AudioclipMetadataEntry objects
     */
    function fetch()
    {
        $sessid = camp_session_get('cc_sessid', '');
        $gunId = dechex($this->m_gunId);
        if ($this->xrc->xr_existsAudioClip($sessid, $gunId)) {
            $xmlMetaData = $this->xrc->xr_getAudioClip($sessid, $gunId);

            $xmlParser = xml_parser_create();
            $result = xml_parse_into_struct($xmlParser, $xmlMetaData['metadata'], $values);
            $metaData = array();
            foreach ($values as $value) {
                if (strncasecmp($value['tag'], 'dc:', 3) == 0
                        || strncasecmp($value['tag'], 'ls:', 3) == 0
                        || strncasecmp($value['tag'], 'dcterms:', 8) == 0) {
                    $tmpMetadataObj =& new AudioclipMetadataEntry(strtoupper($value['tag']), $value['value']);
                    $metaData[] = $tmpMetadataObj;
                }
            }
            return $metaData;
        }
    } // fn fetch


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
    function write($p_metaData)
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
        foreach($p_metaData as $key => $val) {
            $xmlStr .= '<'.$key.'>'.$val.'</'.$key.'>';
        }
        $xmlStr .= '</metadata>
        </audioClip>';

        $sessid = camp_session_get('cc_sessid', '');
        $res = $this->xrc->xr_updateAudioClipMetadata($sessid, $this->m_gunid, $xmlStr);
        return $res['status'];
    } // fn write

} // class AudioclipXMLMetadata

?>