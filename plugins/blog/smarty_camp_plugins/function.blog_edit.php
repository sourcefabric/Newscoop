<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite blog_edit function plugin
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
function smarty_function_blog_edit($p_params, &$p_smarty)
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
            $value = htmlspecialchars(isset($_REQUEST['f_blog_title']) ? Input::Get('f_blog_title') : $campsite->blog->title);
            $html .= "<input type=\"text\" name=\"f_blog_title\" value=\"$value\" {$p_params['html_code']} />";
        break;
            
        case 'info':
            $value = htmlspecialchars(isset($_REQUEST['f_blog_info']) ? Input::Get('f_blog_info') : $campsite->blog->info);
            $html .= "<textarea name=\"f_blog_info\" id=\"f_blog_info\" {$p_params['html_code']} />$value</textarea>";
            if ($p_params['wysiwyg']) {
                $html .='<script language="javascript" type="text/javascript" src="' . $Campsite['WEBSITE_URL'] . '/js/tinymce/tiny_mce.js"></script>'.
                        '<script language="javascript" type="text/javascript">'.
                        '     tinyMCE.init({'.
                        '     	mode : "exact",'.
                        '        elements : "f_blog_info",'.
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
       
        case 'request_text':
            $value = htmlspecialchars(isset($_REQUEST['f_blog_request_text']) ? Input::Get('f_blog_request_text') : $campsite->blog->request_text);
            $html .= "<textarea name=\"f_blog_request_text\"  {$p_params['html_code']}>{$value}</textarea>";
        break;
        
        case 'status':
            $value = isset($_REQUEST['f_blog_status']) ? Input::Get('f_blog_status') : $campsite->blog->status;           
            $html .= "<select name=\"f_blog_status\"  {$p_params['html_code']}>";
            
            foreach (array('online', 'offline', 'moderated') as $status) {
                if ($value == $status) {
                    $selected = 'selected="selected"';   
                } else {
                    $selected = '';   
                }
                $html .= "<option value=\"$status\" $selected {$p_params['html_code']}>$status</option>";   
            }
            $html .= "</select>"; 
        break;  
    }

    return $html;
} // fn smarty_function_blog_edit

?>
