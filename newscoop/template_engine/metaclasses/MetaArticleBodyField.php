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
     * @param string $p_headerFormatStart
     * @param string $p_headerFormatEnd
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
        $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
        $cacheKeyArticle = $cacheService->getCacheKey(array('Article', $p_parent->language->number, $p_parent->number), 'article');
        if ($cacheService->contains($cacheKeyArticle)) {
            $this->m_parent_article = $cacheService->fetch($cacheKeyArticle);
        } else {
            $this->m_parent_article = new Article($p_parent->language->number, $p_parent->number);
            $cacheService->save($cacheKeyArticle, $this->m_parent_article);
        }
        $this->m_fieldName = $p_fieldName;
        $cacheKey = $cacheService->getCacheKey(array('ArticleTypeField', $p_parent->type_name, $p_fieldName), 'article_type');
        if ($cacheService->contains($cacheKey)) {
            $this->m_articleTypeField = $cacheService->fetch($cacheKey);
        } else {
             $articleTypeField = new ArticleTypeField($p_parent->type_name, $p_fieldName);
             $cacheService->save($cacheKey, $articleTypeField);
             $this->m_articleTypeField = $articleTypeField;
        }
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


    /**
     * @param string $p_content
     */
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
            $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
            $cacheKeyObjectType = $cacheService->getCacheKey(array('ObjectType', 'article'), 'ObjectType');
            if ($cacheService->contains($cacheKeyObjectType)) {
                $objectType = $cacheService->fetch($cacheKeyObjectType);
            } else {
                $objectType = new ObjectType('article');
                $cacheService->save($cacheKeyObjectType, $objectType);
            }
            $requestObjectId = $this->m_parent_article->getProperty('object_id');
            $updateArticle = empty($requestObjectId);
            try {
                if ($updateArticle) {
                    $requestObject = new RequestObject($requestObjectId);
                    if (!$requestObject->exists()) {
                        $requestObject->create(array('object_type_id'=>$objectType->getObjectTypeId()));
                        $requestObjectId = $requestObject->getObjectId();
                    }
                    $this->m_parent_article->setProperty('object_id', $requestObjectId);
                }

                // statistics shall be only gathered if the site admin set it on (and not for editor previews)
                $context = CampTemplate::singleton()->context();
                $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');
                if (($preferencesService->CollectStatistics == 'Y') && (!$context->preview)) {
                    $stat_web_url = $Campsite['WEBSITE_URL'];
                    if ('/' != $stat_web_url[strlen($stat_web_url)-1]) {
                        $stat_web_url .= '/';
                    }
                    $article_number = $this->m_parent_article->getProperty('Number');
                    $language_obj = new MetaLanguage($this->m_parent_article->getProperty('IdLanguage'));
                    $language_code = $language_obj->Code;
                    $name_spec = '_' . $article_number . '_' . $language_code;
                    $object_type_id = $objectType->getObjectTypeId();

                    $content .= Statistics::JavaScriptTrigger(array('name_spec' => $name_spec, 'object_type_id' => $object_type_id, 'request_object_id' => $requestObjectId));
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