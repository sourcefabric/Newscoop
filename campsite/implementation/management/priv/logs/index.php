<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
load_common_include_files("logs");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Language.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/User.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Input.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Log.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Event.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ViewLogs')) {
	camp_html_display_error(getGS("You do not have the right to view logs."));
	exit;
}

$f_event_search_id = Input::Get('f_event_search_id', 'int', null, true);
$f_log_page_offset = Input::Get('f_log_page_offset', 'int', 0, true);
//$LogOffs = Input::Get('LogOffs', );
$events = Event::GetEvents();
if ($f_log_page_offset < 0) {
	$f_log_page_offset = 0;
}
$ItemsPerPage = 20;

$logs = Log::GetLogs($f_event_search_id, 
	array('LIMIT' => array('MAX_ROWS' => $ItemsPerPage, 'START' => $f_log_page_offset)));
$numLogLines = Log::GetNumLogs($f_event_search_id);

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Logs"), "");
echo camp_html_breadcrumbs($crumbs);

//    query ("SELECT Id, Name FROM Events WHERE 1=0", 'ee');
//    query ("SELECT TStamp, IdEvent, User, Text FROM Log WHERE 1=0", 'log'); 

?>
<p>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="action_buttons">
<TR>
	<TD ALIGN="RIGHT">
		<FORM METHOD="GET" ACTION="index.php" NAME="">
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="search_dialog">
		<TR>
			<TD><?php  putGS('Event'); ?>:</TD>
			<TD>
				<SELECT NAME="f_event_search_id" class="input_select">
				<OPTION VALUE="0">
				<?php 
				foreach ($events as $event) { 
					camp_html_select_option($event->getEventId(), $f_event_search_id, htmlspecialchars($event->getName()));
				}
				?>
				</SELECT>
			</TD>
			<TD><INPUT TYPE="submit" class="button" NAME="Search" VALUE="<?php  putGS('Search'); ?>"></TD>
		</TR>
		</TABLE>
		</FORM>
	</TD>
</TR>
</TABLE>

<P>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
<TR class="table_list_header">
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Date/Time"); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("User"); ?></B></TD>
	<?php  if ($f_event_search_id == 0) { ?>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Event"); ?></B></TD>
	<?php  } ?>
	<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Description"); ?></B></TD>
</TR>
<?php 
	$color=0;
	foreach ($logs as $entry) { ?>
	<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		<TD ALIGN="CENTER">
			<?php  p(htmlspecialchars($entry->getTimeStamp())); ?>
		</TD>
		
		<TD>
			<?php  p(htmlspecialchars($entry->getProperty("full_name"))); ?>&nbsp;
		</TD>
	
		<?php if ($f_event_search_id == 0) { ?>
		<TD>
			<?php  
			$event =& new Event($entry->getEventId(), 1);
			echo htmlspecialchars($event->getName());
			?>&nbsp;
		</TD>
		<?php  } ?>
		
		<TD>
			<?php  p(htmlspecialchars($entry->getText())); ?>
		</TD>
	</TR>
<?php 
}
?>	
<TR><TD COLSPAN="2" NOWRAP>
<?php  if ($f_log_page_offset > 0) { ?>
	<B><A HREF="index.php?sEvent=<?php  print urlencode($sEvent); ?>&f_log_page_offset=<?php  print max(0, ($f_log_page_offset - $ItemsPerPage)); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
	<?php  
} 
if ($numLogLines > ($f_log_page_offset + $ItemsPerPage)) { ?>
	 | <B><A HREF="index.php?sEvent=<?php  print urlencode($sEvent); ?>&f_log_page_offset=<?php print ($f_log_page_offset + $ItemsPerPage); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
<?php  } ?>	</TD></TR>
</TABLE>
<?php camp_html_copyright_notice(); ?>
