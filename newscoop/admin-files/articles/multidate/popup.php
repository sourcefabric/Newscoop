<?php
$translator = \Zend_Registry::get('container')->getService('translator');
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Expires" content="now" />
<title><?php echo $translator->trans("Multi date events", array(), 'articles'); ?></title>
<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/calendar/fullcalendar.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/calendar/timepicker.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/form.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/content.css" />

<style type="text/css">
.type-holder {
    margin-top: 16px;
    margin-left: 10px;
}
select#multidatefield_specific {
    font-weight: bold;
}
select#multidatefield_range {
    font-weight: bold;
}
.comment-holder {
    margin-top: 10px;
    margin-left: 10px;
}
div.comment-holder textarea {
    font-size: 13px;
    border: 1px solid #9D9D9D;
}
</style>

<?php
$f_multidate_box = 1;
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/html_head.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');


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

$article_language_use = $f_language_selected;
if (empty($article_language_use)) {
    $article_language_use = $f_language_id;
}

$article = new Article($article_language_use, $articleId);

$article_type_name = $article->getType();
$article_type = new ArticleType($article_type_name);
$article_type_columns = $article_type->getUserDefinedColumns();
//var_dump($article_type_columns);

?>
<script src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/jquery/calendar/fullcalendar.min.js" type="text/javascript"></script>
<script type="text/javascript">

$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

function resetSpecificForm() {
	$('#specific-multidate-id').val('');
    $('#specific-radio-start-and-end').trigger('click');
	$('#start-date-specific').val('');
	$('#start-time-specific').val('');
	$('#end-time-specific').val('');
	$('#remove-specific-link').css('display','none');
    $('#multidatefield_specific option').eq(0).attr('selected', 'selected');
    $('#event_comment_specific').val('');
}

function resetDaterangeForm() { 
	$('#daterange-multidate-id').val('');
	$('#start-time-daterange').css('display', 'inline').val('');
	$('#start-time-daterange').css('visibility','visible').val('');
	$('#start-date-daterange').val('');
	$('#end-time-daterange').css('display', 'inline').val('');
	$('#end-time-daterange').css('visibility','visible').val('');
	$('#end-date-daterange').css('visibility','visible').val('');
	$('#daterange-all-day').removeAttr('checked');
	$('#cycle-ends-on-set-date').trigger('click');
	$('#remove-daterange-link').css('display','none');
    $('#multidatefield_range option').eq(0).attr('selected', 'selected');
    $('#event_comment_range').val('');
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
            parent.$.fancybox.message = '<?php echo $translator->trans('Events updated.', array(), 'articles'); ?>';
        }
        parent.$.fancybox.close();
    }
    catch (e) {window.close();}
}

function submitForm(formData) {
	var flash = flashMessage(localizer.processing, null, true);
	var url = '<?php echo $Campsite['WEBSITE_URL']; ?>/admin/multidate/add';

    callServer(
        url,
        formData,
        function(res) {
        	$('#full-calendar').fullCalendar( 'refetchEvents' );
        	flash.fadeOut();
        	resetSpecificForm();
        	resetDaterangeForm();
        },
        true
    );
}

