<?PHP
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/pub/issues/sections/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticlePublish.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission("Publish")) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS("You do not have the right to schedule issues or articles for automatic publishing.")));
	exit;
}

$Pub = Input::get('Pub', 'int', 0);
$Issue = Input::get('Issue', 'int', 0);
$Section = Input::get('Section', 'int', 0);
$Language = Input::get('Language', 'int', 0);
$sLanguage = Input::get('sLanguage', 'int', 0);
$Article = Input::get('Article', 'int', 0);
$publishTime = Input::get('publish_time', 'string', '', true);
$BackLink = Input::get('Back', 'string', "/$ADMIN/pub/issues/sections/articles/index.php", true);

if (!Input::isValid()) {
	header("Location: /$ADMIN/logout.php");
	exit;	
}

$publicationObj =& new Publication($Pub);
if (!$publicationObj->exists()) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS('Publication does not exist.')));
	exit;	
}

$issueObj =& new Issue($Pub, $Language, $Issue);
if (!$issueObj->exists()) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS('Issue does not exist.')));
	exit;	
}

$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
if (!$sectionObj->exists()) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS('Section does not exist.')));
	exit;	
}

$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
if (!$articleObj->exists()) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS('Article does not exist.')));
	exit;
}

$languageObj =& new Language($Language);
$sLanguageObj =& new Language($sLanguage);
$articleEvents =& ArticlePublish::GetArticleEvents($Article, $sLanguage);

ArticleTop($articleObj, $Language, "Article automatic publishing schedule");

