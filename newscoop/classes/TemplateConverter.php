<?php
/**
 * @package Newscoop
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @copyright 2008 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */

$_docRoot = dirname(dirname(__FILE__));
require_once('PEAR.php');
require_once($_docRoot.'/classes/TemplateConverterHelper.php');

define('CS_OPEN_TAG', '{{');
define('CS_CLOSE_TAG', '}}');


/**
 * Class TemplateConverter
 */
class TemplateConverter
{
    /**
     * @var string
     */
    private $m_templateFileName = null;

    /**
     * @var string
     */
    private $m_templatePathDirectory = null;

    /**
     * @var string
     */
    private $m_templateDirectory = null;

    /**
     * @var string
     */
    protected $m_templateOriginalContent = null;

    /**
     * @var string
     */
    protected $m_templateContent = null;

    /**
     * @var array
     */
    private $m_oldTags = array();


    /**
     * Class constructor
     */
    public function __construct() {}


    /**
     * Reads the original template file content.
     *
     * @param string $p_filePath
     *      Full path to the template file
     *
     * @return boolean
     *      True on success, false on failure
     */
    public function read($p_filePath)
    {
        if (!file_exists($p_filePath)) {
            return false;
        }

        if (strtolower(substr($p_filePath, -4)) != '.tpl'
        && strtolower(substr($p_filePath, -4)) != '.htm'
        && strtolower(substr($p_filePath, -5)) != '.html'
        ) {
            return false;
        }

        // sets the template full path directory and template file name
        $this->m_templatePathDirectory = dirname($p_filePath);
        $this->m_templateFileName = basename($p_filePath);
        // sets the relative template directory, if any
        $tplDirPos = strpos($this->m_templatePathDirectory, 'templates/');
        $tplDirLength = strlen('templates/');
        if ($tplDirPos && $tplDir = substr($this->m_templatePathDirectory, $tplDirPos + $tplDirLength)) {
            $this->m_templateDirectory = $tplDir;
        } else {
            $this->m_templateDirectory = null;
        }

        // reads the template file content
        if (!($this->m_templateOriginalContent = @file_get_contents($p_filePath))) {
            return false;
        }

        return true;
    } // fn read


    /**
     * Parses the original template file and replaces old syntax with new one.
     *
     * @return bool
     */
    public function parse()
    {
        // gets all the tags from the original template file
        $pattern = '/<!\*\*\s*([^>]+)>/';
        $this->m_oldTags = $this->getAllTagsFromTemplate($pattern);
        if ($this->m_oldTags == false || sizeof($this->m_oldTags) == 0) {
            return false;
        }

        // sets the tags content (without delimeters)
        $oldTagsContent = $this->m_oldTags[1];
        // inits patterns and replacements arrays
        $patternsArray = array();
        $replacementsArray = array();
        foreach($oldTagsContent as $oldTagContent) {
            // gets single words from tag content (options string)
            $optArray = $this->parseOptionsString($oldTagContent);
            // finds out new tag syntax based on given tag content
            $newTagContent = $this->getNewTagContent($optArray, $oldTagContent);
            if (is_null($newTagContent)) {
                continue;
            }

            // sets pattern and replacement strings
            $pattern = '/<!\*\*\s*'.@preg_quote($oldTagContent).'\s*>/';
            if ($newTagContent == 'DISCARD_SENTENCE') {
                $replacement = '';
            } else {
                $replacement = CS_OPEN_TAG.' '.$newTagContent.' '.CS_CLOSE_TAG;
            }
            $patternsArray[] = $pattern;
            $replacementsArray[] = $replacement;
        }

        // sets pattern and replacement for get_img script
        $patternsArray[] = "/cgi-bin\/get_img/";
        $replacementsArray[] = "get_img.php";

        // replaces all patterns with corresponding replacements
        $this->m_templateContent = @preg_replace($patternsArray,
                                                 $replacementsArray,
                                                 $this->m_templateOriginalContent);

        // replaces templates path properly
        $pattern = '/\/look\/(.*?)[^"\']+/';
        $oldTplTags = $this->getAllTagsFromTemplate($pattern);
        if ($oldTplTags == false || sizeof($oldTplTags) == 0) {
            return false;
        }
        foreach($oldTplTags[0] as $oldTplTag) {
            if (!empty($oldTplTag)) {
                //$pattern = '/(.*?)[^\.tpl]$/';
                $pattern = '/\.tpl/';
                preg_match($pattern, $oldTplTag, $m);
                if (is_array($m) && !empty($m[0])) {
                    $replacement = '/tpl/';
                } else {
                    $replacement = '/templates/';
                }
                $pattern = '/\/look\//';
                $this->m_templateContent = @preg_replace($pattern,
                                                         $replacement,
                                                         $this->m_templateContent,
                                                         1);
            }
        }

        return true;
    } // fn parse


