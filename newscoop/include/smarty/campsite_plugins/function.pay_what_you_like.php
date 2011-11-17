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
    $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    $orderId = str_replace(".", "", uniqid('', true));
    $acceptMsg = '';
    $acceptUrl = 'http://'.$_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'].'?payment=yes';
    if ( array_key_exists('payment', $params)) {
    	if ($params['payment'] == 'yes') {
    		$js = "
    			$('#pay-what-you-want-{$orderId}').trigger('click');
    		";
    		$acceptMsg = 'Vielen Dank für Ihre Zahlung!';
    		$acceptUrl = 'http://'.$_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    	}
    }
    /*
     *
     */


    $campsite = $p_smarty->getTemplateVars('gimme');

    // allowing logged in user or not
    $denyAnon = false;
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

});
</script>
JS;
        $p_smarty->assign('pay-what-you-like-js-set', true, true);
    }

    //taking out the getGS('') because we want to get rid really fast of the (*) signs

	$linktext = 'tageswoche.ch honorieren';
    if (isset($p_params['linktext'])) {
        $linktext = $p_params['linktext'];
    }

    $title = 'tageswoche.ch honorieren';
	if (isset($p_params['title'])) {
        $linktext = $p_params['title'];
    }

    $pre = 'Alle Artikel auf tageswoche.ch sind frei verfügbar. Wenn Ihnen unsere Arbeit etwas wert ist, können Sie uns freiwillig unterstützen. <br />
        	Sie entscheiden, wieviel Sie bezahlen. Danke, dass Sie uns helfen, tageswoche.ch in Zukunft noch besser zu machen.';
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


    $p_smarty->smarty->loadPlugin('smarty_function_uri');
	$pwylUri = smarty_function_uri( array('static_file' => "_css/tw2011/img/thumb_tw-pwyl.png"), $p_smarty);



	//BUILDING THE SHASign
	$shaEncodePath =  $GLOBALS['Campsite']['SUBDIR'].'/postfinance';



	$markup .= <<<HTML
<div style="display: block; margin-bottom: 25px">
	<img style="float: left; margin-right: 10px" alt="pwyl" src="{$pwylUri}" />
	<p><a href="#pay-what-you-want-popup-{$orderId}" id="pay-what-you-want-{$orderId}">{$linktext}</a></p>

	<div style="display:none">
		<div class="pay-what-you-want-popup" id="pay-what-you-want-popup-{$orderId}">
	    	<article>
	        	<header><p>{$title}</p></header>
	        	<p>{$pre}</p>

	        	<div style="width:90%; text-align: center; height:35px;">
	        	{$acceptMsg}
	        	</div>
				<div style="width:100%">
	        		<div style="width:32%; float:left; text-align: right;">
	        			<div style="width: 110px; float:right;">
						<form method="post" action="{$shaEncodePath}" id="PSForm_{$orderId}" >
							<!-- general parameters -->
							<input type="hidden" name="PSPID" value="medienbasel">
							<input type="hidden" name="orderID" value='{$orderId}'>
							<input type="hidden" name="amount" id='postfinance_final_amount_{$orderId}' value="500">
							<input type="hidden" name="currency" value="CHF">
							<input type="hidden" name="language" value="de_DE">

							<!-- check before the payment: see Security: Check before the Payment -->
							<input type="hidden" name="SHASign" value="" id='SHASign'>

							<!-- post payment redirection: see Transaction Feedback to the Customer -->
							<input type="hidden" name="accepturl" value="{$acceptUrl}">
							
							<input type="button" value="Postfinance" style="background-color: #FFCC00; font-weight: bold; color: black; float:left;" onclick="submitPSForm_{$orderId}();" />
						</form>
							<label for='postfinance_amount' style='float: left; margin-top:5px; margin-right:2px;'>CHF</label>
        					<input type="text" value="5" id='postfinance_amount_{$orderId}' name='postfinance_amount' style='width:35px; float: left; margin-top:2px;'/>
					</div>
	        	</div>
	        	<div style="width:32%; float:left; text-align:center;">
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="GJVQTYU3LMQJ6">
					<input type="image" src="https://www.paypalobjects.com/de_DE/CH/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="Jetzt einfach, schnell und sicher online bezahlen – mit PayPal.">
					<img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
					</form>
				</div>
	        	<div style="width:32%; float:left; text-align:left;">
						<a href="http://flattr.com/thing/421328/tageswoche-ch" target="_blank">
							<img src="http://api.flattr.com/button/flattr-badge-large.png" alt="Flattr this" title="Flattr this" border="0" />
						</a>
	        	</div>
	        </div>
	    </article>
	</div>
</div>


</div>

<script>

function submitPSForm_{$orderId}() {
    var postfinanceFinalAmount = $('#postfinance_amount_{$orderId}').val();
    if ( postfinanceFinalAmount <= 0 ) {
        alert('Bitte geben Sie einen Betrag ein.');
        $('#postfinance_amount_{$orderId}').focus();
    } else {
        $('#postfinance_final_amount_{$orderId}').val( $('#postfinance_amount_{$orderId}').val() * 100 );
        $('#PSForm_{$orderId}').submit();
    }
    return false;
}

$(function() {
	$('head').append(
	"<script type='text/javascript'>" +
	"/* <![CDATA[ */ " +
	    "(function() { " +
	        "var s = document.createElement('script'), t = document.getElementsByTagName('script')[0];" +
	        "s.type = 'text/javascript';" +
	        "s.async = true;" +
	        "s.src = 'http://api.flattr.com/js/0.6/load.js?mode=auto';" +
	        "t.parentNode.insertBefore(s, t);" +
	    "})();" +
	"/* ]]> */ " +
	'<' + '/' + 'script>');
	
	$('#pay-what-you-want-{$orderId}').fancybox();
	
	$("#postfinance_amount_{$orderId}").keydown(function(event) {
        // Allow only backspace and delete
        if ( event.keyCode == 46 || event.keyCode == 8 ) {
            // let it happen, don't do anything
            return true;
        }
        else {
            // Ensure that it is a number and stop the keypress
            if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
                event.preventDefault();
            }
        }
    });
    {$js}
});

</script>
HTML;

    return $markup;
}