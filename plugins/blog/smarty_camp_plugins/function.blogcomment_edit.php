<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite blogcomment_edit function plugin
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
function smarty_function_blogcomment_edit($p_params, &$p_smarty)
{
    global $g_ado_db;

    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');
    $html = '';

    if (!isset($p_params['html_code']) || empty($p_params['html_code'])) {
        $p_params['html_code'] = '';
    }

    switch ($p_params['attribute']) {
        case 'title':
        case 'mood':
        case 'user_name':
        case 'user_email':
            $attr = $p_params['attribute'];
            $value = htmlspecialchars(isset($_REQUEST['f_blogcomment_'.$attr]) ? Input::Get('f_blogcomment_'.$attr) : $campsite->blogcomment->$attr);
            $html .= "<input type=\"text\" name=\"f_blogcomment_$attr\" value=\"$value\" {$p_params['html_code']} />";
        break;
            
        case 'content':
            $value = isset($_REQUEST['f_blogcomment_content']) ? Input::Get('f_blogcomment_content') : $campsite->blogcomment->content;
            $html .= "<textarea name=\"f_blogcomment_content\" id=\"f_blogcomment_content\" {$p_params['html_code']} />$value</textarea>";
            if ($p_params['wysiwyg']) {
                $html .='<script language="javascript" type="text/javascript" src="/javascript/tinymce/tiny_mce.js"></script>'.
                        '<script language="javascript" type="text/javascript">'.
                        '     tinyMCE.init({'.
                        '     	mode : "exact",'.
                        '        elements : "f_blogcomment_content",'.
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
    }
    
    return $html;
} // fn smarty_function_blogcomment_edit

?>