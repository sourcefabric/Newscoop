<?php
/**
 * @package Campsite
 */


final class MetaSubtitle {

    static private $m_SubtitlePattern = '<!\*\*[\s]*Title[\s]*>([^<]*)<!\*\*[\s]*EndTitle[\s]*>';

    static private $m_HeaderStartPattern = '(<[\s]*[hH][\d][\s]*>[\s]*)*';

    static private $m_HeaderEndPattern = '([\s]*<[\s]*\/[\s]*[hH][\d][\s]*>)*';

    private $m_name;

    private $m_content;

    private $m_nameFormattingStart;

    private $m_nameFormattingEnd;

    public function MetaSubtitle($p_name = null, $p_content = null,
            $p_formattingStart = '', $p_formattingEnd = '') {
        $this->m_name = $p_name;
        $this->m_content = MetaSubtitle::ProcessContent($p_content);
        $this->m_nameFormattingStart = $p_formattingStart;
        $this->m_nameFormattingEnd = $p_formattingEnd;
    }

    public function getName() {
        return $this->m_name;
    }

    public function getFormattedName($p_formattingStart = '<p>', $p_formattingEnd = '</p>') {
        $formattingStart = empty($this->m_nameFormattingStart) ? $p_formattingStart : $this->m_nameFormattingStart;
        $formattingEnd = empty($this->m_nameFormattingEnd) ? $p_formattingEnd : $this->m_nameFormattingEnd;
        return $formattingStart.$this->m_name.$formattingEnd;
    }

    public function getContent() {
        return $this->m_content;
    }

    public static function ReadSubtitles($p_content, $p_firstSubtitle = '') {
        $result = preg_match_all('/('.MetaSubtitle::GetFindPattern().')/i', $p_content, $subtitlesNames);

        $contentParts = preg_split('/'.MetaSubtitle::GetSplitPattern().'/i', $p_content);
        $subtitlesContents = array();
        foreach ($contentParts as $index=>$contentPart) {
            $name = $index > 0 ? $subtitlesNames[3][$index-1] : $p_firstSubtitle;
            $formatStart = $index > 0 ? $subtitlesNames[2][$index-1] : '';
            $formatEnd = $index > 0 ? $subtitlesNames[4][$index-1] : '';
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

    private static function GetSplitPattern() {
        return MetaSubtitle::$m_HeaderStartPattern.MetaSubtitle::$m_SubtitlePattern.MetaSubtitle::$m_HeaderEndPattern;
    }

    private static function GetFindPattern() {
        return MetaSubtitle::$m_HeaderStartPattern.MetaSubtitle::$m_SubtitlePattern.MetaSubtitle::$m_HeaderEndPattern;
    }
}

?>
