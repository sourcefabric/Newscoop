<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Expires" content="now" />
<title><?php putGS("Multi date event"); ?></title>
<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/calendar/fullcalendar.css" />



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

function popup_save() {
    alert('popup save');
    //callServer(['ArticleList', 'doAction'], aoData, fnSaveCallback);
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
		    })
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
    <input type="text" class="multidate-input" id="specific-date-name" style="width: 260px; margin-left: 12px; margin-top: 20px;" />

	<div class="date-mode-switcher">
		<div class="date-switch date-range-switch"><?php echo putGS('Date from / to'); ?></div>
		<div class="date-switch date-specific-switch switch-active border-left"><?php echo putGS('Specific dates'); ?></div>
	</div>
	
    <input type="text" id="start-date-specific" class="multidate-input"style="width: 125px; margin-left: 12px; margin-top: 20px;" /> 
	<input type="text" id="start-time-specific" class="multidate-input" style="width: 128px; margin-left: 2px; margin-top: 20px;" /> 
	<input type="text" id="end-time-specific" class="multidate-input" style="width: 128px; margin-left: 144px; margin-top: 20px;" />
	
	<div class="specific-radio-holder">
		<input type="radio" id="specific-radio-start-only" name="specific-radio" value="start-only" checked="checked" /><?php echo putGS('Start time'); ?><br />
		<input type="radio" id="specific-radio-start-and-end" name="specific-radio" value="start-and-end" /><?php echo putGS('Start & end time'); ?><br />
		<input type="radio" id="specific-radio-all-day" name="specific-radio" value="all-day" /><?php echo putGS('All day'); ?>
	</div>

</div>

<div class="dates" id="daterange-dates" style="display: none">
    <input type="text" class="multidate-input" id="daterange-name" style="width: 260px; margin-left: 12px; margin-top: 20px;" />
    
    <div class="date-mode-switcher">
        <div class="date-switch date-range-switch"><?php echo putGS('Date from / to'); ?></div>
        <div class="date-switch date-specific-switch switch-active border-left"><?php echo putGS('Specific dates'); ?></div>
    </div>
    
    <input type="text" id="start-date-daterange" class="multidate-input"style="width: 125px; margin-left: 12px; margin-top: 20px;" /> 
    <input type="text" id="start-time-daterange" class="multidate-input" style="width: 128px; margin-left: 2px; margin-top: 20px;" />
    <span style="display:block; margin-left: 12px; margin-top: 10px;"><?php echo putGS('To'); ?></span> 
    <input type="text" id="end-date-daterange" class="multidate-input"style="width: 125px; margin-left: 12px; margin-top: 10px;" /> 
    <input type="text" id="end-time-daterange" class="multidate-input" style="width: 128px; margin-left: 2px; margin-top: 10px;" />
    
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
        <input type="checkbox" id="monday" name="monday" value="1"/>M
        <input type="checkbox" id="tuesday" name="tuesday" value="1"/>T
        <input type="checkbox" id="wednesday" name="wednesday" value="1"/>W
        <input type="checkbox" id="thursday" name="thursday" value="1"/>T
        <input type="checkbox" id="friday" name="friday" value="1"/>F
        <input type="checkbox" id="saturday" name="saturday" value="1"/>S
        <input type="checkbox" id="sunday" name="sunday" value="1"/>S
    </div>
    
     <div class="repeats-checkbox-holder">
       <?php echo putGS('Ends'); ?>
       <input type="radio" id="cycle-ends-on-set-date" value="1" style="display: inline; margin-left:25px;" name="cycle-ends"/><?php echo putGS('On set date');?><br />
       <input type="radio" id="cycle-ends-counter" style="display: inline; margin-left:73px;"  name="cycle-ends" /><?php echo putGS('After');?>
       <input type="text" class="multidate-input" style="display: inline; width:50px;" id="occurences" name="cycle-ends" /> <?php echo putGS('occurences');?>
       <input type="radio" id="cycle-ends-never" value="1" style="display: inline; margin-left:73px;" name="cycle-ends" /><?php echo putGS('Never');?><br />
       
    </div>
    
</div>

<div id="full-calendar" class="full-calendar"></div>
</div>
</div>
</div>
</body>
</html>





