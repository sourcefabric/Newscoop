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
$articleId = Input::Get('f_article_number', 'int', 1);

if (isset($_SESSION['f_language_selected'])) {
	$f_old_language_selected = (int)$_SESSION['f_language_selected'];
} else {
	$f_old_language_selected = 0;
}
$f_language_selected = (int)camp_session_get('f_language_selected', 0);
?>
<script src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/jquery/calendar/fullcalendar.min.js" type="text/javascript"></script>
<script type="text/javascript">

function resetSpecificForm() {
	$('#specific-multidate-id').val('');
	$('#specific-radio-start-only').trigger('click');
	$('#start-date-specific').val('');
	$('#start-time-specific').val('');
	$('#end-time-specific').val('');
	$('#remove-specific-link').css('display','none');
}

function resetDaterangeForm() { 
	$('#daterange-multidate-id').val('');
	$('#start-time-daterange').css('display', 'inline').val('');
	$('#start-date-daterange').val('');
	$('#end-time-daterange').css('display', 'inline').val('');
	$('#end-date-daterange').css('visibility','visible').val('');
	$('#daterange-all-day').removeAttr('checked');
	$('#cycle-ends-on-set-date').trigger('click');
	$('#remove-daterange-link').css('display','none');
}

function prepareDate(oldFormat) {
	var dates = oldFormat.split('-');
	var returnVal = dates[1]+'/'+dates[2]+'/'+dates[0];
	return returnVal;
}

function timeOk(startDate, startTime, endDate, endTime) {
	startDate = prepareDate(startDate);
	endDate = prepareDate(endDate);
	var startFull = new Date(startDate + ' ' + startTime);
	var endFull = new Date(endDate + ' ' + endTime);
	if ( endFull - startFull >=0 ) {
		return true;
	} else {
		return false;
	}
}

function popup_close() {
    try {
        if (parent.$.fancybox.reload) {
            parent.$.fancybox.message = '<?php putGS('Locations updated.'); ?>';
        }
        parent.$.fancybox.close();
    }
    catch (e) {window.close();}
}

function submitForm(formData) {
	var flash = flashMessage(localizer.processing, null, true);
	var url = '<?php echo $Campsite['WEBSITE_URL']; ?>/admin/multidate/add';
	$.ajax({
        'url': url,
        'type': 'POST',
        'data': formData,
        'dataType': 'json',
        'success': function(json) {
        	$('#full-calendar').fullCalendar( 'refetchEvents' );
        	flash.fadeOut();
        	resetSpecificForm();
        	resetDaterangeForm();
        },
        'error': function(json) {
        	flash.fadeOut();
        }
    });
}

