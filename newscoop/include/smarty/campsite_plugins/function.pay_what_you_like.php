<?php
/**
 * Generates a piece of markup that provides payment functionality
 * something like doantions
 * @param array $p_params
 * @param Smarty_Internal_Template $p_smarty
 */
function smarty_function_pay_what_you_like($p_params, &$p_smarty)
{
    // get request params for checking the return from payment site
    // Zend_Controller_Front::getInstance()->getRequest()->getParams();
    /*
     *
     */

    $campsite = $p_smarty->getTemplateVars('gimme');

    // allowing logged in user or not
    $denyAnon = true;
    $userId = $campsite->user->identifier ? $campsite->user->identifier : 'null'; // for js
    if (isset($p_params['nologin'])) {
        $denyAnon = false;
    }

    $amount = '5';
    if (isset($p_params['amount'])) {
        $amount = $p_params['amount'];
    }

    $mustLogin = getGS('You must login to use this feature!');
    $markup = '';
    if ($p_smarty->getVariable('pay-what-you-like-js-set') instanceof Undefined_Smarty_Variable)
    {
        $markup = <<<JS
<script type='text/javascript'>
$(function()
{
	var denyAnon = $denyAnon;
	var userId = $userId;
	$('.pay-what-you-like').each(function()
	{
		$(this).find('a:eq(0)').toggle(function()
		{
			$(this).nextAll('div:eq(0)').slideDown(100);
		},
		function()
		{
			$(this).nextAll('div:eq(0)').slideUp(100);
		});
		$(this).find('form:eq(0)').submit(function()
		{
			if (denyAnon && !userId) {
				if ($('#ob_main:visible').length == 0) omnibox.showHide();
				$('#ob_main .top_title').append( $('<span />').text('{$mustLogin}').delay(5000).fadeOut('fast', function(){ $(this).remove() }) );
				return false;
			}
			var exdays = 5;
			var exdate = new Date();
			exdate.setDate( exdate.getDate() + exdays );
			var value = escape('{$amount}') + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
			document.cookie = "pay-what-you-like = " + value;
		});
	});
});
</script>
JS;
        $p_smarty->assign('pay-what-you-like-js-set', true, true);
    }

    $pre = '';
    if (isset($p_params['descr'])) {
        $pre = $p_params['descr'];
    }

    $descYes = '';
    if (isset($p_params['yestext'])) {
        $descYes = $p_params['yestext'];
    }

    $descNo = '';
    if (isset($p_params['notext'])) {
        $descNo = $p_params['notext'];
    }
    $markup .='<div class="pay-what-you-like">'
    		.    '<a href="javascript:void(0)">'.getGS('Pay what you like').'!</a>'
    		.    '<div>'
    		.        "<div class='pay-what-you-like-pre'>{$pre}</div>"
    		.        "<form action=''>"
    		.    	 	'<ul>'
    		.                '<li>'
    		.               	'<button class="pay-what-you-like-yes">'.getGS("Yes, I'd like to pay").'</button> '
    		.            		"<input value='{$amount}' />&thinsp;".getGS('Francs')
    		.            	'</li>'
    		.            	'<li><button class="pay-what-you-like-no">'.getGS("No I don't want to pay").'</button></li>'
    		.        		"<li class='pay-what-you-like-desc-yes'>{$descYes}</li>"
    		.        		"<li class='pay-what-you-like-desc-no'>{$descNo}</li>"
    		.    		'</ul>'
    		.       "</form>"
    		.    '</div>'
    		. '</div>';
    return $markup;
}