function submitSpecificForm() {

	//validating specific form
	var valid = 1;
	if ( $('#start-date-specific').val() == '' ) {
		valid = 0;
		alert("<?php echo $translator->trans('Start date can not be empty', array(), 'articles'); ?>");
		$('#start-date-specific').focus();
	}
	var radio = $('input:radio[name=specific-radio]:checked').val();
	if (radio == 'start-only' || radio == 'start-and-end') {
		if ( valid == 1) {
			if ( $('#start-time-specific').val() == '' ) {
				valid = 0;
				alert("<?php echo $translator->trans('Start time can not be empty', array(), 'articles')?>");
				$('#start-time-specific').focus();
			}
		}
		if (radio == 'start-and-end' && valid == 1) {
			if ( $('#end-time-specific').val() == '' ) {
				alert("<?php echo $translator->trans('End time can not be empty', array(), 'articles') ?>");
				valid = 0;
				$('#end-time-specific').focus();
			}

			if (valid == 1) {
				if ( !timeOk($('#start-date-specific').val(), $('#start-time-specific').val(), $('#start-date-specific').val(), $('#end-time-specific').val()) ) {
					valid = 0;
					alert("<?php echo $translator->trans('End time can not be set before start time', array(), 'articles')?>");
					$('#end-time-specific').focus();
				}
			}		
		}
	}
	
	if (valid == 1) {
		formData = $('#specific-dates-form').serializeObject();
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
		alert("<?php echo $translator->trans('Start date can not be empty', array(), 'articles')?>");
		$('#start-date-daterange').focus();
	}
	//start time
	if ( valid == 1 && needTime) {
		if ( $('#start-time-daterange').val() == '' ) {
			valid = 0;
			alert("<?php echo $translator->trans("Start time can not be empty", array(), 'articles')?>");
			$('#start-time-daterange').focus();
		}
	}
	//end date
	if (valid == 1 && needDate) {
		if ( $('#end-date-daterange').val() == '' ) {
			valid = 0;
			alert("<?php echo $translator->trans("End date can not be empty", array(), 'articles')?>");
			$('#end-date-daterange').focus();
		}
	}
	//end time 
	if ( valid == 1 && needTime) {
		if ( $('#end-time-daterange').val() == '' ) {
			valid = 0;
			alert("<?php echo $translator->trans("End time can not be empty", array(), 'articles')?>");
			$('#end-time-daterange').focus();
		}
	}
	//end time is not set before start time
	if (valid == 1 && needTime) {
		var auxDate = "2000-01-01";
		var startTime = $('#start-time-daterange').val();
		var endTime = $('#end-time-daterange').val();
		
		if ( !timeOk(auxDate, startTime, auxDate, endTime) ) {
			valid = 0;
			alert("<?php echo $translator->trans("End time can not be set before start time", array(), 'articles')?>");
			$('#end-time-daterange').focus();
		}
	}	
	//last date is not set before first date
	if (valid == 1 && needDate) {
		var startDate = $('#start-date-daterange').val();
        var endDate = $('#end-date-daterange').val();
		var auxTime = "00:00";
		
		if ( !timeOk(startDate, auxTime, endDate, auxTime) ) {
			valid = 0;
			alert("<?php echo $translator->trans("Last date can not be set before first date", array(), 'articles')?>");
			$('#end-date-daterange').focus();
		}
	}	
	
	
	
	if (valid == 1) {
		formData = $('#daterange-dates-form').serializeObject();
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
    $('#start-time-specific').css('display', 'inline');
    $('#end-time-specific').css('visibility', 'hidden');
}

function reset_specific_start_end_time() {
    $('#specific-radio-start-and-end').attr('checked', 'checked');
    $('#start-time-specific').css('display', 'inline');
    $('#end-time-specific').css('visibility', 'visible');
}

function reset_specific_all_day() {
    $('#specific-radio-all-day').attr('checked', 'checked');
    $('#start-time-specific').css('display', 'none');
    $('#end-time-specific').css('visibility', 'hidden');
}

function loadDaterangeEvent(event) {
	resetDaterangeForm();
	$('.date-range-switch').trigger('click');
	$('#daterange-multidate-id').val(event.id);
	$('#start-date-daterange').val(event.startDate);
	$('#start-time-daterange').val(event.startTime);
	$('#end-date-daterange').val(event.endDate);
	$('#end-time-daterange').val(event.endTime);
	var repeatValue = ''+event.isRecurring;
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
		$('#start-time-daterange').css('visibility', 'hidden');
		$('#end-time-daterange').css('visibility', 'hidden');
	}
    $('#multidatefield_range').val(event.field_name);
    $('#event_comment_range').val(event.event_comment);
	
}

function doSpecificTimeRange(all_day, rest_of_day, start, end) {
    if ( all_day ) {
		$('#specific-radio-all-day').trigger('click');
        return;
	}

    if ( rest_of_day ) {
		$('#specific-radio-start-only').trigger('click');
        return;
	}

	$('#specific-radio-start-and-end').trigger('click');
}

