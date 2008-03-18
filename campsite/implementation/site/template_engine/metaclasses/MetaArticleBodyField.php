<?php

/**
 * @package Campsite
 */


final class MetaArticleBodyField {
    /**
     * Stores the number of the subtitle that has to be displayed
     *
     * @var int
     */
    private $m_subtitleNumber;


    /**
     * Stores the subtitle objects
     *
     * @var array of MetaSubtitle
     */
    private $m_subtitles;


    /**
     * Stores the subtitles names
     *
     * @var array of string
     */
    private $m_sutitlesNames;


    /**
     * Constructor
     *
     * @param string $p_content
     */
    public function MetaArticleBodyField($p_content, $p_fieldName, $p_articleName,
    $p_subtitleNumber = null, $p_headerFormatStart = null, $p_headerFormatEnd = null) {
        $this->m_subtitleNumber = $p_subtitleNumber;
        $this->m_subtitles = MetaSubtitle::ReadSubtitles($p_content, $p_fieldName, $p_articleName,
        $p_headerFormatStart, $p_headerFormatEnd);
        $this->m_sutitlesNames = array();
        foreach ($this->m_subtitles as $subtitle) {
            $this->m_sutitlesNames = $subtitle->getName();
        }
    }


    public function __toString() {
        return $this->getContent(!is_null($this->m_subtitleNumber) ? array($this->m_subtitleNumber) : array());
    }


    public function __get($p_property) {
        switch (strtolower($p_property)) {
            case 'subtitles_count': return $this->getSubtitlesCount();
            case 'subtitle_number': return $this->m_subtitleNumber;
            case 'subtitle_is_current':
                return $this->m_subtitleNumber == CampTemplate::singleton()->context()->subtitle->number
                && !is_null($this->m_subtitleNumber);
            case 'has_previous_subtitles':
                if (is_null($this->m_subtitleNumber)) {
                    return null;
                }
                return (int)($this->m_subtitleNumber > 0);
            case 'has_next_subtitles':
                if (is_null($this->m_subtitleNumber)) {
                    return null;
                }
                return (int)($this->m_subtitleNumber < ($this->getSubtitlesCount() - 1));
            default:
                $this->trigger_invalid_property_error($p_property);
                return null;
        }
    }


    /**
     * Returns the content of the given subtitles of the article body field.
     *
     * @param array $p_subtitles
     * @return string
     */
    private function getContent(array $p_subtitles = array())
    {
        $printAll = count($p_subtitles) == 0;
        $content = '';
        foreach ($this->m_subtitles as $index=>$subtitle) {
            if (!$printAll && array_search($index, $p_subtitles) === false) {
                continue;
            }
            $content .= $index > 0 ? $subtitle->getFormattedName() : '';
            $content .= $subtitle->getContent();
        }
        return $content;
    }


    /**
     * Returns the array of subtiles existent in the article body field.
     *
     * @return array of MetaSubtitle
     */
    private function getSubtitles() {
        return $this->m_subtitles;
    }


    /**
     * Returns the total number of subtitles
     *
     * @return int
     */
    private function getSubtitlesCount() {
        return count($this->m_subtitles);
    }


    /**
     * Returns an array containing the subtitle names
     *
     * @return array of string
     */
    private function getSubtitlesNames() {
        return $this->m_sutitlesNames;
    }
}

?>