<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Expires" content="now" />
<title><?php putGS("Multi date event"); ?></title>
<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/calendar/fullcalendar.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/calendar/timepicker.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/form.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/content.css" />



<?php
$f_multidate_box = 1;
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/html_head.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/SystemPref.php');


$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 1);

if (isset($_SESSION['f_language_selected'])) {
	$f_old_language_selected = (int)$_SESSION['f_language_selected'];
} else {
	$f_old_language_selected = 0;
}
$f_language_selected = (int)camp_session_get('f_language_selected', 0);
?>
<script src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/jquery/calendar/fullcalendar.min.js" type="text/javascript"></script>
<script type="text/javascript">
function popup_close() {
    alert('popup close');
    try {
        if (parent.$.fancybox.reload) {
            parent.$.fancybox.message = '<?php putGS('Locations updated.'); ?>';
        }
        parent.$.fancybox.close();
    }
    catch (e) {window.close();}
}

function submitForm(formData) {
	var url = '<?php echo $Campsite['WEBSITE_URL']; ?>/admin/multidate/add';
	$.ajax({
        'url': url,
        'type': 'POST',
        'data': formData,
        'dataType': 'json',
        'success': function(json) {
            
        },
        'error': function(json) {
        }
    });
}

function submitSpecificForm() {
    formData = $('#specific-dates-form').serialize();
    submitForm(formData);    
}

function submitDaterangeForm() {
	formData = $('#daterange-dates-form').serialize();
	submitForm(formData);
}

function popup_save() {
    //alert('popup save');
    //callServer(['ArticleList', 'doAction'], aoData, fnSaveCallback);

    if ($("#specific-dates").css('display') == 'block') {
        submitSpecificForm();
    } else {
        submitDaterangeForm();
    }
    
    
}

function reset_specific_start_time() {
	//console.log('specific start time');
    $('#specific-radio-start-only').attr('checked', 'checked');
    $('#start-time-specific').css('display', 'inline');
    $('#end-time-specific').css('display', 'none');
}

function reset_specific_start_end_time() {
	//console.log('specific start end time');
    $('#specific-radio-start-and-end').attr('checked', 'checked');
    $('#start-time-specific').css('display', 'inline');
    $('#end-time-specific').css('display', 'inline');
}

function reset_specific_all_day() {
	//console.log('specific all day');
    $('#specific-radio-all-day').attr('checked', 'checked');
    $('#start-time-specific').css('display', 'none');
    $('#end-time-specific').css('display', 'none');
}

$(function(){
	 $('#specific-radio-start-only').click( function () {
		 reset_specific_start_time(); 
	});
	 $('#specific-radio-start-and-end').click( function () {
		 reset_specific_start_end_time(); 
	});
	 $('#specific-radio-all-day').click( function () {
		 reset_specific_all_day(); 
    });

	$('.date-range-switch').click( function() {
	    $('#specific-dates').css('display','none');
	    $('#daterange-dates').css('display','block');
	    
	    $('.date-range-switch').addClass('switch-active');
	    $('.date-specific-switch').removeClass('switch-active');
	});

	$('.date-specific-switch').click( function() {
        $('#daterange-dates').css('display','none');
        $('#specific-dates').css('display','block');

        $('.date-specific-switch').addClass('switch-active');
        $('.date-range-switch').removeClass('switch-active');
    });

	

	reset_specific_start_time();
	 $('#full-calendar').fullCalendar({
		    });
	 $("#start-date-specific").datepicker({ dateFormat: 'yy-mm-dd' });
	 
	 $('#start-date-daterange').datepicker({ dateFormat: 'yy-mm-dd' });
	 $('#end-date-daterange').datepicker({ dateFormat: 'yy-mm-dd' });
	 
	 $('#start-time-specific').timepicker({});
	 $('#end-time-specific').timepicker({});
	 $('#start-time-daterange').timepicker({});
	 $('#end-time-daterange').timepicker({});

		    
});


</script>

</head>
<body onLoad="return false;" style="background: none repeat scroll 0 0 #FFFFFF;">




<div class="content">
<div id="multidate-box">
<div class="toolbar">
	<div class="save-button-bar">
	    <input type="submit" name="cancel" value="<?php echo putGS('Close'); ?>" class="default-button" onclick="popup_close();" id="context_button_close">
	    <input type="submit" name="save" value="<?php echo putGS('Save'); ?>" class="save-button-small" onclick="popup_save();" id="context_button_save">
	</div>
<h2><?php echo putGS('Multi date event'); ?></h2>
</div>


<div class="context-content" style="position: relative">

<div class="date-common">


</div>