function loadSpecificEvent(event) {
	$('.date-specific-switch').trigger('click');
	$('#specific-multidate-id').val(event.id);
	$('#start-date-specific').val(event.startDate);
	$('#start-time-specific').val(event.startTime);
	$('#end-time-specific').val(event.endTime);
	$('#remove-specific-link').css('display', 'block');

    doSpecificTimeRange(event.allDay, event.restOfDay, event.startTime, event.endTime);
    $('#multidatefield_specific').val(event.field_name);
    $('#event_comment_specific').val(event.event_comment);
}

function removeSpecificEvent() {
	var id = $('#specific-multidate-id').val();
	if (id.length > 0) {
	    if ( confirm('<?php echo $translator->trans("Are you sure you want to clear the event?", array(), 'articles'); ?>') ) {
	        removeEvent(id);
	        resetSpecificForm();
	    }
	}
}

function removeDaterangeEvent() {
	var id = $('#daterange-multidate-id').val();
    if (id.length > 0) {
        if ( confirm('<?php echo $translator->trans("Are you sure you want to clear the event?", array(), 'articles'); ?>' ) ) {
            removeEvent(id);
            resetDaterangeForm();
        }
    }
}


function removeEvent(id) {
	var url = '<?php echo $Campsite['WEBSITE_URL']; ?>/admin/multidate/remove';
	var data = {'id': id};
    var flashDelete = flashMessage(localizer.processing, null, true);

    callServer(
        url,
        data,
        function(res) {
            flashDelete.fadeOut();
            $('#full-calendar').fullCalendar( 'refetchEvents' );
        },
        true
    );
}

function eventClick(eventId) {
	
	var url = '<?php echo $Campsite['WEBSITE_URL']; ?>/admin/multidate/getevent';
	var data = {'id': eventId};
	var flashClick = flashMessage(localizer.processing, null, true);

    callServer(
        {
            'url': url,
            'method': 'GET'
        },
        data,
        function(res) {
            var isRecurring = res.isRecurring;
            flashClick.fadeOut();
            if (res.isRecurring) {
                loadDaterangeEvent(res);
            } else {
                loadSpecificEvent(res);
            }
        },
        true
    );
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
            events: function (start, end, callback) {
                window.load_events(start, end, callback, url);
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
			 $('#start-time-daterange').css('visibility','visible');
			 $('#end-time-daterange').css('visibility','visible');
		 } else {
			 $('#start-time-daterange').css('visibility','hidden');
			 $('#end-time-daterange').css('visibility','hidden');
		 }
		
	 });

	$('#cycle-ends-on-set-date').click(function(){
		$('#end-date-daterange').css('visibility', 'visible');
		});
	
	$('#cycle-ends-never').click(function(){
		$('#end-date-daterange').css('visibility', 'hidden');
		});
		    
    $('#specific-radio-start-and-end').trigger('click');
});

window.load_events = function(start, end, callback, url) {
// TODO: use start/end to limit the amount of loaded data
    callServer(
        {
            'url': url,
            'method': 'GET'
        },
        {
            articleId : "<?php echo $articleId?>",
            languageId : "<?php echo $article->getLanguageId(); ?>"
        },
        function(res) {
            callback(res);
        },
        true
    );

};

</script>

</head>
<body onLoad="return false;" style="background: none repeat scroll 0 0 #FFFFFF;">




<div class="content">
<div id="multidate-box" class="multidate-box">
<div class="toolbar">
	<div class="save-button-bar">
	    <input type="submit" name="cancel" value="<?php echo $translator->trans('Close'); ?>" class="default-button" onclick="popup_close();" id="context_button_close">
	</div>
<h2><?php echo $translator->trans('Multi date events', array(), 'articles'); ?></h2>
</div>


<div class="multi-box">

