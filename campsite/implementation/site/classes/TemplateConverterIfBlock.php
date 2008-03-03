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


/**
 * Class TemplateConverterIfBlock
 */
class TemplateConverterIfBlock
{
    /**
     * @var array
     */
    private $m_objects = array(
        'article' => array(
            'defined' => 'defined',
            'name' => 'name',
            'number' => 'number',
            'translated_to' => 'translated_to',
            'type' => 'type',
            'upload_date' => 'upload_date',
            'hasattachments' => array(
                'attribute' => 'has_attachments'),
            'haskeyword' => array(
                'attribute' => 'has_keyword'),
            'onfrontpage' => array(
                'attribute' => 'on_front_page'),
            'onsectionpage' => array(
                'attribute' => 'on_section_page'),
            'public' => array(
                'attribute' => 'is_public'),
            ),
        'articleattachment' => array(
            'description' => 'description',
            'extension' => 'extension',
            'filename' => array(
                'new_object' => 'attachment',
                'attribute' => 'file_name'),
            'mimetype' => array(
                'new_object' => 'attachment',
                'attribute' => 'mime_type'),
            'sizeb' => array(
                'new_object' => 'attachment',
                'attribute' => 'size_b'),
            'sizekb' => array(
                'new_object' => 'attachment',
                'attribute' => 'size_kb'),
            'sizemb' => array(
                'new_object' => 'attachment',
                'attribute' => 'size_mb'),
            ),
        'articlecomment' => array(
            'captchaenabled' => array(
                'new_object' => 'publication',
                'attribute' => 'captcha_enabled'),
            'defined' => array(
                'new_object' => 'comment',
                'attribute' => 'defined'),
            'enabled' => array(
                'new_object' => 'article',
                'attribute' => 'comments_enabled'),
            'preview' => array(
                'new_object' => 'preview_comment_action',
                'attribute' => 'defined'),
            'publicallowed' => array(
                'new_object' => 'publication',
                'attribute' => 'public_comments'),
            'publicmoderated' => array(
                'new_object' => 'publication',
                'attribute' => 'moderated_comments'),
            'rejected' => array(
                'new_object' => 'submit_comment_action',
                'attribute' => 'rejected'),
            'submitted' => array(
                'new_object' => 'submit_comment_action',
                'attribute' => 'defined'),
            'submiterror' => array(
                'new_object' => 'submit_comment_action',
                'attribute' => 'is_error'),
            'subscribersmoderated' => array(
                'new_object' => 'publication',
                'attribute' => 'moderated_comments')
            ),
        'audioattachment' => array(
            'tracknum' => array(
                'new_object' => 'audioclip',
                'attribute' => 'track_no'),
            'disknum' => array(
                'new_object' => 'audioclip',
                'attribute' => 'disk_no')
            ),
        'image' => array(
            'number' => 'number'),
        'issue' => array(
            'defined' => 'defined',
            'name' => 'name',
            'number' => 'number',
            'publish_date' => 'publish_date',
            'iscurrent' => array(
                'attribute' => 'is_current')
            ),
        'language' => array(
            'code' => 'code',
            'defined' => 'defined',
            'name' => 'name',
            'number' => 'number',
            'englname' => array(
                'attribute' => 'english_name')
            ),
        'list' => array(
            'column' => 'column',
            'index' => 'index',
            'row' => 'row',
            'end' => array(
                'attribute' => 'at_end'),
            'start' => array(
                'attribute' => 'at_beginning')
            ),
        'login',
        'publication' => array(
            'defined' => 'defined',
            'identifier' => 'identifier',
            'name' => 'name'
            ),
        'search',
        'section' => array(
            'defined' => 'defined',
            'name' => 'name',
            'number' => 'number'
            ),
        'subscription',
        'subtitle' => array(
            'number' => 'number'),
        'topic' => array(
            'name' => 'name'),
        'user' => array(
            'defined' => 'defined',
            'addaction' => array(
                'new_object' => 'edit_user_action',
                'attribute' => 'defined'),
            'adderror' => array(
                'new_object' => 'edit_user_action',
                'attribute' => 'is_error'),
            'addok' => array(
                'new_object' => 'edit_user_action',
                'attribute' => 'ok'),
            'blockedfromcomments' => array(
                'attribute' => 'blocked_from_comments'),
            'loggedin' => array(
                'attribute' => 'logged_in'),
            'modifyaction' => array(
                'new_object' => 'edit_user_action',
                'attribute' => 'defined'),
            'modifyerror' => array(
                'new_object' => 'edit_user_action',
                'attribute' => 'is_error'),
            'modifyok' => array(
                'new_object' => 'edit_user_action',
                'attribute' => 'ok')
            )
        );

    /**
     * @var array
     */
    private $m_ifSentences = array(
        'allowed' => array(
            'new_object' => 'article',
            'attribute' => 'content_accesible'),
        'currentsubtitle' => array(
            'new_object' => 'current_list->current',
            'attribute' => 'number',
            'condition' => ''),
        'nextitems' => array(
            'new_object' => 'current_list',
            'attribute' => 'has_next_elements'),
        'nextsubtitles' => array(
            'new_object' => '',
            'attribute' => 'has_next_subtitles'),
        'previousitems' => array(
            'new_object' => 'current_list',
            'attribute' => 'has_previous_elements'),
        'prevsubtitles' => array(
            'new_object' => '',
            'attribute' => 'has_previous_subtitles')
        );

    /**
     * @var string
     */
    private $m_ifBlock = '';

