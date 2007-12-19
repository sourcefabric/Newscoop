<?php
/**
 * @package Campsite
 */


final class MetaSubtitle {

    /**
     * The pattern used to detect the subtitles.
     *
     * @var string
     */
    static private $m_SubtitlePattern = '<!\*\*[\s]*Title[\s]*>([^<]*)<!\*\*[\s]*EndTitle[\s]*>';

    /**
     * The pattern used to detect the subtitle formatting start.
     *
     * @var string
     */
    static private $m_HeaderStartPattern = '(<[\s]*[hH][\d][\s]*>[\s]*)*';

    /**
     * The pattern used to detect the subtitle formatting end.
     *
     * @var string
     */
    static private $m_HeaderEndPattern = '([\s]*<[\s]*\/[\s]*[hH][\d][\s]*>)*';

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
     * @param string $p_name
     * @param string $p_content
     * @param string $p_formattingStart
     * @param string $p_formattingEnd
     */
    public function MetaSubtitle($p_name = null, $p_content = null,
            $p_formattingStart = '', $p_formattingEnd = '') {
        $this->m_name = $p_name;
        $this->m_content = MetaSubtitle::ProcessContent($p_content);
        $this->m_nameFormattingStart = $p_formattingStart;
        $this->m_nameFormattingEnd = $p_formattingEnd;
    }


    /**
     * Returns the subtitle name
     *
     * @return string
     */
    public function getName() {
        return $this->m_name;
    }


    /**
     * Returns the formatted subtitle name
     *
     * @param string $p_formattingStart
     * @param string $p_formattingEnd
     * @return string
     */
    public function getFormattedName($p_formattingStart = '<p>', $p_formattingEnd = '</p>') {
        $formattingStart = empty($this->m_nameFormattingStart) ? $p_formattingStart : $this->m_nameFormattingStart;
        $formattingEnd = empty($this->m_nameFormattingEnd) ? $p_formattingEnd : $this->m_nameFormattingEnd;
        return $formattingStart.$this->m_name.$formattingEnd;
    }


    /**
     * Returns the subtitle content
     *
     * @return string
     */
    public function getContent() {
        return $this->m_content;
    }


    /**
     * Reads the subtitles from the given content
     *
     * @param string $p_content
     * @param string $p_firstSubtitle
     * @return array of MetaSubtitle
     */
    public static function ReadSubtitles($p_content, $p_firstSubtitle = '',
                                         $p_headerFormatStart = null,
                                         $p_headerFormatEnd = null) {
        $result = preg_match_all('/('.MetaSubtitle::GetFindPattern().')/i', $p_content, $subtitlesNames);

        $contentParts = preg_split('/'.MetaSubtitle::GetSplitPattern().'/i', $p_content);
        $subtitlesContents = array();
        foreach ($contentParts as $index=>$contentPart) {
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
            $subtitles[] = new MetaSubtitle($name, $contentPart, $formatStart, $formatEnd);
        }
        return $subtitles;
    }


    /**
     * Process the body field content (except subtitles):
     *  - internal links
     *  - image links
     *
     * @param string $p_content
     * @return string
     */
    private static function ProcessContent($p_content) {
        // process internal links
        $linkPattern = '<!\*\*[\s]*Link[\s]+Internal[\s]+(([\d\w]+[=][\d\w]+&?)*)[\s]+(TARGET[\s]+([^>\s]*)[\s]*)*>([^<\s]*)<!\*\*[\s]*EndLink[\s]*>';
        $content = preg_replace_callback("|$linkPattern|i",
                                         'MetaSubtitle::ProcessInternalLink',
                                         $p_content);

//      image tag format: <!** Image 1 align="left" alt="FSF" sub="FSF">
        $imagePattern = '<!\*\*[\s]*Image[\s]+([\d]+)(([\s]+(align|alt|sub)="?[^"\s]+"?)*)[\s]*>';
        return preg_replace_callback("/$imagePattern/i",
                                     'MetaSubtitle::ProcessImageLink',
                                     $content);
    }


    /**
     * Process the image statement given in Campsite internal formatting.
     * Returns a standard image URL.
     *
     * @param array $p_matches
     * @return string
     */
    public static function ProcessImageLink(array $p_matches) {
        $uri = CampTemplate::singleton()->context()->url;
        if ($uri->article->number == 0) {
            return '';
        }

        $imageNumber = $p_matches[1];
        $detailsString = $p_matches[2];
        preg_match_all('/[\s]+(align|alt|sub)="?([^"\s]+)"?/i', $detailsString, $detailsArray);
        $detailsArray[1] = array_map('strtolower', $detailsArray[1]);
        $detailsArray = array_combine($detailsArray[1], $detailsArray[2]);

        $imgString = '<table border="0" cellspacing="0" cellpadding="0" class="cs_img"';
        if (isset($detailsArray['align'])) {
            $imgString .= ' align="' . $detailsArray['align'] . '"';
        }
        $imgString .= '>';
        $imgString .= '<tr><td align="center">';
        $imgString .= '<img src="/get_img?NrArticle=' . $uri->article->number
                    . '&amp;NrImage=' . $imageNumber;
        if (isset($detailsArray['alt'])) {
            $imgString .= ' alt="' . $detailsArray['alt'] . '" ';
        }
        $imgString .= 'border="0" hspace="5" vspace="5">';
        $imgString . '</td>';
        $imgString .= '</tr>';
        if (isset($detailsArray['sub'])) {
            $imgString .= '<tr><td align="center" class="caption">'
                       . $detailsArray['sub'] . '</td></tr>';
        }
        $imgString .= '</table>';
        return $imgString;
    }


    /**
     * Process the internal link statement given in Campsite internal formatting.
     * Returns a standard URL.
     *
     * @param array $p_matches
     * @return string
     */
    public static function ProcessInternalLink(array $p_matches) {
        $parametersString = $p_matches[1];
        $targetName = $p_matches[4];
        $linkText = $p_matches[5];
        preg_match_all('/([\d\w]+)=([\d\w]+)&?/i', $parametersString, $parametersArray);
        $parametersArray = array_combine($parametersArray[1], $parametersArray[2]);

        $uri = CampTemplate::singleton()->context()->url;
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
        if ($uri->publication->identifier == CampRequest::GetVar(CampRequest::PUBLICATION_ID)) {
            $linkContent = $uri->getURI();
        } else {
            $linkContent = $uri->getURL();
        }
        $urlString = '<a href="' . $linkContent . '" target="' . $targetName
                    . '">' . $linkText . '</a>';
        return $urlString;
    }


    /**
     * Returns the pattern used to split the content in subtitles
     *
     * @return string
     */
    private static function GetSplitPattern() {
        return MetaSubtitle::$m_HeaderStartPattern.MetaSubtitle::$m_SubtitlePattern.MetaSubtitle::$m_HeaderEndPattern;
    }


    /**
     * Returns the pattern used to find a subtitle in the article content field
     *
     * @return string
     */
    private static function GetFindPattern() {
        return MetaSubtitle::$m_HeaderStartPattern.MetaSubtitle::$m_SubtitlePattern.MetaSubtitle::$m_HeaderEndPattern;
    }
}

?>