    /**
     * Writes the new template syntax to the output file.
     * Output file might be either the given as parameter or the original file.
     *
     * @param string $p_templateFileName
     *      File name for the template after conversion,
     *      default is the original template file name
     *
     * @return boolean
     *      True on success, false on failure
     */
    public function write($p_templateFileName = null)
    {
        // sets the output file to write to
        if (!is_null($p_templateFileName)) {
            $output = $this->m_templatePathDirectory.'/'.$p_templateFileName;
        } else {
            $output = $this->m_templatePathDirectory.'/'.$this->m_templateFileName;
        }


        if ((file_exists($output) && !is_writable($output))
                 || !is_writable($this->m_templatePathDirectory)) {
            return new PEAR_Error('Could not write template file');
        }

        if (@file_put_contents($output, $this->m_templateContent) == false) {
            return new PEAR_Error('Could not write template file');
        }

        return true;
    } // fn write


    /**
     * Gets all the tags from the source template.
     *
     * @param string $p_pattern
     * @return array $matches
     */
    private function getAllTagsFromTemplate($p_pattern)
    {
        preg_match_all($p_pattern, $this->m_templateOriginalContent, $matches);
        return $matches;
    } // fn getAllTagsFromTemplate


    /**
     * Parses the options string and returns an array of words.
     *
     * @param string $p_optionsString
     *
     * @return array
     */
    private function parseOptionsString($p_optionsString)
    {
        if (empty($p_optionsString)) {
            return array();
        }

        $words = array();
        $escaped = false;
        $lastWord = '';
        $quotedString = '';
        $isOpenQuote = false;
        foreach (str_split($p_optionsString) as $char) {
            if ($char == '"' && !$isOpenQuote) {
                $isOpenQuote = true;
                $quotedString .= $char;
            } elseif (strlen($quotedString) > 0) {
                $quotedString .= $char;
                if ($char == '"') {
                    $words[] = trim(trim($quotedString, '"'));
                    $quotedString = '';
                    $isOpenQuote = false;
                }
            } else {
                if (preg_match('/[\s]/', $char) && !$escaped) {
                    if (!empty($lastWord)) {
                        $words[] = $lastWord;
                        $lastWord = '';
                    }
                } elseif ($char == "\\" && !$escaped) {
                    $escaped = true;
                } else {
                    $lastWord .= $char;
                    $escaped = false;
                }
            }
        }
        if (strlen($lastWord) > 0) {
            $words[] = $lastWord;
        }

        return $words;
    } // fn parseOptionsString


    /**
     * @param array $p_optArray
     */
    private function getNewTagContent($p_optArray, $p_oldTagContent = null)
    {
        if (!is_array($p_optArray) || sizeof($p_optArray) < 1) {
            return;
        }

        $newTag = '';
        $p_optArray[0] = strtolower($p_optArray[0]);

        if ($p_optArray[0] == 'list'|| $p_optArray[0] == 'foremptylist'
                || strpos($p_optArray[0], 'endlist') !== false) {
            $newTag = TemplateConverterListObject::GetNewTagContent($p_optArray);
        } elseif ($p_optArray[0] == 'if' || $p_optArray[0] == 'endif') {
            $newTag = TemplateConverterIfBlock::GetNewTagContent($p_optArray);
        } else {
            if (in_array($p_optArray[0], array('uri','uripath','url','urlparameters'))) {
                $newTag = TemplateConverterHelper::GetNewTagContent($p_optArray);
            } else {
                return TemplateConverterHelper::GetNewTagContent($p_optArray, $this->m_templateDirectory);
            }
        }

        if (strlen($newTag) > 0) {
            $pattern = '/<!\*\*\s*'.@preg_quote($p_oldTagContent).'\s*>/';
            $replacement = CS_OPEN_TAG.' '.$newTag.' '.CS_CLOSE_TAG;
            $this->m_templateOriginalContent = @preg_replace($pattern,
                                                             $replacement,
                                                             $this->m_templateOriginalContent,
                                                             1);
            return null;
        }

        return false;
    } // fn getNewTagContent

} // class TemplateConverter

?>
