<?PHP
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/pub/issues/sections/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticlePublish.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission("Publish")) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS("You do not have the right to schedule issues or articles for automatic publishing." )));
	exit;
}

$Pub = Input::get('Pub', 'int', 0);
$Issue = Input::get('Issue', 'int', 0);
$Section = Input::get('Section', 'int', 0);
$Language = Input::get('Language', 'int', 0);
$sLanguage = Input::get('sLanguage', 'int', 0);
$Article = Input::get('Article', 'int', 0);
$publishDate = trim(Input::get('publish_date'));
$publishHour = trim(Input::get('publish_hour', 'int', 0));
$publishMinute = trim(Input::get('publish_min', 'int', 0));
$publishAction = Input::get('publish_action', 'string', '', true);
$frontPageAction = Input::get('front_page_action', 'string', '', true);
$sectionPageAction = Input::get('section_page_action', 'string', '', true);
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

$correct = true;
$created = false;
if ( ( ($publishDate == "") || ($publishHour == "") || ($publishMinute == "") ) 
	|| ( ($publishAction != "P") && ($publishAction != "U")
	&& ($frontPageAction != "S") && ($frontPageAction != "R")
	&& ($sectionPageAction != "S") && ($sectionPageAction != "R") ) ) {
	$correct = false;
}
else {
	$publishTime = $publishDate . " " . $publishHour . ":" . $publishMinute . ":00";
	$articlePublishObj =& new ArticlePublish($Article, $sLanguage, $publishTime);
	if (!$articlePublishObj->exists()) {
		$articlePublishObj->create();
		$created = true;
	}
	if ($publishAction == "P" || $publishAction == "U") {
		$articlePublishObj->setPublishAction($publishAction);
	}
	if ($frontPageAction == "S" || $frontPageAction == "R") {
		$articlePublishObj->setFrontPageAction($frontPageAction);
	}
	if ($sectionPageAction == "S" || $sectionPageAction == "R") {
		$articlePublishObj->setSectionPageAction($sectionPageAction);
	}
	header("Location: /$ADMIN/pub/issues/sections/articles/autopublish.php?Pub=$Pub&Issue=$Issue&Section=$Section&Article=$Article&Language=$Language&sLanguage=$sLanguage");
	exit;
}
ArticleTop($articleObj, $Language, "Scheduling a new publish action");
if ($articleObj->getPublished() != 'N') {
?>

<P>
<CENTER>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Scheduling a new publish action"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2"><BLOCKQUOTE>
	<?php 
	if ($publishDate == "") { ?>	
		<LI><?php putGS('You must complete the $1 field.','<B>'.getGS('Date').'</B>' ); ?></LI>
		<?php 
	}

	if ($publishHour == "" || $publishMinute == "") { ?>	
		<LI><?php putGS('You must complete the $1 field.','<B>'.getGS('Time').'</B>' ); ?></LI>
		<?php 
	}

	if ( ($publishAction != "P") && ($publishAction != "U")
		&& ($frontPageAction != "S") && ($frontPageAction != "R")
		&& ($sectionPageAction != "S") && ($sectionPageAction != "R") ) {?>	
		<LI><?php putGS('You must select an action.'); ?></LI>
    <?php 
	}
	?>	
	</BLOCKQUOTE>
	</TD>
	</TR>
	<TR>
		<TD COLSPAN="2" align="center">
			<INPUT TYPE="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/pub/issues/sections/articles/autopublish.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Section=<?php p($Section); ?>&Article=<?php p($Article); ?>&Language=<?php p($Language); ?>&sLanguage=<?php p($sLanguage); ?>'" class="button">
		</TD>
	</TR>
</TABLE>
</CENTER>
<P>

<?php } else { ?><BLOCKQUOTE>
	<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Scheduling a new publish action"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE><?php putGS("The article is new; it is not possible to schedule it for automatic publishing."); ?></BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
	<INPUT TYPE="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/pub/issues/sections/articles/edit.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Section=<?php p($Section); ?>&Article=<?php p($Article); ?>&Language=<?php p($Language); ?>&sLanguage=<?php p($sLanguage); ?>'">
		</DIV>
		</TD>
	</TR>
	</TABLE></CENTER>
</BLOCKQUOTE>
<?php } 
CampsiteInterface::CopyrightNotice();
?>
</BODY>

</HTML>