<div class="dates" id="specific-dates" style="display:block">
<form id="specific-dates-form" onsubmit="return false;">
    <input type="hidden" name="article-number" value="<?php echo Input::Get('f_article_number', 'int', 1)?>" />
    <input type="hidden" name="date-type" value="specific" />
    <input type="hidden" name="multidateId" id="specific-multidate-id" value="" />
    
    <div class="date-switch date-specific-switch switch-active border-left" style="margin-left: 10px;"><?php echo $translator->trans('Specific dates', array(), 'articles'); ?></div>
	<div class="date-switch date-range-switch" style="margin-left: 4px;"><?php echo $translator->trans('Recurring', array(), 'articles'); ?></div>
	
    <input type="text" id="start-date-specific" name="start-date-specific" class="multidate-input date-input" style="width: 125px; margin-left: 12px; margin-top: 20px;" readonly='true' title="<?php echo $translator->trans('Event date', array(), 'articles'); ?>" /> 
	<input type="text" id="start-time-specific" name="start-time-specific" class="multidate-input time-input" style="width: 128px; margin-left: 2px; margin-top: 20px;" readonly='true' title="<?php echo $translator->trans('Event start time', array(), 'articles'); ?>" /> 
	<input type="text" id="end-time-specific" name="end-time-specific" class="multidate-input time-input" style="width: 128px; margin-left: 144px; margin-top: 20px; visibility: visible" readonly='true' title="<?php echo $translator->trans('Event end time', array(), 'articles'); ?>" />
	
	<div class="specific-radio-holder">
		<input type="radio" id="specific-radio-start-only" name="specific-radio" value="start-only" /><label for="specific-radio-start-only"><?php echo $translator->trans('Start time', array(), 'articles'); ?></label><br />
		<input type="radio" id="specific-radio-start-and-end" name="specific-radio" value="start-and-end" checked="checked" /><label for="specific-radio-start-and-end"><?php echo $translator->trans('Start & end time', array(), 'articles'); ?><br /></label>
		<input type="radio" id="specific-radio-all-day" name="specific-radio" value="all-day" /><label for="specific-radio-all-day"><?php echo $translator->trans('All day', array(), 'articles'); ?></label>
	</div>

    <div><hr style="width: 260px; margin-bottom: 10px;"></div>

        <div class="type-holder">
            <select name="multidatefield" id="multidatefield_specific" title="<?php echo $translator->trans('Event type', array(), 'articles'); ?>">
<?php
    foreach ($article_type_columns as $one_column_type) {
        if (ArticleTypeField::TYPE_COMPLEX_DATE != $one_column_type->getType()) {
            continue;
        }
        if ($one_column_type->isHidden()) {
            continue;
        }
        $field_name = $one_column_type->getPrintName();
        $option_str = '<option value="' . $field_name . '">' . $field_name . '';
        echo $option_str . "\n";
    }
?>
            </select>
        </div>
        <div class="comment-holder">
            <textarea name="event-comment" id="event_comment_specific" rows="4" cols="30" title="<?php echo $translator->trans('Event comment', array(), 'articles'); ?>"></textarea>
        </div>

	<div class="form-action-holder">
		<input type="button" value="Reset form" onclick="resetSpecificForm()"; class="default-button" style="width:127px; margin-right:3px;"/>
		<input type="button" class="save-button-small" onclick="popup_save();" value="Save" style="width:129px;";/>
	</div>
	<a href="#" onclick="removeSpecificEvent()" class="remove-link" id="remove-specific-link" style="display:none"><?php echo $translator->trans("Remove event", array(), 'articles'); ?></a>
</form>
</div>

