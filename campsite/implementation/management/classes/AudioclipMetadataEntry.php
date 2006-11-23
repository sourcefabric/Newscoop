<?php
/**
 * @package Campsite
 */


class AudioclipMetadataEntry {
    var $m_tagName = null;
    var $m_tagValue = null;

    /**
     *
     */
    function AudioclipMetadataEntry($p_tagName, $p_tagValue)
    {
        $this->m_tagName = $p_tagName;
        $this->m_tagValue = $p_tagValue;
    } // fn AudioclipMetadataEntry


    /**
     * @return string
     */
    function getTagName()
    {
        return $this->m_tagName;
    } // fn getTagName


    /**
     * @return string
     */
    function getTagValue()
    {
        return $this->m_tagValue;
    } // fn getPredicate


    /**
     * @return string
     */
    function getNameSpace()
    {
        list($nameSpace, $localName) = explode(':', $this->m_tagName);
        return $nameSpace;
    } // fn getNameSpace

} // class AudioclipMetadataEntry

?>