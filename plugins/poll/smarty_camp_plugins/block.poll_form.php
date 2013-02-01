<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite poll_form block plugin
 *
 * Type:     block
 * Name:     poll_form
 * Purpose:  Provides a form for an poll
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
function smarty_block_poll_form($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    global $Campsite;
    
    if (isset($p_content)) {
    	$p_smarty->smarty->loadPlugin('smarty_shared_escape_special_chars');

	    // gets the context variable
	    $campsite = $p_smarty->getTemplateVars('gimme');
	    $html = '';
	
	    if (isset($p_params['template'])) {
	        $template = new MetaTemplate($p_params['template']);
	        if (!$template->defined()) {
	            $template = null;
	        }
	    }
	    $templateId = is_null($template) ? $campsite->template->identifier : $template->identifier;
	    if (!isset($p_params['submit_button'])) {
	        $p_params['submit_button'] = 'Submit';
	    }
	    if (!isset($p_params['html_code']) || empty($p_params['html_code'])) {
	        $p_params['html_code'] = '';
	    }
	    if ($p_params['ajax'] == true && !Input::Get('f_poll_ajax_request')) {
	       $html .=
'
<script language="javascript" src="/js/jquery/jquery.min.js"></script>
<script language="javascript"><!--
function poll_'.$campsite->poll->identifier.'_vote()
{
    var data = $("#poll_'.$campsite->poll->identifier.'_form").serialize();

    // avoid building the div tag on ajax request
    data = data + "&f_poll_ajax_request=1";

    $.get($("#poll_'.$campsite->poll->identifier.'_form").attr("action"),
        data,
        function(response) {
            $("#poll_'.$campsite->poll->identifier.'_div").html(response);  
        });
}
//--></script>	
';
	       $html .= '<div id="poll_'.$campsite->poll->identifier.'_div">';
	       
	       $mode_tag = "<input type=\"hidden\" name=\"f_poll_mode\" value=\"ajax\" />\n";
	    } else {
	       $mode_tag = "<input type=\"hidden\" name=\"f_poll_mode\" value=\"standard\" />\n";   
	    }
	    
        $url = $campsite->url;
        $url->uri_parameter = "template " . str_replace(' ', "\\ ", $template->name);
        $html .= "<form name=\"poll\" id=\"poll_{$campsite->poll->identifier}_form\" action=\"" . $url->uri_path . "\" method=\"post\">\n";
        
        $html .= "<input type=\"hidden\" name=\"f_poll\" value=\"1\" />\n";
        $html .= "<input type=\"hidden\" name=\"f_poll_nr\" value=\"{$campsite->poll->number}\" />\n";
        $html .= "<input type=\"hidden\" name=\"f_poll_language_id\" value=\"{$campsite->poll->language_id}\" />\n";        $html .= $mode_tag;
        
        foreach ($campsite->url->form_parameters as $param) {
            $html .= '<input type="hidden" name="'.$param['name']
                .'" value="'.htmlentities($param['value'])."\" />\n";
        }
        $html .= "<input type=\"hidden\" name=\"tpl\" value=\"$templateId\" />\n";
        
        $html .= $p_content;
        
        if (strlen($p_params['submit_button']) && $campsite->poll->is_votable) { 
	        $html .= "<div align=\"center\">\n.
	        		 <input type=\"submit\" name=\"f_poll\" value=\"".
	        		 smarty_function_escape_special_chars($p_params['submit_button']).
	            	 "\" ".
	            	 $p_params['html_code']." />\n".
	            	 "</div>\n";
        }
        
        $html .= "</form>\n";
        
	    if ($p_params['div'] == true && !Input::Get('f_poll_ajax_request')) {
	       $html .= '</div>';
	    }
	    
        return $html;
	} 
} // fn smarty_block_poll_form

?>
