<?php
/**
 * @package Campsite
 */

/**
 * @package Campsite
 */
final class MetaSubtitle
{
    /**
     * The pattern used to detect the subtitles.
     *
     * @var string
     */
    private static $m_SubtitlePattern = '<!\*\*[\s]*Title[\s]*>([^<]*)<!\*\*[\s]*EndTitle[\s]*>';

    /**
     * The pattern used to detect the subtitle formatting start.
     *
     * @var string
     */
    private static $m_HeaderStartPattern = '(<[\s]*[hH][\d][\s]*>[\s]*)*';

    /**
     * The pattern used to detect the subtitle formatting end.
     *
     * @var string
     */
    private static $m_HeaderEndPattern = '([\s]*<[\s]*\/[\s]*[hH][\d][\s]*>)*';

    /**
     * The subtitle order number
     *
     * @var int
     */
    private $m_number;

    /**
     * The field name to which this subtitle belongs
     *
     * @var string
     */
    private $m_fieldName;

    /**
     * The number of subtitles
     *
     * @var int
     */
    private $m_count;

    /**
     * The subtitle name
     *
     * @var string
     */
    private $m_name;

    /**
     * The subtitle content
     *
     * @var string
     */
    private $m_content;

    /**
     * Stores the subtitle name formatting start
     *
     * @var string
     */
    private $m_nameFormattingStart;

    /**
     * Stores the subtitle name formatting end
     *
     * @var string
     */
    private $m_nameFormattingEnd;

    /**
     * Constructor
     *
     * @param string $p_number
     * @param string $p_count
     * @param string $p_name
     * @param string $p_content
     * @param string $p_formattingStart
     * @param string $p_formattingEnd
     */
    public function MetaSubtitle($p_number = null, $p_fieldName = null,
    $p_count = null, $p_name = null, $p_content = null, $p_formattingStart = '',
    $p_formattingEnd = '')
    {
        $this->m_number = $p_number;
        $this->m_fieldName = $p_fieldName;
        $this->m_count = $p_count;
        $this->m_name = $p_name;
        $this->m_content = MetaSubtitle::ProcessContent($p_content);
        $this->m_nameFormattingStart = $p_formattingStart;
        $this->m_nameFormattingEnd = $p_formattingEnd;
    }

    /**
     * Returns true if the current object is the same type as the given
     * object then has the same value.
     * @param  mix     $p_otherObject
     * @return boolean
     */
    public function same_as($p_otherObject)
    {
        return get_class($this) == get_class($p_otherObject)
        && $this->m_number == $p_otherObject->m_number
        && $this->m_fieldName == $p_otherObject->m_fieldName
        && $this->m_name == $p_otherObject->m_name;
    }

    public function __get($p_property)
    {
        switch (strtolower($p_property)) {
            case 'number': return $this->m_number;
            case 'field_name': return $this->m_fieldName;
            case 'count': return $this->m_count;
            case 'name': return $this->m_name;
            case 'formatted_name': return $this->getFormattedName();
            case 'content': return $this->m_content;
            case 'has_previous_subtitles': return (int) ($this->m_number > 0);
            case 'has_next_subtitles': return (int) ($this->m_number < ($this->m_count - 1));
            default:
                $this->trigger_invalid_property_error($p_property);

                return;
        }
    }

    /**
     * Returns the formatted subtitle name
     *
     * @param  string $p_formattingStart
     * @param  string $p_formattingEnd
     * @return string
     */
    protected function getFormattedName($p_formattingStart = '<p>', $p_formattingEnd = '</p>')
    {
        $formattingStart = empty($this->m_nameFormattingStart) ? $p_formattingStart : $this->m_nameFormattingStart;
        $formattingEnd = empty($this->m_nameFormattingEnd) ? $p_formattingEnd : $this->m_nameFormattingEnd;

        return $formattingStart.$this->m_name.$formattingEnd;
    }

    /**
     * Reads the subtitles from the given content
     *
     * @param  string $p_content
     * @param  string $p_firstSubtitle
     * @return array  of MetaSubtitle
     */
    public static function ReadSubtitles($p_content, $p_fieldName, $p_firstSubtitle = '',
    $p_headerFormatStart = null, $p_headerFormatEnd = null)
    {
        $result = preg_match_all('/('.MetaSubtitle::GetFindPattern().')/i', $p_content, $subtitlesNames);

        $contentParts = preg_split('/'.MetaSubtitle::GetSplitPattern().'/i', $p_content);
        $subtitlesContents = array();
        foreach ($contentParts as $index => $contentPart) {
            $name = $index > 0 ? $subtitlesNames[3][$index-1] : $p_firstSubtitle;
            if (empty($p_headerFormatStart)) {
                $formatStart = $index > 0 ? $subtitlesNames[2][$index-1] : '';
            } else {
                $formatStart = $p_headerFormatStart;
            }
            if (empty($p_headerFormatEnd)) {
                $formatEnd = $index > 0 ? $subtitlesNames[4][$index-1] : '';
            } else {
                $formatEnd = $p_headerFormatEnd;
            }
            $subtitles[] = new MetaSubtitle($index, $p_fieldName, count($contentParts),
            $name, $contentPart, $formatStart, $formatEnd);
        }

        return $subtitles;
    }

