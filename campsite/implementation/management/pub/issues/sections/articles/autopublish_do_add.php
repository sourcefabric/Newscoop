<?PHP
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/pub/issues/sections/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticlePublish.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission("Publish")) {
	CampsiteInterface::DisplayError(getGS("You do not have the right to schedule issues or articles for automatic publishing."));
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$sLanguage = Input::Get('sLanguage', 'int', 0);
$Article = Input::Get('Article', 'int', 0);
$publishDate = trim(Input::Get('publish_date'));
$publishHour = trim(Input::Get('publish_hour', 'int', 0));
$publishMinute = trim(Input::Get('publish_min', 'int', 0));
$publishAction = Input::Get('publish_action', 'string', '', true);
$frontPageAction = Input::Get('front_page_action', 'string', '', true);
$sectionPageAction = Input::Get('section_page_action', 'string', '', true);
$BackLink = Input::Get('Back', 'string', "/$ADMIN/pub/issues/sections/articles/index.php"
                       ."?Pub=$Pub&Issue=$Issue&Section=$Section&sLanguage=$sLanguage&Language=$Language", 
                       true);
                       
if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(getGS('Invalid input: $1', Input::GetErrorString()), $BackLink);
	exit;	
}

$publicationObj =& new Publication($Pub);
if (!$publicationObj->exists()) {
	CampsiteInterface::DisplayError(getGS('Publication does not exist.'), $BackLink);
	exit;	
}

$issueObj =& new Issue($Pub, $Language, $Issue);
if (!$issueObj->exists()) {
	CampsiteInterface::DisplayError(getGS('Issue does not exist.'), $BackLink);
	exit;	
}

$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
if (!$sectionObj->exists()) {
	CampsiteInterface::DisplayError(getGS('Section does not exist.'), $BackLink);
	exit;	
}

$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
if (!$articleObj->exists()) {
	CampsiteInterface::DisplayError(getGS('Article does not exist.'), $BackLink);
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
	$redirect = CampsiteInterface::ArticleUrl($articleObj, $Language, "autopublish.php", $BackLink);
	header("Location: $redirect");
	exit;
}
$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 
				  'Section' => $sectionObj, 'Article'=>$articleObj);
CampsiteInterface::ContentTop(getGS("Scheduling a new publish action"), $topArray);
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
