<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @copyright 2008 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */

$_docRoot = dirname(dirname(__FILE__));
require_once($_docRoot.'/classes/TemplateConverterHelper.php');


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
            'has_keyword' => 'has_keyword',
            'name' => 'name',
            'number' => 'number',
            'translated_to' => 'translated_to',
            'type' => array(
                'attribute' => 'type_name'),
            'upload_date' => 'upload_date',
            'hasattachments' => array(
                'attribute' => 'has_attachments'),
            'haskeyword' => array(
                'attribute' => 'has_keyword'),
            'onfrontpage' => array(
                'attribute' => 'on_front_page'),
            'onsection' => array(
                'attribute' => 'on_section_page'),
            'public' => array(
                'attribute' => 'is_public'),
            ),
        'articleattachment' => array(
            'extension' => array(
                'new_object' => 'attachment'),
            'description' => array(
                'new_object' => 'attachment'),
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
            'title' => array(
                'new_object' => 'audioclip'),
            'creator' => array(
                'new_object' => 'audioclip'),
            'genre' => array(
                'new_object' => 'audioclip'),
            'length' => array(
                'new_object' => 'audioclip'),
            'year' => array(
                'new_object' => 'audioclip'),
            'bitrate' => array(
                'new_object' => 'audioclip'),
            'samplerate' => array(
                'new_object' => 'audioclip'),
            'album' => array(
                'new_object' => 'audioclip'),
            'description' => array(
                'new_object' => 'audioclip'),
            'format' => array(
                'new_object' => 'audioclip'),
            'label' => array(
                'new_object' => 'audioclip'),
            'composer' => array(
                'new_object' => 'audioclip'),
            'channels' => array(
                'new_object' => 'audioclip'),
            'rating' => array(
                'new_object' => 'audioclip'),
            'tracknum' => array(
                'new_object' => 'audioclip',
                'attribute' => 'track_no'),
            'disknum' => array(
                'new_object' => 'audioclip',
                'attribute' => 'disk_no'),
            'lyrics' => array(
                'new_object' => 'audioclip'),
            'copyright' => array(
                'new_object' => 'audioclip')
            ),
        'image' => array(),
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
            'column' => array(
                'new_object' => 'current_list'),
            'index' => array(
                'new_object' => 'current_list'),
            'row' => array(
                'new_object' => 'current_list'),
            'end' => array(
                'new_object' => 'current_list',
                'attribute' => 'at_end'),
            'start' => array(
                'new_object' => 'current_list',
                'attribute' => 'at_beginning')
            ),
        'login' => array(
            'action' => array(
                'new_object' => 'login_action',
                'attribute' => 'defined'),
            'error' => array(
                'new_object' => 'login_action',
                'attribute' => 'is_error'),
            'ok' => array(
                'new_object' => 'login_action')
            ),
        'publication' => array(
            'defined' => 'defined',
            'identifier' => 'identifier',
            'name' => 'name'
            ),
        'search' => array(
            'action' => array(
                'new_object' => 'search_articles_action',
                'attribute' => 'defined'),
            'error' => array(
                'new_object' => 'search_articles_action',
                'attribute' => 'is_error'),
            'ok' => array(
                'new_object' => 'search_articles_action')
            ),
        'section' => array(
            'defined' => 'defined',
            'name' => 'name',
            'number' => 'number'
            ),
        'subscription' => array(
            'ok' => array(
                'new_object' => 'edit_subscription_action'),
            'error' => array(
                'new_object' => 'edit_subscription_action',
                'attribute' => 'is_error'),
            'trial' => array(
                'new_object' => 'edit_subscription_action',
                'attribute' => 'is_trial'),
            'paid' => array(
                'new_object' => 'edit_subscription_action',
                'attribute' => 'is_paid'),
            'action' => array(
                'new_object' => 'edit_subscription_action',
                'attribute' => 'defined')
            ),
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
            'isblockedfromcomments' => array(
                'attribute' => 'is_blocked_from_comments'),
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
    private $m_sentences = array(
        'allowed' => array(
            'new_object' => 'article',
            'attribute' => 'content_accessible'),
        'currentsubtitle' => array(
            'new_object' => 'subtitle',
            'attribute' => 'number',
            'condition' => ' == $campsite->article->#field_name#->subtitle_number'),
        'nextitems' => array(
            'new_object' => 'current_list',
            'attribute' => 'has_next_elements'),
        'nextsubtitles' => array(
            'new_object' => 'article',
            'attribute' => 'type->#type_name#->field->has_next_subtitles'),
        'previousitems' => array(
            'new_object' => 'current_list',
            'attribute' => 'has_previous_elements'),
        'prevsubtitles' => array(
            'new_object' => 'article',
            'attribute' => 'type->#type_name#->field->has_previous_subtitles')
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

        $sentence = array_key_exists(strtolower($p_optArray[$idx]), $this->m_sentences) ? strtolower($p_optArray[$idx]) : '';
        $object = array_key_exists(strtolower($p_optArray[$idx]), $this->m_objects) ? strtolower($p_optArray[$idx]) : '';

        $ifBlockStr = '';
        $openBracket = false;
        if (strlen($sentence) > 0) {
            $this->m_ifBlock = $sentence;
            if ($sentence == 'nextsubtitles' || $sentence == 'prevsubtitles') {
                if (TemplateConverterHelper::GetWithArticleType() != '') {
                    $this->m_sentences[$sentence]['attribute'] = preg_replace('/#type_name#/', TemplateConverterHelper::GetWithArticleType(), $this->m_sentences[$sentence]['attribute']);
                }
            } elseif($sentence == 'currentsubtitle') {
                if (TemplateConverterHelper::GetWithBodyField() != '') {
                    $this->m_sentences[$sentence]['condition'] = preg_replace('/#field_name#/', TemplateConverterHelper::GetWithBodyField(), $this->m_sentences[$sentence]['condition']);
                }
            }

            $ifBlockStr.= '->'.$this->m_sentences[$sentence]['new_object'];
            $ifBlockStr.= '->'.$this->m_sentences[$sentence]['attribute'];
            if (isset($this->m_sentences[$sentence]['condition'])) {
                $ifBlockStr.= $this->m_sentences[$sentence]['condition'];
            }
            $idx++;
        }

        if (strlen($object) > 0) {
            $objectIdx = $idx;
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
                    if ($object == 'image' && is_numeric($attribute)) {
                        $ifBlockStr.= '->article->has_image('.$attribute.')';
                    } else {
                        $numElements = sizeof($p_optArray);
                        if (isset($p_optArray[$numElements - 2]) && array_key_exists($p_optArray[$numElements - 2], $this->m_operators)) {
                            $operatorIdx = $numElements - 2;
                            if ($operatorIdx > $objectIdx) {
                                $numIdentifiers = $operatorIdx - $objectIdx;
                                if ($numIdentifiers == 3) {
                                    $type = $attribute;
                                    $attribute = $p_optArray[$idx+1];
                                    $ifBlockStr.= '->'.$object.'->type->'.$type.'->'.$attribute;
                                    $idx++;
                                } elseif ($numIdentifiers == 2) {
                                    $ifBlockStr.= '->'.$object.'->'.$attribute;
                                }
                            }

                        } else {
                            $ifBlockStr.= '->'.$object.'->'.$attribute;
                        }
                    }
                }
            }

            $idx++;
            //
            $operator = (isset($p_optArray[$idx]) && array_key_exists(strtolower($p_optArray[$idx]), $this->m_operators)) ? $this->m_operators[strtolower($p_optArray[$idx])] : '';
            if (strlen($operator) > 0) {
                $ifBlockStr.= ' '.$operator;
                $idx++;
            } elseif ($object == 'list' && ($attribute == 'row'
                                                || $attribute == 'column'
                                                || $attribute == 'index')) {
                $p_optArray[$idx] = isset($p_optArray[$idx]) ? strtolower($p_optArray[$idx]) : '';
                if (isset($p_optArray[$idx]) && ($p_optArray[$idx] == 'odd' || $p_optArray[$idx] == 'even')) {
                    $ifBlockStr.= ' is '.$p_optArray[$idx];
                    $idx++;
                } elseif(isset($p_optArray[$idx])) {
                    $ifBlockStr.= ' == '.$p_optArray[$idx++];
                    for ($i = $idx; $i < sizeof($p_optArray); $i++) {
                        $ifBlockStr.= ' || '.CS_OBJECT.'->current_list->'.$attribute.' == '.$p_optArray[$i];
                        $idx++;
                    }
                    if ($condType == true) {
                        $ifBlockStr.= ')';
                        $openBracket = true;
                    }
                }
            }

            //
            $value = (isset($p_optArray[$idx])) ? $p_optArray[$idx] : null;
            if (isset($p_optArray[$idx])) {
                if (!empty($p_optArray[$idx])) {
                    $value = $p_optArray[$idx];
                } else {
                    $value = '""';
                }
            }
            if (!is_null($value)) {
                if ($value == '""') $value = '';
                $value = (is_numeric($value)) ? $value : '"'.$value.'"';
                $ifBlockStr.= (strlen($operator) <= 0) ? ' == '.$value : ' '.$value;
                $idx++;
            }
        }

        for ($x = $idx; $x < sizeof($p_optArray); $x++) {
            $ifBlockStr.= ' '.$p_optArray[$x];
        }

        if (strlen($ifBlockStr) > 0) {
            $this->m_ifBlockStr = ($condType == true) ? 'if ! ' : 'if ';
            if ($openBracket == true) {
                $this->m_ifBlockStr.= '(';
            }
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
     * @param string[] $p_optArray
     * @return string
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