<div class="dates" id="daterange-dates" style="display: none">
<form id="daterange-dates-form" onsubmit="return false;">
    <input type="hidden" name="article-number" value="<?php echo Input::Get('f_article_number', 'int', 1)?>" />
    <input type="hidden" name="date-type" value="daterange" />
    <input type="hidden" name="multidateId" id="daterange-multidate-id" value="" />
    
    <div class="date-switch date-specific-switch switch-active border-left" style="margin-left: 10px; margin-bottom: 10px;"><?php echo $translator->trans('Specific dates', array(), 'articles'); ?></div>
    <div class="date-switch date-range-switch" style="margin-left: 4px; margin-bottom: 10px;"><?php echo $translator->trans('Recurring', array(), 'articles'); ?></div>
    
    <input type="text" id="start-date-daterange" name="start-date-daterange" class="multidate-input date-input"style="width: 125px; margin-left: 12px; margin-top: 10px;"  readonly='true' title="<?php echo $translator->trans('Event first date', array(), 'articles'); ?>" /> 
    <input type="text" id="start-time-daterange" name="start-time-daterange" class="multidate-input time-input" style="width: 128px; margin-left: 2px; margin-top: 10px;" readonly='true' title="<?php echo $translator->trans('Event start time', array(), 'articles'); ?>" />
    
    <div class="repeats-checkbox-holder">
        <input type="checkbox" id="daterange-all-day" name="daterange-all-day" value="1" / style="margin-top: 12px;"><label for="daterange-all-day"><?php echo $translator->trans('All day', array(), 'articles'); ?></label>
        <input type="text" id="end-time-daterange" name="end-time-daterange" class="multidate-input time-input" style="float: right; width: 128px; margin-right: 11px; margin-top: 12px; margin-bottom: 20px;"  readonly='true' title="<?php echo $translator->trans('Event end time', array(), 'articles'); ?>" />
    </div>

    <div><hr style="width: 260px; margin-bottom: 10px;"></div>

    <select id="repeats-cycle" class="multidate-input" style="margin-left: 12px; margin-top: 10px; width: 260px;" name="repeats-cycle">
        <option value='daily'><?php echo $translator->trans('Repeats daily', array(), 'articles'); ?></option>
        <option value='weekly'><?php echo $translator->trans('Repeats weekly', array(), 'articles'); ?></option>
        <option value='monthly'><?php echo $translator->trans('Repeats monthly', array(), 'articles'); ?></option>
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

    <span style="display:block; margin-left: 16px; margin-top: 20px;"><?php echo $translator->trans('Till', array(), 'articles'); ?></span> 
    <input type="text" id="end-date-daterange" name="end-date-daterange" class="multidate-input date-input"style="width: 125px; margin-left: 12px; margin-top: 12px;"  readonly='true' title="<?php echo $translator->trans('Event last date', array(), 'articles'); ?>" /> 
    
     <div class="repeats-checkbox-holder">
       <?php echo $translator->trans('Ends', array(), 'articles'); ?>
       <input type="radio" id="cycle-ends-on-set-date" name="cycle-ends"  value="on-set-date" style="display: inline; margin-left:25px;" name="cycle-ends" checked="checked"/><label for="cycle-ends-on-set-date"><?php echo $translator->trans('On set date', array(), 'articles');?></label><br />
       <input type="radio" id="cycle-ends-never" name="cycle-ends" value="never" style="display: inline; margin-left:73px;" /><label for="cycle-ends-never"><?php echo $translator->trans('Never', array(), 'articles');?></label><br />
    </div>

    <div><hr style="width: 260px; margin-bottom: 10px;"></div>

        <div class="type-holder">
            <select name="multidatefield" id="multidatefield_range" title="<?php echo $translator->trans('Event type', array(), 'articles'); ?>">
<?php
    foreach ($article_type_columns as $one_column_type) {
        if (ArticleTypeField::TYPE_COMPLEX_DATE != $one_column_type->getType()) {
            continue;
        }
        if ($one_column_type->isHidden()) {
            continue;
        }
        $field_name = $one_column_type->getPrintName();
        $option_str = '<option value="' . $field_name . '">' . $field_name . '';
        echo $option_str . "\n";
    }
?>
            </select>
        </div>
        <div class="comment-holder">
            <textarea name="event-comment" id="event_comment_range" rows="4" cols="30" title="<?php echo $translator->trans('Event comment', array(), 'articles'); ?>"></textarea>
        </div>

    <div class="form-action-holder">
		<input type="button" value="Reset form" onclick="resetDaterangeForm()"; class="default-button" style="width:127px; margin-right:3px;"/>
		<input type="button" class="save-button-small" onclick="popup_save();" value="Save" style="width:129px;";/>
	</div>
	<a href="#" onclick="removeDaterangeEvent()" class="remove-link" id="remove-daterange-link" style="display:none"><?php echo $translator->trans("Remove event", array(), 'articles'); ?></a>
</form>
</div>

<div id="full-calendar" class="full-calendar" style=""></div>
</div>
</div>
</div>

<script type="text/javascript">
    $('input.time-input').click(function(e) {
        $('input.time-input').removeClass('clicked');
        $(e.target).addClass('clicked');
    });

    $('.ui-datepicker-close').live('click', function() {
        var input = $('input.time-input.clicked').first();
        if (input.val() == '') {
            input.val('00:00');
            input.removeClass('clicked');
        }
    });
</script>
</body>
</html>
