<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
load_common_include_files("$ADMIN_DIR/pub/issues");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");

todefnum('TOL_UserId');
todefnum('TOL_UserKey');
query ("SELECT * FROM Users WHERE Id=$TOL_UserId AND KeyId=$TOL_UserKey", 'Usr');
$access=($NUM_ROWS != 0);
if ($NUM_ROWS) {
	fetchRow($Usr);
	query ("SELECT * FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'XPerm');
	if ($NUM_ROWS){
		fetchRow($XPerm);
	} else
		$access = 0;	//added lately; a non-admin can enter the administration area; he exists but doesn't have ANY rights
	$xpermrows= $NUM_ROWS;
} else {
	query ("SELECT * FROM UserPerm WHERE 1=0", 'XPerm');
}
?>

<HEAD>
	<TITLE><?php  putGS("Issue automatic publishing schedule"); ?></TITLE>
<?php if ($access == 0) { ?>	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/<?php echo $ADMIN; ?>/logout.php">
<?php }
    query ("SELECT * FROM Images WHERE 1=0", 'q_img');
?></HEAD>

<?php
if ($access) {
	if (getVar($XPerm,'Publish') == "Y")
		$pb=1;
	else 
		$pb=0;
?>
<BODY  BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">
<?php
	todefnum('Pub');
	todefnum('Issue');
	todefnum('Language');
	todef('publish_time');
?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD><?php  putGS("Issue automatic publishing schedule"); ?></TD>
		<TD ALIGN=RIGHT>
			<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
			<TR>
				<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/?Pub=<?php p($Pub); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Issues"); ?>"></A></TD><TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/?Pub=<?php p($Pub); ?>" ><B><?php  putGS("Issues");  ?></B></A></TD>
				<TD><A HREF="/<?php echo $ADMIN; ?>/pub/" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Publications"); ?>"></A></TD><TD><A HREF="/<?php echo $ADMIN; ?>/pub/" ><B><?php  putGS("Publications");  ?></B></A></TD>
			</TR>
			</TABLE>
		</TD>
	</TR>
</TABLE>

<?php
	query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
	if ($NUM_ROWS) {
		query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
		if ($NUM_ROWS) {
		    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');

		    fetchRow($q_iss);
		    fetchRow($q_pub);
		    fetchRow($q_lang);
?>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table">
<TR>
	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Publication"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php pgetHVar($q_pub,'Name'); ?></TD>

	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Issue"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php pgetHVar($q_iss,'Number'); ?>. <?php pgetHVar($q_iss,'Name'); ?> (<?php pgetHVar($q_lang,'Name'); ?>)</TD>
</TR>
</TABLE>

<?php
	if ($publish_time == "")
		$publish_time = date("Y-m-d H:i");
	if ($publish_time != "") {
		$sql = "select * from IssuePublish where IdPublication = $Pub and NrIssue = $Issue and IdLanguage = $Language and PublishTime = '$publish_time'";
		query($sql, 'q_autop');
		if ($NUM_ROWS > 0) {
			fetchRow($q_autop);
			$action = getVar($q_autop, 'Action');
			$publish_articles = getVar($q_autop, 'PublishArticles');
		}
		$datetime = explode(" ", trim($publish_time));
		$publish_date = $datetime[0];
		$publish_time = explode(":", trim($datetime[1]));
		$publish_hour = $publish_time[0];
		$publish_min = $publish_time[1];
	}
?>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="autopublish_do_add.php" >
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" BGCOLOR="#C0D0FF" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B><?php  putGS("Schedule a new publish action"); ?></B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php echo $Pub; ?>">
	<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php echo $Issue; ?>">
	<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php echo $Language; ?>">
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Date"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="publish_date" SIZE="10" MAXLENGTH="10" VALUE="<?php p($publish_date); ?>">
		<?php putGS('YYYY-MM-DD'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Time"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="publish_hour" SIZE="2" MAXLENGTH="2" VALUE="<?php p($publish_hour); ?>"> :
		<INPUT TYPE="TEXT" class="input_text" NAME="publish_min" SIZE="2" MAXLENGTH="2" VALUE="<?php p($publish_min); ?>">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Action"); ?>:</TD>
		<TD>
		<SELECT NAME="action">
			<OPTION VALUE=" ">---</OPTION>
			<OPTION VALUE="P" <?php if ($action == "P") echo "SELECTED"; ?>><?php putGS("Publish"); ?></OPTION>
			<OPTION VALUE="U" <?php if ($action == "U") echo "SELECTED"; ?>><?php putGS("Unpublish"); ?></OPTION>
		</SELECT>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Publish articles"); ?>:</TD>
		<TD>
		<SELECT NAME="publish_articles">
			<OPTION VALUE="Y" <?php if ($publish_articles == "Y") echo "SELECTED"; ?>><?php putGS("Yes"); ?></OPTION>
			<OPTION VALUE="N" <?php if ($publish_articles == "N") echo "SELECTED"; ?>><?php putGS("No"); ?></OPTION>
		</SELECT>
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>">
		<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/pub/issues/?Pub=<?php p($Pub); ?>&Language=<?php p($Language); ?>'">
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
</FORM>
</P>

<P><?php
	todefnum('Offs', 0);
	todefnum('lpp', 10);

	query ("SELECT * FROM IssuePublish WHERE IdPublication = $Pub AND NrIssue = $Issue AND IdLanguage = $Language ORDER BY PublishTime DESC LIMIT $Offs, ".($lpp+1), 'q_autop');
	if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i=$lpp;
	$color= 0;
	?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" WIDTH="100%">
	<TR BGCOLOR="#C0D0FF">
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Date/Time"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Action"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Publish articles"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Delete"); ?></B></TD>
	</TR>
<?php
    for($loop=0; $loop<$nr; $loop++) {
	fetchRow($q_autop);
	if ($i) {
		$url_publish_time = encURL(getVar($q_autop,'PublishTime'));
		?>	<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		<TD >
			<A HREF="/<?php echo $ADMIN; ?>/pub/issues/autopublish.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Language=<?php p($Language); ?>&publish_time=<?php echo $url_publish_time; ?>"><?php pgetHVar($q_autop,'PublishTime'); ?></A>
		</TD>
		<TD >
<?php
	$action = getVar($q_autop,'Action');
	if ($action == "P")
		putGS("Publish");
	else
		putGS("Unpublish");
?>&nbsp;
		</TD>
		<TD >
<?php
	$publish_articles = getVar($q_autop,'PublishArticles');
	if ($publish_articles == "Y")
		putGS("Yes");
	else
		putGS("No");
?>&nbsp;
		</TD>
		<TD ALIGN="CENTER">
			<A HREF="/<?php echo $ADMIN; ?>/pub/issues/autopublish_del.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Language=<?php p($Language); ?>&publish_time=<?php echo $url_publish_time; ?>"><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/x.gif" BORDER="0" ALT="<?php putGS('Delete entry'); ?>"></A>
		</TD>
	<?php } ?>
	</TR>
<?php
    $i--;
    }
?>	<TR><TD COLSPAN="2" NOWRAP>
<?php if ($Offs <= 0) { ?>		&lt;&lt; <?php  putGS('Previous'); ?>
<?php } else { ?>		<B><A HREF="autopublish.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Language=<?php p($Language); ?>&Offs=<?php p($Offs - $lpp); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
<?php } ?><?php if ($nr < $lpp+1) { ?>		 | <?php  putGS('Next'); ?> &gt;&gt;
<?php } else { ?>		 | <B><A HREF="autopublish.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Language=<?php p($Language); ?>&Offs=<?php p($Offs + $lpp); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
<?php } ?>	</TD></TR>
</TABLE>
<?php } else { ?><BLOCKQUOTE>
	<LI><?php putGS('No entries.'); ?></LI>
</BLOCKQUOTE>
<?php } ?>
<?php } else { ?><BLOCKQUOTE>
	<LI><?php putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<?php } ?>
<?php } else { ?><BLOCKQUOTE>
	<LI><?php putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<?php } ?>
<?php }
CampsiteInterface::CopyrightNotice();
?>
</BODY>

</HTML>
