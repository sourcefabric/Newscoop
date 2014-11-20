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
require_once($_docRoot.'/classes/TemplateConverterListObject.php');
require_once($_docRoot.'/classes/TemplateConverterIfBlock.php');

define('CS_OBJECT', '$campsite');


/**
 * Class TemplateConverterHelper
 */
class TemplateConverterHelper
{
    /**
     * @var array
     */
    private static $m_exceptions = array(
        'article' => array(
            'date' => array(
                'attribute' => 'date'),
            'type' => array(
                'attribute' => 'type_name'),
            'mon_nr' => array(
                'attribute' => 'mon'),
            'wday_nr' => array(
                'attribute' => 'wday'),
            'upload_date' => array(
                'attribute' => 'creation_date'),
            'uploaddate' => array(
                'attribute' => 'creation_date'),
            'publishdate' => array(
                'attribute' => 'publish_date')
            ),
        'articlecomment' => array(
            'readeremail' => array(
                'new_object' => 'comment',
                'attribute' => 'reader_email'),
            'readeremailobfuscated' => array(
                'new_object' => 'comment',
                'attribute' => 'reader_email|obfuscate_email'),
            'submitdate' => array(
                'new_object' => 'comment',
                'attribute' => 'submit_date'),
            'readeremailpreview' => array(
                'new_object' => 'preview_comment_action',
                'attribute' => 'reader_email'),
            'readeremailpreviewobfuscated' => array(
                'new_object' => 'preview_comment_action',
                'attribute' => 'reader_email|obfuscate_email'),
            'subjectpreview' => array(
                'new_object' => 'preview_comment_action',
                'attribute' => 'subject'),
            'contentpreview' => array(
                'new_object' => 'preview_comment_action',
                'attribute' => 'content'),
            'count' => array(
                'new_object' => 'article',
                'attribute' => 'comment_count'),
            'submiterror' => array(
                'new_object' => 'submit_comment_action',
                'attribute' => 'error_message'),
            'submiterrorno' => array(
                'new_object' => 'submit_comment_action',
                'attribute' => 'error_code')
            ),
        'articleattachment' => array(
            'extension' => array(
                'new_object' => 'attachment',
                'attribute' => 'extension'),
            'description' => array(
                'new_object' => 'attachment',
                'attribute' => 'description'),
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
                'attribute' => 'size_mb')
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
            'number' => array(
                'attribute' => 'article_index'),
            'mon_nr' => array(
                'attribute' => 'mon'),
            'wday_nr' => array(
                'attribute' => 'wday')
            ),
        'issue' => array(
            'date' => array(
                'attribute' => 'date'),
            'mon_nr' => array(
                'attribute' => 'mon'),
            'wday_nr' => array(
                'attribute' => 'wday'),
            'iscurrent' => array(
                'attribute' => 'is_current')
            ),
        'language' => array(
            'englname' => array (
                'attribute' => 'english_name')
            ),
        'login' => array(
            'error' => array(
                'new_object' => 'login_action',
                'attribute' => 'error_message')
            ),
        'search' => array(
            'error' => array(
                'new_object' => 'search_articles_action',
                'attribute' => 'error_message'),
            'keywords' => array(
                'new_object' => 'search_articles_action',
                'attribute' => 'search_keywords')
            ),
        'subscription' => array(
            'expdate' => array(
                'new_object' => 'user->subscription',
                'attribute' => 'expiration_date'),
            'unit' => array(
                'new_object' => 'publication',
                'attribute' => 'subscription_time_unit'),
            'unitcost' => array(
                'new_object' => 'publication',
                'attribute' => 'subscription_unit_cost'),
            'currency' => array(
                'new_object' => 'publication',
                'attribute' => 'subscription_currency'),
            'trialtime' => array(
                'new_object' => 'publication',
                'attribute' => 'subscription_trial_time'),
            'paidtime' => array(
                'new_object' => 'publication',
                'attribute' => 'subscription_paid_time'),
            'error' => array(
                'new_object' => 'edit_subscription_action',
                'attribute' => 'error_message')
            ),
        'user' => array(
            'straddress' => array (
                'attribute' => 'str_address'),
            'phone2' => array(
                'attribute' => 'second_phone'),
            'postalcode' => array(
                'attribute' => 'postal_code'),
            'addok' => array(
                'attribute' => 'add_ok'),
            'modifyok' => array(
                'attribute' => 'modify_ok'),
            'adderror' => array(
                'new_object' => 'edit_user_action',
                'attribute' => 'error_message'),
            'modifyerror' => array(
                'new_object' => 'edit_user_action',
                'attribute' => 'error_message')
            )
        );

    /**
     * @var array
     */
    private static $m_envObjects = array(
        'language','publication','issue','section','article',
        'topic','articlecomment'
        );

    /**
     * @var array
     */
    private static $m_printEx = array(
        'articleattachment' => 'attachment',
        'articlecomment' => 'comment',
        'audioattachment' => 'audioclip'
        );

    /**
     * @var array
     */
    private static $m_urXFuncs = array(
        'uri','uripath','url','urlparameters','formparameters');

    /**
     * @var array
     */
    private static $m_simpleForms = array('login','search','user');

    /**
     * @var array
     */
    private static $m_endForms = array(
        'endarticlecomment','endlogin','endsearch', 'endsubscription','enduser'
        );

    /**
     * @var array
     */
    private static $m_operators = array(
        'is' => '==',
        'not' => '!=',
        'greater' => '>',
        'greater_equal' => '>=',
        'smaller' => '<',
        'smaller_equal' => '<='
        );

    /**
     * @var string
     */
    private static $m_withArticleType = '';

    /**
     * @var string
     */
    private static $m_withBodyField = '';


    /**
     *
     */
    public static function GetWithArticletype()
    {
        return self::$m_withArticleType;
    } // fn GetWithArticleType


    /**
     *
     */
    public static function GetWithBodyField()
    {
        return self::$m_withBodyField;
    } // fn GetWithBodyField


    /**
     * @param string[] $p_optArray
     * @param string $p_tplPath
     */
    public static function GetNewTagContent($p_optArray, $p_tplPath = null)
    {
        if (!is_array($p_optArray) || sizeof($p_optArray) < 1) {
            continue;
        }

        $newTag = '';
        $p_optArray[0] = strtolower($p_optArray[0]);

        // <!** Print statement ... > to {{ $campsite->statement ... }}
        if ($p_optArray[0] == 'print') {
            return self::BuildPrintStatement($p_optArray);
        }

        // Environmental Objects
        // <!** Publication name ... > to {{ set_publication name= ... }}
        // <!** Issue off ... > to {{ unset_issue }}
        if (in_array($p_optArray[0], self::$m_envObjects)) {
            return self::BuildEnvironmentalStatement($p_optArray);
        }

        // URI and URL Functions
        // <!** URI> to {{ uri }}
        // <!** URLParameters section> to {{ urlparameters options="section" }}
        if (in_array($p_optArray[0], self::$m_urXFuncs)) {
            return self::BuildUrxStatement($p_optArray);
        }

        // User, Search and Login Forms
        // <!** Search do-search.tpl Go>
        // to
        // {{ search_form template="do-search.tpl" submit_button="Go" }}
        if (in_array($p_optArray[0], self::$m_simpleForms)) {
            return self::BuildSimpleFormStatement($p_optArray);
        }

        // Article Comment Form
        // <!** ArticleCommentForm ArtCommForm.tpl Send Preview>
        // to
        // {{ article_comment_form template="ArtCommForm.tpl"
        //    submit_button="Send" preview_button="Preview" }}
        if ($p_optArray[0] == 'articlecommentform') {
            return self::BuildArticleCommentFormStatement($p_optArray);
        }

        // Subscription Comment Form
        // <!** Subscription by_section do-subsc.tpl Subscribe>
        // to
        // {{ subscription_form type="by_section" template="do-subsc.tpl"
        //    submit_button="Subscribe" }}
        if ($p_optArray[0] == 'subscription') {
            return self::BuildSubscriptionFormStatement($p_optArray);
        }

        // End forms
        // <!** EndSearch> to {{ /search_form }}
        if (in_array($p_optArray[0], self::$m_endForms)) {
            return self::BuildEndFormStatement($p_optArray);
        }

        // Edit form fields
        // <!** Edit Login uname>
        // <!** Edit Search keywords>
        if ($p_optArray[0] == 'edit') {
            return self::BuildEditStatement($p_optArray);
        }

        // Select form fields
        // <!** Select Login RememberLogin>
        // to
        // {{ camp_select object="login" attribute="rememberlogin" }}
        //
        // <!** Select User gender male female>
        // to
        // {{ camp_select object="user" attribute="gender" male_name="M" female_name="M" }}
        if ($p_optArray[0] == 'select') {
            return self::BuildSelectStatement($p_optArray);
        }

        // HTML Encoding
        // <!** HTMLEncoding> to {{ enable_html_encoding }}
        // <!** HTMLEncoding off> to {{ disable_html_encoding }}
        if ($p_optArray[0] == 'htmlencoding') {
            return self::BuildHTMLEncodingStatement($p_optArray);
        }

        if ($p_optArray[0] == 'with' || $p_optArray[0] == 'endwith') {
            return self::BuildWithStatement($p_optArray);
        }

        switch ($p_optArray[0]) {
        // <!** Date ... > to {{ $smarty.now|camp_date_format:" ... " }}
        case 'date':
            $newTag = '$smarty.now|camp_date_format:"' . $p_optArray[1] . '"';
            break;
        // <!** include header.tpl> to {{ include file="header.tpl" }}
        // <!** include article.tpl> to {{ include file="news/article.tpl" }}
        case 'include':
            $filePath = (!is_null($p_tplPath)) ? $p_tplPath.'/'.$p_optArray[1]
                : $p_optArray[1];
            $newTag = 'include file="' . $filePath . '"';
            break;
        // <!** local> to {{ local }}
        case 'local':
            $newTag = 'local';
            break;
        // <!** endLocal> to {{ /local }}
        case 'endlocal':
            $newTag = '/local';
            break;
        // <!** else> to {{ /else }}
        case 'else':
            $newTag = 'else';
            break;
        }
        
        return $newTag;
    } // fn GetNewTagContent


    /**
     * @param string[] $p_optArray
     *
     * @return string $newTag
     */
    public static function BuildPrintStatement($p_optArray)
    {
        $newTag = CS_OBJECT;
        $object = strtolower($p_optArray[1]);
        if (array_key_exists($object, self::$m_exceptions)
                && array_key_exists(strtolower($p_optArray[2]), self::$m_exceptions[$object])) {
            $e = self::$m_exceptions[$object][strtolower($p_optArray[2])];
            $newTag .= (isset($e['new_object'])) ? '->'.$e['new_object'] : '->'.strtolower($p_optArray[1]);
            $newTag .= (isset($e['attribute'])) ? '->'.$e['attribute'] : '';
            if ($e['attribute'] == 'date' || $e['attribute'] == 'creation_date'
                    || $e['attribute'] == 'publish_date') {
                $newTag.= (isset($p_optArray[3])) ? '|camp_date_format:"'.$p_optArray[3].'"' : '';
            }
        } elseif ($object == 'captcha') {
            $newTag = 'captcha_image_link';
        } else {
            for($i = 1; $i < sizeof($p_optArray); $i++) {
                if (array_key_exists(strtolower($p_optArray[$i]), self::$m_printEx)) {
                    $p_optArray[$i] = self::$m_printEx[strtolower($p_optArray[$i])];
                }
                if ($object == 'article' && $i > 2 && $i == (sizeof($p_optArray) - 1)
                && strstr($p_optArray[$i], '%') !== false) {
                    // date/time format string
                    $newTag .= '|camp_date_format:"'.$p_optArray[$i].'"';
                } elseif ($p_optArray[$i] == 'image' && is_numeric($p_optArray[$i+1])) {
                    $newTag .= '->article->' . strtolower($p_optArray[$i]) . $p_optArray[$i+1];
                    $i += 1;
                } elseif ($object == 'article'
                && strtolower($p_optArray[$i]) == 'firstparagraph') {
                    $newTag .= '->first_paragraph';
                } else {
                    $newTag .= '->' . strtolower($p_optArray[$i]);
                }
            }
        }
        
        return $newTag;    
    } // fn BuildPrintStatement


    /**
     * @param string[] $p_optArray
     *
     * @return string $newTag
     */
    public static function BuildEnvironmentalStatement($p_optArray)
    {
        if (strtolower($p_optArray[1]) == 'off') {
            if ($p_optArray[0] == 'articlecomment') {
                $p_optArray[0] = 'comment';
            }
            $newTag = 'unset_' . $p_optArray[0];
        } else {
            if (strtolower($p_optArray[1]) == 'current') {
                $newTag = 'set_current_'.$p_optArray[0];
            } elseif (strtolower($p_optArray[1]) == 'default') {
                $newTag = 'set_default_'.$p_optArray[0];
            } else {
                $newTag = 'set_' . $p_optArray[0];
                if ($p_optArray[0] == 'language') {
                    $newTag.= ' name="' . strtolower($p_optArray[1]) . '"';
                } else {
                    if (preg_match('/ /', $p_optArray[1])) {
                        $p_optArray[1] = '"'.$p_optArray[1].'"';
                    }
                    $newTag.= (isset($p_optArray[1])) ? ' ' . $p_optArray[1] : '';
                    $newTag.= (isset($p_optArray[2])) ? '="' . $p_optArray[2] . '"' : '';
                }
            }
        }

        return $newTag;
    } // fn BuildEnvironmentalStatement


    /**
     * @param string[] $p_optArray
     *
     * @return string $newTag
     */
    public static function BuildUrxStatement($p_optArray)
    {
        $newTag = $p_optArray[0];
        $ifBlockStack = TemplateConverterIfBlock::GetIfBlockStack();
        $ifBlockStackSize = sizeof($ifBlockStack);
        if ($ifBlockStackSize > 0) {
            $option = '';
            $idx = $ifBlockStackSize - 1;
            switch($ifBlockStack[$idx]->getIfBlock()) {
            case 'nextitems':
                $option = 'next_items'; break;
            case 'previous_items':
                $option = 'previous_items'; break;
            case 'nextsubtitles':
                $withField = self::GetWithBodyField();
                $option = 'next_subtitle';
                $option.= (strlen($withField) > 0) ? ' '.$withField : '';
                break;
            case 'prevsubtitles':
                $withField = self::GetWithBodyField();
                $option = 'previous_subtitle';
                $option.= (strlen($withField) > 0) ? ' '.$withField : '';
                break;
            }
            $newTag.= (strlen($option) > 0) ? ' options="'.$option.'"' : '';
        }

        if (sizeof($p_optArray) > 1) {
            $newTag.= ' options="';
            for ($x = 1; $x < sizeof($p_optArray); $x++) {
                $newTag.= ($x > 1) ? ' ' : '';
                $newTag.= strtolower($p_optArray[$x]);
            }
            $newTag.= '"';
        }

        return $newTag;
    } // fn BuildUrxStatement


    /**
     * @param string[] $p_optArray
     *
     * @return string $newTag
     */
    public static function BuildSimpleFormStatement($p_optArray)
    {
        $newTag = $p_optArray[0] . '_form';
        if ($p_optArray[0] == 'user') {
            $tmpArr = array();
            $tmpArr[0] = $p_optArray[0];
            for ($i = 1; $i < sizeof($p_optArray); $i++) {
                if (isset($p_optArray[$i+1])) {
                    $tmpArr[$i] = $p_optArray[$i+1];
                }
            }
            $p_optArray = $tmpArr;
        }
        $newTag.= (isset($p_optArray[1])) ? ' template="' . $p_optArray[1] . '"' : '';
        $newTag.= (isset($p_optArray[2])) ? ' submit_button="' . $p_optArray[2] . '"' : '';
        $newTag.= (isset($p_optArray[3])) ? ' html_code="' . $p_optArray[3] . '"' : '';

        return $newTag;
    } // fn BuildSimpleFormStatement


    /**
     * @param string[] $p_optArray
     *
     * @return string $newTag
     */
    public static function BuildArticleCommentFormStatement($p_optArray)
    {
        $newTag = 'article_comment_form';
        $newTag.= (isset($p_optArray[1])) ? ' template="'.$p_optArray[1].'"' : '';
        $newTag.= (isset($p_optArray[2])) ? ' submit_button="'.$p_optArray[2].'"' : '';
        $newTag.= (isset($p_optArray[3])) ? ' preview_button="'.$p_optArray[3].'"' : '';

        return $newTag;
    } // fn BuildArticleCommentFormStatement


    /**
     * @param string[] $p_optArray
     *
     * @return string $newTag
     */
    public static function BuildSubscriptionFormStatement($p_optArray)
    {
        $newTag = 'subscription_form';
        $newTag.= (isset($p_optArray[1])) ? ' type="'.$p_optArray[1].'"' : '';
        $newTag.= (isset($p_optArray[2])) ? ' template="'.$p_optArray[2].'"' : '';
        $newTag.= (isset($p_optArray[3])) ? ' submit_button="'.$p_optArray[3].'"' : '';
        $newTag.= (isset($p_optArray[4])) ? ' total_field="'.$p_optArray[4].'"' : '';
        $newTag.= (isset($p_optArray[5])) ? ' evaluated_field="'.$p_optArray[5].'"' : '';

        return $newTag;
    } // fn BuildArticleCommentFormStatement


    /**
     * @param string[] $p_optArray
     *
     * @return string $newTag
     */
    public static function BuildEndFormStatement($p_optArray)
    {
        $newTag = '/' . substr($p_optArray[0], 3) . '_form';
        return $newTag;
    } // fn BuildEndFormStatement


    /**
     * @param string[] $p_optArray
     *
     * @return string $newTag
     */
    public static function BuildEditStatement($p_optArray)
    {
        $newTag = 'camp_edit';
        $newTag.= (isset($p_optArray[1])) ? ' object="'.strtolower($p_optArray[1]).'"' : '';
        $newTag.= (isset($p_optArray[2])) ? ' attribute="'.strtolower($p_optArray[2]).'"' : '';
        if (isset($p_optArray[3])) {
            if (is_integer($p_optArray[3])) {
                $newTag.= ' size="'.$p_optArray[3].'"';
            } elseif (strtolower($p_optArray[3]) == 'html') {
                $newTag.= ' html_code="';
                $newTag.= (isset($p_optArray[4])) ? $p_optArray[4].'"' : '"';
            }
        }

        return $newTag;
    } // fn BuildEditStatement


    /**
     * @param string[] $p_optArray
     *
     * @return string $newTag
     */
    public static function BuildSelectStatement($p_optArray)
    {
        $newTag = 'camp_select';
        $newTag.= (isset($p_optArray[1])) ? ' object="'.strtolower($p_optArray[1]).'"' : '';
        $newTag.= (isset($p_optArray[2])) ? ' attribute="'.strtolower($p_optArray[2]).'"' : '';
        if (strtolower($p_optArray[2]) == 'gender') {
            $newTag.= (isset($p_optArray[3])) ? ' male_name="'.strtolower($p_optArray[3]).'"' : '';
            $newTag.= (isset($p_optArray[4])) ? ' female_name="'.strtolower($p_optArray[4]).'"' : '';
        }

        return $newTag;
    } // fn BuildSelectStatement


    /**
     * @param string[] $p_optArray
     *
     * @return string $newTag
     */
    public static function BuildHTMLEncodingStatement($p_optArray)
    {
        if (isset($p_optArray[1]) && strtolower($p_optArray[1]) == 'off') {
            $newTag = 'disable_html_encoding';
        } else {
            $newTag = 'enable_html_encoding';
        }

        return $newTag;
    } // fn BuildHTMLEncodingStatement


    /**
     * @param string[] $p_optArray
     *
     * @return string $newTag
     */
    public static function BuildWithStatement($p_optArray)
    {
        if ($p_optArray[0] == 'with') {
            if (isset($p_optArray[1])) {
                self::$m_withArticleType = strtolower($p_optArray[1]);
            }
            if (isset($p_optArray[2])) {
                self::$m_withBodyField = strtolower($p_optArray[2]);
            }
        } elseif ($p_optArray[0] == 'endwith') {
            self::$m_withArticleType = '';
            self::$m_withBodyField = '';
        }
        $newTag = 'DISCARD_SENTENCE';

        return $newTag;
    } // fn BuildWithStatement

} // class TemplateConverterHelper

?>