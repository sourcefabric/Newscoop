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
     * Stores the article object that owns the body field
     *
     * @var MetaArticle
     */
    private $m_parent_article;


    /**
     * Stores the object describing the article type field
     *
     * @var ArticleTypeField
     */
    private $m_articleTypeField;


    /**
     * Stores the body field name
     */
    private $m_fieldName;


    /**
     * Constructor
     *
     * @param string $p_content
     */
    public function __construct($p_content, MetaArticle $p_parent, $p_fieldName,
                                $p_articleName, $p_subtitleNumber = null,
                                $p_headerFormatStart = null, $p_headerFormatEnd = null) {
        $this->m_subtitleNumber = $p_subtitleNumber;
        $this->m_subtitles = MetaSubtitle::ReadSubtitles($p_content, $p_fieldName, $p_articleName,
                                                         $p_headerFormatStart, $p_headerFormatEnd);
        $this->m_sutitlesNames = array();
        foreach ($this->m_subtitles as $subtitle) {
            $this->m_sutitlesNames[] = $subtitle->name;
        }
        $this->m_parent_article = new Article($p_parent->language->number, $p_parent->number);
        $this->m_fieldName = $p_fieldName;
        $this->m_articleTypeField = new ArticleTypeField($p_parent->type_name, $p_fieldName);
    }


    public function __toString() {
        return $this->getContent(!is_null($this->m_subtitleNumber) ? array($this->m_subtitleNumber) : array());
    }


    public function __get($p_property) {
        switch (strtolower($p_property)) {
            case 'all_subtitles': return $this->getContent(array());
            case 'first_paragraph': return $this->getParagraphs($this->__toString(), array(1));
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


    private function getParagraphs($p_content, array $p_paragraphs = array()) {
        $printAll = count($p_paragraphs) == 0;
        $content = '';
        $paragraphs = preg_split("/(<[\s]*\/?[\s]*p[\s]*>|<[\s]*br[\s]*\/?>([\s]|&nbsp;)*<[\s]*br[\s]*\/?>)/i",
        $p_content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $index = 0;
        $pStart = false;
        foreach ($paragraphs as $paragraph) {
            if (preg_match("/^([\s]|&nbsp;)*$/i", $paragraph)) {
                // This is an empty paragraph, skip it.
                continue;
            }
            if (preg_match("/^<[\s]*\/p[\s]*>$/i", $paragraph)
            || preg_match("/^<[\s]*br[\s]*\/?>([\s]|&nbsp;)*<[\s]*br[\s]*\/?>$/i", $paragraph)) {
                if ($printAll || array_search($index, $p_paragraphs) !== false) {
                    $content .= $paragraph;
                }
                continue;
            } elseif (preg_match("/^<[\s]*p[\s]*>$/i", $paragraph)) {
                // paragraph start
                $pStart = true;
                $index++;
            } else {
                if (!$pStart) {
                    $index++;
                }
                $pStart = false;
            }
            if ($printAll || array_search($index, $p_paragraphs) !== false) {
                $content .= $paragraph;
            }
        }
        return $content;
    }


    /**
     * Returns the content of the given subtitles of the article body field.
     *
     * @param array $p_subtitles
     * @return string
     */
    private function getContent(array $p_subtitles = array())
    {
        global $Campsite;

        $printAll = count($p_subtitles) == 0;
        $content = '';
        foreach ($this->m_subtitles as $index=>$subtitle) {
            if (!$printAll && array_search($index, $p_subtitles) === false) {
                continue;
            }
            $content .= $index > 0 ? $subtitle->formatted_name : '';
            $content .= $subtitle->content;
        }
        if ($this->m_articleTypeField->isContent()) {
            $objectType = new ObjectType('article');
            $userId = CampTemplate::singleton()->context()->user->identifier;
            $requestObjectId = $this->m_parent_article->getProperty('object_id');
            $updateArticle = empty($requestObjectId);
            try {
                // note that SessionRequest::Create() is called at the js-based stats now, at CampSite::writeStats();
                if ($updateArticle) {
                    $this->m_parent_article->setProperty('object_id', $requestObjectId);
                }

                // statistics shall be only gathered if the site admin set it on (and not for editor previews)
                $context = CampTemplate::singleton()->context();
                if ((SystemPref::CollectStatistics()) && (!$context->preview)) {
                    $stat_web_url = $Campsite['WEBSITE_URL'];
                    if ("/" != $stat_web_url[strlen($stat_web_url)-1]) {
                        $stat_web_url .= "/";
                    }
                    $article_number = $this->m_parent_article->getProperty('Number');
                    $language_obj = new MetaLanguage($this->m_parent_article->getProperty('IdLanguage'));
                    $language_code = $language_obj->Code;
                    $name_spec = '_' . $article_number . '_' . $language_code;

                    $content .= '
                        <script type="text/javascript">
                        var stats_getHTTPObject' . $name_spec . ' = function () {
                            var xhr = false;
                            if (window.XMLHttpRequest) {
                                xhr = new XMLHttpRequest();
                            } else if (window.ActiveXObject) {
                                try {
                                    xhr = new ActiveXObject("Msxml2.XMLHTTP");
                                } catch(e) {
                                    try {
                                        xhr = new ActiveXObject("Microsoft.XMLHTTP");
                                    } catch(e) {
                                        xhr = false;
                                    }
                                }
                            }
                            return xhr;
                        };

                        var stats_submit' . $name_spec . ' = function () {
                            var stats_request = stats_getHTTPObject' . $name_spec . '();
                            stats_request.onreadystatechange = function() {};
    
                            var read_date = new Date();
                            var read_path = "_statistics/reader/article/";
                            var request_randomizer = "" + read_date.getTime() + Math.random();
                            var stats_url = "' . $stat_web_url . '" + read_path + "' . $article_number . '/' . $language_code . '/";
                            try {
                                stats_request.open("GET", stats_url + "?randomizer=" + request_randomizer, true);
                                stats_request.send(null);
                                /* not everybody has jquery installed
                                $.ajax({
                                    url: stats_url,
                                    data: {randomizer: request_randomizer},
                                    success: function() {}
                                });
                                */
                            } catch (e) {}
                        };
                        stats_submit' . $name_spec . '();
                        </script>
                    ';
                }
            } catch (Exception $ex) {
                $content .= "<p><strong><font color=\"red\">INTERNAL ERROR! " . $ex->getMessage()
                         . "</font></strong></p>\n";
                // do something
            }
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


    protected function trigger_invalid_property_error($p_property, $p_smarty = null)
    {
        $errorMessage = INVALID_PROPERTY_STRING . " $p_property "
                        . OF_OBJECT_STRING . ' article->' . $this->m_fieldName;
        CampTemplate::singleton()->trigger_error($errorMessage, $p_smarty);
    }
}

?>