<div class="dates" id="specific-dates" style="display:block">
<form id="specific-dates-form" onsubmit="return false;">
    <input type="text" class="multidate-input" id="specific-date-name" name="specific-date-name" style="width: 260px; margin-left: 12px; margin-top: 20px;"  />
    <input type="hidden" name="article-number" value="<?php echo Input::Get('f_article_number', 'int', 1)?>" />
    <input type="hidden" name="date-type" value="specific" />
	<div class="date-mode-switcher">
		<div class="date-switch date-range-switch"><?php echo putGS('Date from / to'); ?></div>
		<div class="date-switch date-specific-switch switch-active border-left"><?php echo putGS('Specific dates'); ?></div>
	</div>
	
    <input type="text" id="start-date-specific" name="start-date-specific" class="multidate-input"style="width: 125px; margin-left: 12px; margin-top: 20px;" readonly='true'/> 
	<input type="text" id="start-time-specific" name="start-time-specific" class="multidate-input" style="width: 128px; margin-left: 2px; margin-top: 20px;" readonly='true'/> 
	<input type="text" id="end-time-specific" name="end-time-specific" class="multidate-input" style="width: 128px; margin-left: 144px; margin-top: 20px;" readonly='true'/>
	
	<div class="specific-radio-holder">
		<input type="radio" id="specific-radio-start-only" name="specific-radio" value="start-only" checked="checked" /><?php echo putGS('Start time'); ?><br />
		<input type="radio" id="specific-radio-start-and-end" name="specific-radio" value="start-and-end" /><?php echo putGS('Start & end time'); ?><br />
		<input type="radio" id="specific-radio-all-day" name="specific-radio" value="all-day" /><?php echo putGS('All day'); ?>
	</div>
</form>
</div>

<div class="dates" id="daterange-dates" style="display: none">
<form id="daterange-dates-form" onsubmit="return false;">
    <input type="text" class="multidate-input" id="daterange-date-name" name="daterange-date-name" style="width: 260px; margin-left: 12px; margin-top: 20px;" />
    <input type="hidden" name="article-number" value="<?php echo Input::Get('f_article_number', 'int', 1)?>" />
    <input type="hidden" name="date-type" value="daterange" />
    <div class="date-mode-switcher">
        <div class="date-switch date-range-switch"><?php echo putGS('Date from / to'); ?></div>
        <div class="date-switch date-specific-switch switch-active border-left"><?php echo putGS('Specific dates'); ?></div>
    </div>
    
    <input type="text" id="start-date-daterange" name="start-date-range" class="multidate-input"style="width: 125px; margin-left: 12px; margin-top: 20px;" /> 
    <input type="text" id="start-time-daterange" name="start-time-daterange" class="multidate-input" style="width: 128px; margin-left: 2px; margin-top: 20px;" />
    <span style="display:block; margin-left: 12px; margin-top: 10px;"><?php echo putGS('To'); ?></span> 
    <input type="text" id="end-date-daterange" name="end-date-daterange" class="multidate-input"style="width: 125px; margin-left: 12px; margin-top: 10px;" /> 
    <input type="text" id="end-time-daterange" name="end-time-daterange" class="multidate-input" style="width: 128px; margin-left: 2px; margin-top: 10px;" />
    
    <div class="repeats-checkbox-holder">
        <input type="checkbox" id="daterange-all-day" name="daterange-all-day" value="1" checked="checked" /><?php echo putGS('All day'); ?><br />
        <input type="checkbox" id="daterange-repeats" name="daterange-repeats" value="1" /><?php echo putGS('Repeats'); ?><br />
    </div>
    
    <select id="repeats-cycle" class="multidate-input" style="margin-left: 12px; margin-top: 10px; width: 260px;">
        <option value='daily'><?php echo putGS('Daily'); ?></option>
        <option value='weekly'><?php echo putGS('Weekly'); ?></option>
        <option value='monthly'><?php echo putGS('Monthly'); ?></option>
    </select>
    
     <div class="repeats-checkbox-holder">
        <input type="checkbox" id="monday" name="day-repeat" value="moday"/>M
        <input type="checkbox" id="tuesday" name="day-repeat" value="tuesday"/>T
        <input type="checkbox" id="wednesday" name="day-repeat" value="wednesday"/>W
        <input type="checkbox" id="thursday" name="day-repeat" value="thursday"/>T
        <input type="checkbox" id="friday" name="day-repeat" value="friday"/>F
        <input type="checkbox" id="saturday" name="day-repeat" value="saturday"/>S
        <input type="checkbox" id="sunday" name="day-repeat" value="sunday"/>S
    </div>
    
     <div class="repeats-checkbox-holder">
       <?php echo putGS('Ends'); ?>
       <input type="radio" id="cycle-ends-on-set-date" name="cycle-ends"  value="on-set-date" style="display: inline; margin-left:25px;" name="cycle-ends"/><?php echo putGS('On set date');?><br />
       <input type="radio" id="cycle-ends-counter" name="cycle-ends" value="counter" style="display: inline; margin-left:73px;" /><?php echo putGS('After');?>
       <input type="text" class="multidate-input" name="cycle-ends" style="display: inline; width:50px;" id="occurences" /> <?php echo putGS('occurences');?>
       <input type="radio" id="cycle-ends-never" name="cycle-ends" value="never" style="display: inline; margin-left:73px;" /><?php echo putGS('Never');?><br />
       
    </div>
</form>
</div>

<div id="full-calendar" class="full-calendar"></div>
</div>
</div>
</div>
</body>
</html>





