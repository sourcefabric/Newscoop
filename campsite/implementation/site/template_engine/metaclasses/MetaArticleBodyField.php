<?php

/**
 * @package Campsite
 */


final class MetaArticleBodyField {
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
    public function MetaArticleBodyField($p_content, $p_articleName, $p_bodyField,
                                         $p_headerFormatStart = null,
                                         $p_headerFormatEnd = null) {
//        if (!$p_article->defined) {
//            return;
//        }
//        try {
//            $content = $p_article->$p_bodyField;
//        } catch (InvalidPropertyException $ex) {
//            return;
//        }
        $this->m_subtitles = MetaSubtitle::ReadSubtitles($p_content, $p_articleName,
                                                         $p_headerFormatStart,
                                                         $p_headerFormatEnd);
        $this->m_sutitlesNames = array();
        foreach ($this->m_subtitles as $subtitle) {
            $this->m_sutitlesNames = $subtitle->getName();
        }
    }


    /**
     * Returns the content of the given subtitles of the article body field.
     *
     * @param array $p_subtitles
     * @return string
     */
    public function getContent(array $p_subtitles = array())
    {
        $printAll = empty($p_subtitles);
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
    public function getSubtitles() {
        return $this->m_subtitles;
    }


    /**
     * Returns an array containing the subtitle names
     *
     * @return array of string
     */
    public function getSubtitlesNames() {
        return $this->m_sutitlesNames;
    }
}

?>