    /**
     * @var string
     */
    private $m_ifBlockStr = '';

    /**
     * @var string
     */
    private $m_endIfBlock = '';

    /**
     * @var array
     */
    private static $m_ifBlockStack = array();

    /**
     * @var array
     */
    private $m_operators = array(
        'is' => '==',
        'not' => '!=',
        'greater' => '>',
        'greater_equal' => '>=',
        'smaller' => '<',
        'smaller_equal' => '<='
        );


    /**
     * @param array $p_optArray
     */
    public function __construct($p_optArray)
    {
        if (!is_array($p_optArray) || sizeof($p_optArray) < 1) {
            return false;
        }

        $this->parse($p_optArray);
    } // fn __construct


    /**
     * @param array $p_optArray
     */
    private function parse($p_optArray)
    {
        if (isset($p_optArray[1]) && strtolower($p_optArray[1]) == 'not') {
            $condType = true;
            $idx = 2;
        } else {
            $condType = false;
            $idx = 1;
        }

        if (!isset($p_optArray[$idx])) {
            return;
        }

        $sentence = array_key_exists(strtolower($p_optArray[$idx]), $this->m_ifSentences) ? strtolower($p_optArray[$idx]) : '';
        $object = array_key_exists(strtolower($p_optArray[$idx]), $this->m_objects) ? strtolower($p_optArray[$idx]) : '';

        $this->m_ifBlockStr = ($condType == true) ? 'if ! ' : 'if ';
        $this->m_ifBlockStr.= CS_OBJECT;
        $ifBlockStr = '';
        if (strlen($sentence) > 0) {
            $this->m_ifBlock = $sentence;
            $ifBlockStr.= '->'.$this->m_ifSentences[$sentence]['new_object'];
            $ifBlockStr.= '->'.$this->m_ifSentences[$sentence]['attribute'];
            if (isset($this->m_ifSentences[$sentence]['condition'])) {
                // process condition
            }
        }

        if (strlen($object) > 0) {
            $idx++;
            $attribute = (isset($p_optArray[$idx])) ? strtolower($p_optArray[$idx]) : '';
            if ($attribute == 'fromstart') {
                $ifBlockStr.= '->default_'.$object.' == '.CS_OBJECT.'->'.$object;
            } else {
                if (array_key_exists($attribute, $this->m_objects[$object])) {
                    if (is_array($this->m_objects[$object][$attribute])) {
                        $ifBlockStr.= (isset($this->m_objects[$object][$attribute]['new_object'])) ? '->'.$this->m_objects[$object][$attribute]['new_object'] : '->'.$object;
                        $ifBlockStr.= (isset($this->m_objects[$object][$attribute]['attribute'])) ? '->'.$this->m_objects[$object][$attribute]['attribute'] : '->'.$attribute;
                    } else {
                        $ifBlockStr.= '->'.$object.'->'.$attribute;
                    }
                } else {
                    $ifBlockStr.= '->'.$object.'->'.$attribute;
                }
            }

            $idx++;
            //
            $operator = (isset($p_optArray[$idx]) && array_key_exists(strtolower($p_optArray[$idx]), $this->m_operators)) ? $this->m_operators[strtolower($p_optArray[$idx])] : '';
            if (strlen($operator) > 0) {
                $ifBlockStr.= ' '.$operator;
                $idx++;
            }

            //
            $value = (isset($p_optArray[$idx])) ? $p_optArray[$idx] : null;
            if (!is_null($value)) {
                $ifBlockStr.= (strlen($operator) < 0) ? ' == '.$value : ' '.$value;
            }
        }

        if (strlen($ifBlockStr) > 0) {
            $this->m_ifBlockStr = ($condType == true) ? 'if ! ' : 'if ';
            $this->m_ifBlockStr.= CS_OBJECT . $ifBlockStr;
        }

        if (!empty($this->m_ifBlockStr)) {
            $this->m_endIfBlock = '/if';
        }
    } // fn parse


    /**
     *
     */
    public function getEndIfBlock()
    {
        return $this->m_endIfBlock;
    } // fn getEndIfBlock


    /**
     *
     */
    public function getIfBlock()
    {
        return $this->m_ifBlock;
    } // fn getIfBlockString


    /**
     *
     */
    public function getIfBlockString()
    {
        return $this->m_ifBlockStr;
    } // fn getIfBlockString


    /**
     *
     */
    public function GetNewTagContent($p_optArray)
    {
        $newTag = '';
        $maxIndex = sizeof(self::$m_ifBlockStack) ? sizeof(self::$m_ifBlockStack) - 1 : 0;
        if ($p_optArray[0] == 'if') {
            self::$m_ifBlockStack[] = new TemplateConverterIfBlock($p_optArray);
            $maxIndex = sizeof(self::$m_ifBlockStack) ? sizeof(self::$m_ifBlockStack) - 1 : 0;
            $newTag = self::$m_ifBlockStack[$maxIndex]->getIfBlockString();
        } elseif (strpos($p_optArray[0], 'endif') !== false) {
            if (isset(self::$m_ifBlockStack[$maxIndex])
                    && is_object(self::$m_ifBlockStack[$maxIndex])) {
                $newTag = self::$m_ifBlockStack[$maxIndex]->getEndIfBlock();
                array_pop(self::$m_ifBlockStack);
            }
        }

        return $newTag;
    } // fn GetNewTagContent


    public static function GetIfBlockStack()
    {
        return self::$m_ifBlockStack;
    } // fn GetIfBlockStack

} // class TemplateConverterIfBlock

?>