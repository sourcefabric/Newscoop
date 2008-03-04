<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @copyright 2008 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */

$_docRoot = dirname(dirname(__FILE__));
require_once($_docRoot.'/include/pear/PEAR.php');
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
    private $m_templateDirectory = null;

    /**
     * @var string
     */
    private $m_templateOriginalContent = null;

    /**
     * @var string
     */
    private $m_templateContent = null;

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

        // sets template full path directory and file name
        $this->m_templateDirectory = dirname($p_filePath);
        $this->m_templateFileName = basename($p_filePath);

        // reads the template file content
        if (!($this->m_templateOriginalContent = @file_get_contents($p_filePath))) {
            return false;
        }

        return true;
    } // fn read


    /**
     * Parses the original template file and replaces old syntax with new one.
     * 
     * @return array $replaceArray
     */
    public function parse()
    {
        // gets all the tags from the original template file
        $this->m_oldTags = $this->getAllTagsFromTemplate();
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
            $pattern = '/<!\*\*\s*'.preg_quote($oldTagContent).'\s*>/';
            if ($newTagContent == 'DISCARD_SENTENCE') {
                $replacement = '';
            } else {
                $replacement = CS_OPEN_TAG.' '.$newTagContent.' '.CS_CLOSE_TAG;
            }
            $patternsArray[] = $pattern;
            $replacementsArray[] = $replacement;
        }

        // replaces all patterns with corresponding replacements
        $this->m_templateContent = preg_replace($patternsArray,
                                                $replacementsArray,
                                                $this->m_templateOriginalContent);

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
            $output = $this->m_templateDirectory.'/'.$p_templateFileName;
        } else {
            $output = $this->m_templateDirectory.'/'.$this->m_templateFileName;
        }


        if ((file_exists($output) && !is_writable($output))
                 || !is_writable($this->m_templateDirectory)) {
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
     * @return array $matches
     */
    private function getAllTagsFromTemplate()
    {
        preg_match_all('/<!\*\*\s*([^>]+)>/', $this->m_templateOriginalContent, $matches);
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
            return TemplateConverterHelper::GetNewTagContent($p_optArray);
        }

        if (strlen($newTag) > 0) {
            $pattern = '/<!\*\*\s*'.preg_quote($p_oldTagContent).'\s*>/';
            $replacement = CS_OPEN_TAG.' '.$newTag.' '.CS_CLOSE_TAG;
            $this->m_templateOriginalContent = preg_replace($pattern,
                                                            $replacement,
                                                            $this->m_templateOriginalContent,
                                                            1);
            return null;
        }

        return false;
    } // fn getNewTagContent

} // class TemplateConverter

?>