    /**
     * Process the body field content (except subtitles):
     *  - internal links
     *  - image links
     *
     * @param  string $p_content
     * @return string
     */
    public static function ProcessContent($p_content, $articleNumber = null, $articleLanguageNumber = null)
    {
        if (!is_null($articleNumber)) {
            $context = CampTemplate::singleton()->context();
            $uri = $context->url;
            $uri->article = new MetaArticle($articleLanguageNumber, $articleNumber);
        }

        $content = trim($p_content);
        if (empty($content)) {
            return $p_content;
        }
        // process internal links
        $linkPattern = '<!\*\*[\s]*Link[\s]+Internal[\s]+(([\d\w]+[=][\d\w]+&?)*)([\s]+TARGET[\s]+([^>\s]*))*[\s]*>';
        $content = preg_replace_callback("|$linkPattern|i",
                                         array('MetaSubtitle', 'ProcessInternalLink'),
                                         $p_content);
        $endLinkPattern = '<!\*\*[\s]*EndLink[\s]*>';
        $content = preg_replace("|$endLinkPattern|i", '</a>', $content);

        // image tag format: <!** Image 1 align="left" alt="FSF" sub="FSF" attr="value">
        $imagePattern = '<!\*\*[\s]*Image[\s]+([\d]+)(([\s]+(align|alt|sub|width|height|ratio|\w+)\s*=\s*("[^"]*"|[^\s]*))*)[\s]*>';
        $content = preg_replace_callback("/$imagePattern/i",
                                     array('MetaSubtitle', 'ProcessImageLink'),
                                     $content);

        // snippet tag format: <-- Snippet 1 -->
        $snippetPattern = '<\-\-\sSnippet\s([\d]+)\s\-\->';
        $content = preg_replace_callback("/$snippetPattern/i",
                                     array('MetaSubtitle', 'ProcessSnippet'),
                                     $content);

        return $content;
    }