if ($articleObj->getPublished() != 'N') {
	if ($publishTime == '') {
		$publishTime = date("Y-m-d H:i");
	}
	$publishAction = '';
	$frontPageAction = '';
	$sectionPageAction = '';
	if ($publishTime != "") {
		$articlePublishObj =& new ArticlePublish($Article, $sLanguage, $publishTime);
		if ($articlePublishObj->exists()) {
			$publishAction = $articlePublishObj->getPublishAction();
			$frontPageAction = $articlePublishObj->getFrontPageAction();
			$sectionPageAction = $articlePublishObj->getSectionPageAction();
		}
		$datetime = explode(" ", trim($publishTime));
		$publishDate = $datetime[0];
		$publishTime = explode(":", trim($datetime[1]));
		$publishHour = $publishTime[0];
		$publishMinute = $publishTime[1];
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
	<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php echo $Section; ?>">
	<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<?php echo $Article; ?>">
	<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php echo $Language; ?>">
	<INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<?php echo $sLanguage; ?>">
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Date"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" NAME="publish_date" SIZE="11" MAXLENGTH="10" VALUE="<?php p($publishDate); ?>" class="input_text">
		<?php putGS('YYYY-MM-DD'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Time"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" NAME="publish_hour" SIZE="2" MAXLENGTH="2" VALUE="<?php p($publishHour); ?>" class="input_text"> :
		<INPUT TYPE="TEXT" NAME="publish_min" SIZE="2" MAXLENGTH="2" VALUE="<?php p($publishMinute); ?>" class="input_text">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="CENTER" COLSPAN="2"><b><?php  putGS("Actions"); ?></b></TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Publish"); ?>:</TD>
		<TD>
		<SELECT NAME="publish_action" class="input_select">
			<OPTION VALUE=" ">---</OPTION>
			<OPTION VALUE="P" <?php if ($publishAction == "P") echo "SELECTED"; ?>><?php putGS("Publish"); ?></OPTION>
			<OPTION VALUE="U" <?php if ($publishAction == "U") echo "SELECTED"; ?>><?php putGS("Unpublish"); ?></OPTION>
		</SELECT>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Front page"); ?>:</TD>
		<TD>
		<SELECT NAME="front_page_action">
			<OPTION VALUE=" ">---</OPTION>
			<OPTION VALUE="S" <?php if ($frontPageAction == "S") echo "SELECTED"; ?>><?php putGS("Show on front page"); ?></OPTION>
			<OPTION VALUE="R" <?php if ($frontPageAction == "R") echo "SELECTED"; ?>><?php putGS("Remove from front page"); ?></OPTION>
		</SELECT>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Section page"); ?>:</TD>
		<TD>
		<SELECT NAME="section_page_action">
			<OPTION VALUE=" ">---</OPTION>
			<OPTION VALUE="S" <?php if ($sectionPageAction == "S") echo "SELECTED"; ?>><?php putGS("Show on section page"); ?></OPTION>
			<OPTION VALUE="R" <?php if ($sectionPageAction == "R") echo "SELECTED"; ?>><?php putGS("Remove from section page"); ?></OPTION>
		</SELECT>
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2" align="center">
		<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>" class="button">
		<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/pub/issues/sections/articles/edit.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Section=<?php p($Section); ?>&Article=<?php p($Article); ?>&Language=<?php p($Language); ?>&sLanguage=<?php p($sLanguage); ?>'" class="button">
		</TD>
	</TR>
</TABLE></CENTER>
</FORM>
</P>

<P>
<?php
	if (count($articleEvents) > 0) {
	$color= 0;
	?>
	<center>
	<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" WIDTH="550px">
	<TR BGCOLOR="#C0D0FF">
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Date/Time"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Publish"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Front page"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Section page"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Delete"); ?></B></TD>
	</TR>
	<?php
	foreach ($articleEvents as $event) {
		$url_publish_time = urlencode($event->getPublishTime());
		?>	<TR <?php  if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php  } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php  } ?>>
		<TD>
			<A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/autopublish.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Section=<?php p($Section); ?>&Article=<?php p($Article); ?>&Language=<?php p($Language); ?>&sLanguage=<?php p($sLanguage); ?>&publish_time=<?php echo $url_publish_time; ?>"><?php p(htmlspecialchars($event->getPublishTime())); ?></A>
		</TD>
		
		<TD>
			<?php
			$publishAction = $event->getPublishAction();
			if ($publishAction == "P") {
				putGS("Publish");
			}
			if ($publishAction == "U") {
				putGS("Unpublish");
			}
			?>&nbsp;
		</TD>
		
		<TD>
			<?php
			$frontPageAction = $event->getFrontPageAction();
			if ($frontPageAction == "S") {
				putGS("Show");
			}
			if ($frontPageAction == "R") {
				putGS("Remove");
			}
			?>&nbsp;
		</TD>
		
		<TD>
			<?php
			$sectionPageAction = $event->getSectionPageAction();
			if ($sectionPageAction == "S") {
				putGS("Show");
			}
			if ($sectionPageAction == "R") {
				putGS("Remove");
			}
			?>&nbsp;
		</TD>
		
		<TD ALIGN="CENTER">
			<A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/autopublish_del.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Section=<?php p($Section); ?>&Article=<?php p($Article); ?>&Language=<?php p($Language); ?>&sLanguage=<?php p($sLanguage); ?>&publish_time=<?php echo $url_publish_time; ?>"><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/x.gif" BORDER="0" ALT="<?php putGS('Delete entry'); ?>"></A>
		</TD>
	</TR>
	<?php
    } // foreach
	?>	
	</TABLE>
	</center>
	<?php 
	} // if (count($articleEvents) > 0) 
} 
else { ?>
	<BLOCKQUOTE>
	<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" BGCOLOR="#C0D0FF" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Scheduling a new publish action"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE><?php putGS("The article is new; it is not possible to schedule it for automatic publishing.");?></BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD COLSPAN="2" align="center">
			<INPUT TYPE="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/pub/issues/sections/articles/edit.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Section=<?php p($Section); ?>&Article=<?php p($Article); ?>&Language=<?php p($Language); ?>&sLanguage=<?php p($sLanguage); ?>'" class="button">
		</TD>
	</TR>
	</TABLE></CENTER>
	</BLOCKQUOTE>
<?php 
} 
CampsiteInterface::CopyrightNotice();
?>