function submitSpecificForm() {

	//validating specific form
	var valid = 1;
	if ( $('#start-date-specific').val() == '' ) {
		valid = 0;
		alert("<?php echo putGS("Start date can't be empty")?>");
		$('#start-date-specific').focus();
	}
	var radio = $('input:radio[name=specific-radio]:checked').val();
	if (radio == 'start-only' || radio == 'start-and-end') {
		if ( valid == 1) {
			if ( $('#start-time-specific').val() == '' ) {
				valid = 0;
				alert("<?php echo putGS("Start time can't be empty")?>");
				$('#start-time-specific').focus();
			}
		}
		if (radio == 'start-and-end' && valid == 1) {
			if ( $('#end-time-specific').val() == '' ) {
				alert("<?php echo putGS("End time can't be empty")?>");
				valid = 0;
				$('#end-time-specific').focus();
			}

			if (valid == 1) {
				if ( !timeOk($('#start-date-specific').val(), $('#start-time-specific').val(), $('#start-date-specific').val(), $('#end-time-specific').val()) ) {
					valid = 0;
					alert("<?php echo putGS("End time can't be set before start time")?>");
					$('#end-time-specific').focus();
				}
			}		
		}
	}
	
	if (valid == 1) {
		formData = $('#specific-dates-form').serialize();
	    submitForm(formData);
	}
        
}

function submitDaterangeForm() {

	var valid = 1;
	var needTime = true;
	var needDate = true;

	if ($('input:radio[name=cycle-ends]:checked').val() == 'never') {
		needDate = false;
	}
	if ($('#daterange-all-day').attr('checked') == 'checked' ) {
		needTime = false;
	}
	
	//start date
	if ( $('#start-date-daterange').val() == '' ) {
		valid = 0;
		alert("<?php echo putGS("Start date can't be empty")?>");
		$('#start-date-daterange').focus();
	}
	//start time
	if ( valid == 1 && needTime) {
		if ( $('#start-time-daterange').val() == '' ) {
			valid = 0;
			alert("<?php echo putGS("Start time can't be empty")?>");
			$('#start-time-daterange').focus();
		}
	}
	//end date
	if (valid == 1 && needDate) {
		if ( $('#end-date-daterange').val() == '' ) {
			valid = 0;
			alert("<?php echo putGS("End date can't be empty")?>");
			$('#end-date-daterange').focus();
		}
	}
	//end time 
	if ( valid == 1 && needTime) {
		if ( $('#end-time-daterange').val() == '' ) {
			valid = 0;
			alert("<?php echo putGS("End time can't be empty")?>");
			$('#end-time-daterange').focus();
		}
	}
	//end is not set before time
	if (valid == 1) {
		var startDate = $('#start-date-daterange').val();
		if (needDate) {
			var endDate = $('#end-date-daterange').val();
		} else {
			var endDate = "2099-12-31";
		}
		if (needTime) {
			var startTime = $('#start-time-daterange').val();
			var endTime = $('#end-time-daterange').val();
		} else {
			var startTime = "00:00";
			var endTime = "23:59";
		}
		
		if ( !timeOk(startDate, startTime, endDate, endTime) ) {
			valid = 0;
			alert("<?php echo putGS("End time can't be set before start time")?>");
			$('#end-date-daterange').focus();
		}
	}	
	
	
	
	if (valid == 1) {
		formData = $('#daterange-dates-form').serialize();
		submitForm(formData);
	}
	
}

function popup_save() {
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

function loadDaterangeEvent(event) {
	resetDaterangeForm();
	$('.date-range-switch').trigger('click');
	$('#daterange-multidate-id').val(event.id);
	$('#start-date-daterange').val(event.startDate);
	$('#start-time-daterange').val(event.startTime);
	$('#end-date-daterange').val(event.endDate);
	$('#end-time-daterange').val(event.endTime);
	var repeatValue = 'recurring:'+event.isRecurring;
	$("#repeats-cycle option[value='"+repeatValue+"']").attr('selected','selected');
	$('#remove-daterange-link').css('display','block');
	if (event.neverEnds == 1) {
		$('#cycle-ends-never').attr('checked', 'checked');
		$('#end-date-daterange').css('visibility','hidden');
	} else {
		$('#cycle-ends-on-set-date').attr('checked', 'checked');
		$('#end-date-daterange').css('visibility','visible');
	}
	if (event.allDay) {
		$('#daterange-all-day').trigger('click');
		$('#start-time-daterange').css('display', 'none');
		$('#end-time-daterange').css('display', 'none');
	}
	
}

function doSpecificTimeRange(start, end) {
	if ( (start == '00:00' || start == '00:01') && end == '23:59' ) {
		$('#specific-radio-all-day').trigger('click');
	}

	if ( (start != '00:00' && start != '00:01') && end == '23:59' ) {
		$('#specific-radio-start-only').trigger('click');
	}

	if ( (start != '00:00' && start != '00:01') && end != '23:59' ) {
		$('#specific-radio-start-and-end').trigger('click');
	}
}

function loadSpecificEvent(event) {
	//console.log('loading specific event');
	$('.date-specific-switch').trigger('click');
	$('#specific-multidate-id').val(event.id);
	$('#start-date-specific').val(event.startDate);
	$('#start-time-specific').val(event.startTime);
	$('#end-time-specific').val(event.endTime);
	$('#remove-specific-link').css('display', 'block');

	doSpecificTimeRange(event.startTime, event.endTime);
}

function removeSpecificEvent() {
	var id = $('#specific-multidate-id').val();
	if (id.length > 0) {
	    if ( confirm('<?php putGS("Are you sure you want to clear the event"); ?>') ) {
	        removeEvent(id);
	        resetSpecificForm();
	    }
	}
}

function removeDaterangeEvent() {
	var id = $('#daterange-multidate-id').val();
    if (id.length > 0) {
        if ( confirm('<?php putGS("Are you sure you want to clear the event"); ?>' + id) ) {
            removeEvent(id);
            resetDaterangeForm();
        }
    }
}


function removeEvent(id) {
	var url = '<?php echo $Campsite['WEBSITE_URL']; ?>/admin/multidate/remove';
	var data = 'id=' + id;
    var flashDelete = flashMessage(localizer.processing, null, true);
    $.ajax({
        'url': url,
        'type': 'POST',
        'data': data,
        'dataType': 'json',
        'success': function(json) {
            flashDelete.fadeOut();
            $('#full-calendar').fullCalendar( 'refetchEvents' );
            
        },
        'error': function(json) {
        	flashDelete.fadeOut();
        }
    });
}

function eventClick(eventId) {
	
	var url = '<?php echo $Campsite['WEBSITE_URL']; ?>/admin/multidate/getevent';
	var data = 'id='+eventId;
	var flashClick = flashMessage(localizer.processing, null, true);
	$.ajax({
        'url': url,
        'type': 'POST',
        'data': data,
        'dataType': 'json',
        'success': function(json) {
        	//console.log(json);
        	//var isRecurring = json.isRecurring;
        	flashClick.fadeOut();
        	if (json.isRecurring) {
				loadDaterangeEvent(json);
        	} else {
				loadSpecificEvent(json);
        	}
        },
        'error': function(json) {
        	flashClick.fadeOut();
        }
    });
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

	var url = '<?php echo $Campsite['WEBSITE_URL']; ?>/admin/multidate/getdates';
	
	$('#full-calendar').fullCalendar({
			eventClick: function(calEvent, jsEvent, view) {
				eventClick(calEvent.id);
			},
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
		 	editable: false,
			events: {
				url : url,
				type : 'GET',
				data : {
					articleId : "<?php echo $articleId?>"
				},
			},
			eventDrop: function(event, delta) {
				alert(event.title + ' was moved ' + delta + ' days\n' +
					'(should probably update your database)');
			},
			loading: function(bool) {
				if (bool) $('#loading').show();
				else $('#loading').hide();
			},
			timeFormat: 'H(:mm)'
	});
	 
	 $("#start-date-specific").datepicker({ dateFormat: 'yy-mm-dd' });
	 
	 $('#start-date-daterange').datepicker({ dateFormat: 'yy-mm-dd' });
	 $('#end-date-daterange').datepicker({ dateFormat: 'yy-mm-dd' });
	 
	 $('#start-time-specific').timepicker({stepHour: 1, stepMinute: 1});
	 $('#end-time-specific').timepicker({stepHour: 1, stepMinute: 1});
	 $('#start-time-daterange').timepicker({stepHour: 1, stepMinute: 1});
	 $('#end-time-daterange').timepicker({stepHour: 1, stepMinute: 1});

	 $('#daterange-all-day').click(function() {
		 if ($('#daterange-all-day').attr('checked') != 'checked') {
			 $('#start-time-daterange').css('display', 'inline');
			 $('#end-time-daterange').css('display', 'inline');
		 } else {
			 $('#start-time-daterange').css('display', 'none');
			 $('#end-time-daterange').css('display', 'none');
		 }
		
	 });

	$('#cycle-ends-on-set-date').click(function(){
		$('#end-date-daterange').css('visibility', 'visible');
		});
	
	$('#cycle-ends-never').click(function(){
		$('#end-date-daterange').css('visibility', 'hidden');
		});
		    
});


</script>

</head>
<body onLoad="return false;" style="background: none repeat scroll 0 0 #FFFFFF;">




<div class="content">
<div id="multidate-box" class="multidate-box">
<div class="toolbar">
	<div class="save-button-bar">
	    <input type="submit" name="cancel" value="<?php echo putGS('Close'); ?>" class="default-button" onclick="popup_close();" id="context_button_close">
	</div>
<h2><?php echo putGS('Multi date event'); ?></h2>
</div>


<div class="multi-box">

<div class="dates" id="specific-dates" style="display:block">
<form id="specific-dates-form" onsubmit="return false;">
    <input type="hidden" name="article-number" value="<?php echo Input::Get('f_article_number', 'int', 1)?>" />
    <input type="hidden" name="date-type" value="specific" />
    <input type="hidden" name="multidateId" id="specific-multidate-id" value="" />
    
	<div class="date-switch date-range-switch" style="margin-left: 12px;"><?php echo putGS('Date from / to'); ?></div>
    <div class="date-switch date-specific-switch switch-active border-left"><?php echo putGS('Specific dates'); ?></div>
	
    <input type="text" id="start-date-specific" name="start-date-specific" class="multidate-input date-input"style="width: 125px; margin-left: 12px; margin-top: 20px;" readonly='true'/> 
	<input type="text" id="start-time-specific" name="start-time-specific" class="multidate-input time-input" style="width: 128px; margin-left: 2px; margin-top: 20px;" readonly='true'/> 
	<input type="text" id="end-time-specific" name="end-time-specific" class="multidate-input time-input" style="width: 128px; margin-left: 144px; margin-top: 20px; display: none" readonly='true'/>
	
	<div class="specific-radio-holder">
		<input type="radio" id="specific-radio-start-only" name="specific-radio" value="start-only" checked="checked" /><?php echo putGS('Start time'); ?><br />
		<input type="radio" id="specific-radio-start-and-end" name="specific-radio" value="start-and-end" /><?php echo putGS('Start & end time'); ?><br />
		<input type="radio" id="specific-radio-all-day" name="specific-radio" value="all-day" /><?php echo putGS('All day'); ?>
	</div>
	<div class="form-action-holder">
		<input type="button" value="Reset form" onclick="resetSpecificForm()"; class="default-button" style="width:127px; margin-right:3px;"/>
		<input type="button" class="save-button-small" onclick="popup_save();" value="Save" style="width:129px;";/>
	</div>
	<a href="#" onclick="removeSpecificEvent()" class="remove-link" id="remove-specific-link" style="display:none"><?php putGS("Remove event")?></a>
</form>
</div>

<div class="dates" id="daterange-dates" style="display: none">
<form id="daterange-dates-form" onsubmit="return false;">
    <input type="hidden" name="article-number" value="<?php echo Input::Get('f_article_number', 'int', 1)?>" />
    <input type="hidden" name="date-type" value="daterange" />
    <input type="hidden" name="multidateId" id="daterange-multidate-id" value="" />
    
    <div class="date-switch date-range-switch" style="margin-left: 12px;"><?php echo putGS('Date from / to'); ?></div>
    <div class="date-switch date-specific-switch switch-active border-left"><?php echo putGS('Specific dates'); ?></div>
    
    <input type="text" id="start-date-daterange" name="start-date-daterange" class="multidate-input date-input"style="width: 125px; margin-left: 12px; margin-top: 20px;"  readonly='true'/> 
    <input type="text" id="start-time-daterange" name="start-time-daterange" class="multidate-input time-input" style="width: 128px; margin-left: 2px; margin-top: 20px;" readonly='true'/>
    <span style="display:block; margin-left: 12px; margin-top: 10px;"><?php echo putGS('To'); ?></span> 
    <input type="text" id="end-date-daterange" name="end-date-daterange" class="multidate-input date-input"style="width: 125px; margin-left: 12px; margin-top: 10px;"  readonly='true'/> 
    <input type="text" id="end-time-daterange" name="end-time-daterange" class="multidate-input time-input" style="width: 128px; margin-left: 2px; margin-top: 10px;"  readonly='true'/>
    
    <div class="repeats-checkbox-holder">
        <input type="checkbox" id="daterange-all-day" name="daterange-all-day" value="1" /><label for="daterange-all-day"><?php echo putGS('All day'); ?></label><br />
       <!-- <input type="checkbox" id="daterange-repeats" name="daterange-repeats" value="1" /><?php echo putGS('Repeats'); ?><br /> -->
    </div>
    
    <select id="repeats-cycle" class="multidate-input" style="margin-left: 12px; margin-top: 10px; width: 260px;" name="repeats-cycle">
        <option value='recurring:daily'><?php echo putGS('Repeats daily'); ?></option>
        <option value='recurring:weekly'><?php echo putGS('Repeats weekly'); ?></option>
        <option value='recurring:monthly'><?php echo putGS('Repeats monthly'); ?></option>
    </select>
    
    <!-- 
     <div class="repeats-checkbox-holder">
        <input type="checkbox" id="monday" name="day-repeat" value="moday"/>M
        <input type="checkbox" id="tuesday" name="day-repeat" value="tuesday"/>T
        <input type="checkbox" id="wednesday" name="day-repeat" value="wednesday"/>W
        <input type="checkbox" id="thursday" name="day-repeat" value="thursday"/>T
        <input type="checkbox" id="friday" name="day-repeat" value="friday"/>F
        <input type="checkbox" id="saturday" name="day-repeat" value="saturday"/>S
        <input type="checkbox" id="sunday" name="day-repeat" value="sunday"/>S
    </div>
     -->
    
     <div class="repeats-checkbox-holder">
       <?php echo putGS('Ends'); ?>
       <input type="radio" id="cycle-ends-on-set-date" name="cycle-ends"  value="on-set-date" style="display: inline; margin-left:25px;" name="cycle-ends" checked="checked"/><?php echo putGS('On set date');?><br />
       <input type="radio" id="cycle-ends-never" name="cycle-ends" value="never" style="display: inline; margin-left:73px;" /><?php echo putGS('Never');?><br />
    </div>
    <div class="form-action-holder">
		<input type="button" value="Reset form" onclick="resetDaterangeForm()"; class="default-button" style="width:127px; margin-right:3px;"/>
		<input type="button" class="save-button-small" onclick="popup_save();" value="Save" style="width:129px;";/>
	</div>
	<a href="#" onclick="removeDaterangeEvent()" class="remove-link" id="remove-daterange-link" style="display:none"><?php putGS("Remove event")?></a>
</form>
</div>

<div id="full-calendar" class="full-calendar" style=""></div>
</div>
</div>
</div>
</body>
</html>





