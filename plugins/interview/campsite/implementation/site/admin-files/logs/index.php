<?php
camp_load_translation_strings("logs");
camp_load_translation_strings("api");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Language.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/User.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Input.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Log.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Event.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/SimplePager.php');

if (!$g_user->hasPermission('ViewLogs')) {
	camp_html_display_error(getGS("You do not have the right to view logs."));
	exit;
}

$f_event_search_id = Input::Get('f_event_search_id', 'int', null, true);
$f_log_page_offset = camp_session_get('f_log_page_offset', 0);
if ($f_event_search_id == 0) {
	$f_event_search_id = null;
}

$events = Event::GetEvents();
if ($f_log_page_offset < 0) {
	$f_log_page_offset = 0;
}
$ItemsPerPage = 15;

$logs = Log::GetLogs($f_event_search_id,
	array('LIMIT' => array('MAX_ROWS' => $ItemsPerPage, 'START' => $f_log_page_offset)));
$numLogLines = Log::GetNumLogs($f_event_search_id);

$pager = new SimplePager($numLogLines, $ItemsPerPage, "f_log_page_offset", "index.php?f_event_search_id=".urlencode($f_event_search_id)."&");

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Logs"), "");
echo camp_html_breadcrumbs($crumbs);

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

<table class="indent">
<TR>
	<TD>
		<?php echo $pager->render(); ?>
	</TD>
</TR>
</TABLE>
<?php if (count($logs) > 0) { ?>
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
			$event = new Event($entry->getEventId(), 1);
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
</table>
<table class="indent">
<TR>
	<TD>
		<?php echo $pager->render(); ?>
	</TD>
</TR>
</TABLE>
<?php } else { ?>
	<BLOCKQUOTE>
	<LI><?php  putGS('No events.'); ?></LI>
	</BLOCKQUOTE>
<?php } ?>
<?php camp_html_copyright_notice(); ?>
