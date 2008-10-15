<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite blogentry_form block plugin
 *
 * Type:     block
 * Name:     blogentry_form
 * Purpose:  Provides a form for an blog entry
 *
 * @param string
 *     $p_params
 * @param string
 *     $p_smarty
 * @param string
 *     $p_content
 *
 * @return
 *
 */
function smarty_block_blogentry_form($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    // gets the context variable
	$campsite = $p_smarty->get_template_vars('campsite');
	    
    if (!is_object($campsite->blog) || !$campsite->blog->identifier) {
        return false;   
    }
    
    if (isset($p_content)) {
    	require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');
	    $html = '';
	
	    if (isset($p_params['template'])) {
	        $template = new MetaTemplate($p_params['template']);
	        if (!$template->defined()) {
	            $template = null;
	        }
	    }
	    $templateId = is_null($template) ? $campsite->template->identifier : $template->identifier;

	    /*
        if (!isset($p_params['submit_button'])) {
            $p_params['submit_button'] = 'Submit';
        }
                
        if (!isset($p_params['preview_button'])) {
            $p_params['preview_button'] = 'Preview';
        }
        */
	            
	    if (!isset($p_params['html_code']) || empty($p_params['html_code'])) {
	        $p_params['html_code'] = '';
	    }
	    
        $url = $campsite->url;
        $url->uri_parameter = "template " . str_replace(' ', "\\ ", $template->name);
        $html .= "<form name=\"blogentry\" action=\"" . $url->uri_path . "\" method=\"post\">\n";
        
        $html .= "<input type=\"hidden\" name=\"f_blogentry\" value=\"1\" />\n";
        $html .= "<input type=\"hidden\" name=\"f_blog_id\" value=\"{$campsite->blog->identifier}\" />\n";
        
        foreach ($campsite->url->form_parameters as $param) {
            $html .= '<input type="hidden" name="'.$param['name'].'" value="'.htmlentities($param['value'])."\" />\n";
        }
        
        $html .= $p_content;
        
        $html .= "</form>\n";
	    
        return $html;
	} 
} // fn smarty_block_blogentry_form

?>