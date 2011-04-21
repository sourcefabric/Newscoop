<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite blogentry_edit function plugin
 *
 * Type:     function
 * Name:     bloganswer_edit
 * Purpose:  
 *
 * @param array
 *     $p_params the date in unixtime format from $smarty.now
 * @param object
 *     $p_smarty the date format wanted
 *
 * @return
 *     string the html form element
 *     string empty if something is wrong
 */
function smarty_function_blogentry_edit($p_params, &$p_smarty)
{
    global $g_ado_db;

    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    // gets the context variable
    $campsite = $p_smarty->get_template_vars('gimme');
    $html = '';

    if (!isset($p_params['html_code']) || empty($p_params['html_code'])) {
        $p_params['html_code'] = '';
    }

    switch ($p_params['attribute']) {
        case 'title':
            $value = isset($_REQUEST['f_blogentry_title']) ? Input::Get('f_blogentry_title') : $campsite->blogentry->title;
            $html .= "<input type=\"text\" name=\"f_blogentry_title\" value=\"$value\" {$p_params['html_code']} />";
        break;
            
        case 'content':
            $value = isset($_REQUEST['f_blogentry_content']) ? Input::Get('f_blogentry_content') : $campsite->blogentry->content;
            $html .= "<textarea name=\"f_blogentry_content\" id=\"f_blogentry_content\" {$p_params['html_code']} />$value</textarea>";

            if ($p_params['wysiwyg']) {
                $html .='<script language="javascript" type="text/javascript" src="' . $Campsite['WEBSITE_URL'] . '/js/tinymce/tiny_mce.js"></script>'.
                        '<script language="javascript" type="text/javascript">'.
                        '     tinyMCE.init({'.
                        '     	mode : "exact",'.
                        '        elements : "f_blogentry_content",'.
                        '        theme : "advanced",'.
                        '        plugins : "emotions, paste", '.
                        '        paste_auto_cleanup_on_paste : true, '.
                        '        theme_advanced_buttons1 : "bold, italic, underline, undo, redo, link, emotions", '.
                        '        theme_advanced_buttons2 : "", '.
                        '        theme_advanced_buttons3 : "" '.
                        '     });'.
                        '</script>';
            }
        break;
       
        case 'mood':
            $value = isset($_REQUEST['f_blogentry_mood']) ? Input::Get('f_blogentry_mood') : $campsite->blogentry->mood;
            $html .= "<input type=\"text\" name=\"f_blogentry_mood\" value=\"$value\" {$p_params['html_code']} />";
        break; 
    }

    return $html;
} // fn smarty_function_blogentry_edit

?>