    /**
     * Process the image statement given in Campsite internal formatting.
     * Returns a standard image URL.
     *
     * @param  array  $p_matches
     * @return string
     */
    public static function ProcessImageLink(array $p_matches)
    {
        $context = CampTemplate::singleton()->context();
        $oldImage = $context->image;
        $uri = $context->url;
        if ($uri->article->number == 0) {
            return '';
        }

        $imageNumber = $p_matches[1];
        $detailsString = $p_matches[2];
        $detailsArray = array();
        if (trim($detailsString) != '') {
            $imageAttributes = 'align|alt|sub|width|height|ratio|\w+';
            preg_match_all("/[\s]+($imageAttributes)=\"([^\"]+)\"/i", $detailsString, $detailsArray1);
            $detailsArray1[1] = array_map('strtolower', $detailsArray1[1]);
            if (count($detailsArray1[1]) > 0) {
                $detailsArray1 = array_combine($detailsArray1[1], $detailsArray1[2]);
            } else {
                $detailsArray1 = array();
            }
            preg_match_all("/[\s]+($imageAttributes)=([^\"\s]+)/i", $detailsString, $detailsArray2);
            $detailsArray2[1] = array_map('strtolower', $detailsArray2[1]);
            if (count($detailsArray2[1]) > 0) {
                $detailsArray2 = array_combine($detailsArray2[1], $detailsArray2[2]);
            } else {
                $detailsArray2 = array();
            }
            $detailsArray = array_merge($detailsArray1, $detailsArray2);
        }

        $articleImage = new ArticleImage($uri->article->number, null, $imageNumber);
        $image = new MetaImage($articleImage->getImageId());
        $context->image = $image;

        $imageOptions = '';
        $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');

        if (array_key_exists('sub', $detailsArray)) {
            if ($preferencesService->MediaRichTextCaptions == 'Y') {
                $detailsArray['sub'] = html_entity_decode($detailsArray['sub'], ENT_QUOTES, 'UTF-8');
            } else {
                $detailsArray['sub'] = strip_tags(html_entity_decode($detailsArray['sub'], ENT_QUOTES, 'UTF-8'));
            }
        }

        $defaultOptions = array('ratio' => 'EditorImageRatio', 'width' => 'EditorImageResizeWidth',
        'height' => 'EditorImageResizeHeight', );
        foreach (array('ratio', 'width', 'height') as $imageOption) {
            $defaultOption = (int) $preferencesService->get($defaultOptions[$imageOption]);
            if (isset($detailsArray[$imageOption]) && $detailsArray[$imageOption] > 0) {
                if (isset($detailsArray['size']) && strpos($detailsArray['size'], 'px') !== false) {
                    $detailsArray['percentage'] = '100%';
                    $imageOptions .= " $imageOption ".rtrim($detailsArray['size'], 'px');
                } else {
                    $imageOptions .= " $imageOption ".(int) $detailsArray[$imageOption];
                }
            } elseif ($imageOption != 'ratio' && $defaultOption > 0) {
                $imageOptions .= " $imageOption $defaultOption";
            } elseif ($imageOption == 'ratio' && $defaultOption != 100) {
                $imageOptions .= " $imageOption $defaultOption";
            }
        }
        $imageOptions = trim($imageOptions);

        $imgZoomLink = '';
        if ($preferencesService->EditorImageZoom == 'Y' && strlen($imageOptions) > 0) {
            $imgZoomLink = '/'.$image->filerpath;
        }

        try {
            $copiedUriParameters = $uri->uri_parameter;
            $templatesService = \Zend_Registry::get('container')->getService('newscoop.templates.service');
            $originalVector = $templatesService->getSmarty()->campsiteVector;
            $uri->uri_parameter = "image $imageOptions";
            $templatesService->setVector(array_merge($templatesService->getSmarty()->campsiteVector, array(
                'params' => implode('__', array(
                    $uri->article->number,
                    $imageNumber,
                    $articleImage->getImageId(),
                    $templatesService->getContentImageTemplate()
                ))
            )));

            $image = $templatesService->fetchTemplate($templatesService->getContentImageTemplate(), array(
                'imageDetails' => $detailsArray,
                'MediaRichTextCaptions' => $preferencesService->MediaRichTextCaptions,
                'uri' => $uri,
                'imgZoomLink' => $imgZoomLink
            ));
            $uri->uri_parameter = $copiedUriParameters;
            $templatesService->setVector($originalVector);

            return $image;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Process the Snippet markup and return the Snippet rendered
     *
     * @param  array  $p_matches
     * @return string
     */
    public static function ProcessSnippet(array $p_matches)
    {
        $snippet = '';
        if (array_key_exists(1, $p_matches)) {
            $em = \Zend_Registry::get('container')->getService('doctrine.em');
            $snippet = $em->getRepository('Newscoop\Entity\Snippet')->getSnippetById($p_matches[1]);
            $snippet = $snippet->render();
        }

        return $snippet;
    }

    /**
     * Process the internal link statement given in Campsite internal formatting.
     * Returns a standard URL.
     *
     * @param  array  $p_matches
     * @return string
     */
    public static function ProcessInternalLink(array $p_matches)
    {
        $parametersString = $p_matches[1];
        $targetName = isset($p_matches[4]) ? $p_matches[4] : null;
        preg_match_all('/([\d\w]+)=([\d\w]+)&?/i', $parametersString, $parametersArray);
        $parametersArray = array_combine($parametersArray[1], $parametersArray[2]);

        $uri = new MetaURL();
        $uri->reset_parameters();
        $uri->language = new MetaLanguage($parametersArray['IdLanguage']);
        $uri->publication = new MetaPublication($parametersArray[CampRequest::PUBLICATION_ID]);
        $uri->issue = new MetaIssue($parametersArray[CampRequest::PUBLICATION_ID],
        $parametersArray[CampRequest::LANGUAGE_ID],
        $parametersArray[CampRequest::ISSUE_NR]);
        $uri->section = new MetaSection($parametersArray[CampRequest::PUBLICATION_ID],
        $parametersArray[CampRequest::ISSUE_NR],
        $parametersArray[CampRequest::LANGUAGE_ID],
        $parametersArray[CampRequest::SECTION_NR]);
        $uri->article = new MetaArticle($parametersArray[CampRequest::LANGUAGE_ID],
        $parametersArray[CampRequest::ARTICLE_NR]);
        $urlString = '<a href="'.$uri->url.'" target="'.$targetName.'">';

        return $urlString;
    }

    /**
     * Returns the pattern used to split the content in subtitles
     *
     * @return string
     */
    private static function GetSplitPattern()
    {
        return MetaSubtitle::$m_HeaderStartPattern.MetaSubtitle::$m_SubtitlePattern.MetaSubtitle::$m_HeaderEndPattern;
    }

    /**
     * Returns the pattern used to find a subtitle in the article content field
     *
     * @return string
     */
    private static function GetFindPattern()
    {
        return MetaSubtitle::$m_HeaderStartPattern.MetaSubtitle::$m_SubtitlePattern.MetaSubtitle::$m_HeaderEndPattern;
    }

    protected function trigger_invalid_property_error($p_property, $p_smarty = null)
    {
        $errorMessage = INVALID_PROPERTY_STRING." $p_property "
        .OF_OBJECT_STRING.' subtitle';
        CampTemplate::singleton()->trigger_error($errorMessage, $p_smarty);
    }
}
