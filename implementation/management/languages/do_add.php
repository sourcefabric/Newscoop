<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("$ADMIN_DIR/languages");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/localizer/Localizer.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");
require_once($_SERVER['DOCUMENT_ROOT']."/parser_utils.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission('ManageLanguages')) {
	CampsiteInterface::DisplayError("You do not have the right to add new languages.");
	exit;
}

$cName = Input::Get('cName');
$cCodePage = Input::Get('cCodePage');
$cOrigName = Input::Get('cOrigName');
$cCode = Input::Get('cCode');
$cMonth1 = Input::Get('cMonth1');
$cMonth2 = Input::Get('cMonth2');
$cMonth3 = Input::Get('cMonth3');
$cMonth4 = Input::Get('cMonth4');
$cMonth5 = Input::Get('cMonth5');
$cMonth6 = Input::Get('cMonth6');
$cMonth7 = Input::Get('cMonth7');
$cMonth8 = Input::Get('cMonth8');
$cMonth9 = Input::Get('cMonth9');
$cMonth10 = Input::Get('cMonth10');
$cMonth11 = Input::Get('cMonth11');
$cMonth12 = Input::Get('cMonth12');
$cWDay1 = Input::Get('cWDay1');
$cWDay2 = Input::Get('cWDay2');
$cWDay3 = Input::Get('cWDay3');
$cWDay4 = Input::Get('cWDay4');
$cWDay5 = Input::Get('cWDay5');
$cWDay6 = Input::Get('cWDay6');
$cWDay7 = Input::Get('cWDay7');
$Back = Input::Get('Back');

$correct= 1;
$created= 0;
if (($cName == "") || ($cOrigName == "") || ($cCodePage == "")) {
    $correct=0; 
}

if ($correct) {
	query("INSERT IGNORE INTO Languages SET Name='$cName', CodePage='$cCodePage', Code='$cCode', OrigName='$cOrigName', Month1='$cMonth1', Month2='$cMonth2', Month3='$cMonth3', Month4='$cMonth4', Month5='$cMonth5', Month6='$cMonth6', Month7='$cMonth7', Month8='$cMonth8', Month9='$cMonth9', Month10='$cMonth10', Month11='$cMonth11', Month12='$cMonth12', WDay1='$cWDay1', WDay2='$cWDay2', WDay3='$cWDay3', WDay4='4cWDay4', WDay5='$cWDay5', WDay6='$cWDay6', WDay7='$cWDay7'");
	$created= ($AFFECTED_ROWS > 0);
    query ("SELECT LAST_INSERT_ID()", 'lid');
	fetchRowNum($lid);
	$IdLang = getNumVar($lid,0);
	query("INSERT IGNORE INTO TimeUnits VALUES ('D', $IdLang, '$D'), ('W', $IdLang, '$W'), ('M', $IdLang, '$M'), ('Y', $IdLang, '$Y')");
	Localizer::CreateLanguageFilesRecursive($cCode);
}
if ($created) {
	create_language_links();
    $logtext = getGS('Language $1 added', $cName); 
    Log::Message($logtext, $User->getUserName(), 101);
}
?>
<HEAD>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
	<TITLE><?php  putGS("Adding new language"); ?></TITLE>
</HEAD>

<BODY>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
<TR>
	<TD class="page_title">
	    <?php  putGS("Adding new language"); ?>
	</TD>

	<TD ALIGN=RIGHT>
	   <TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
	   <TR>
	       <TD><A HREF="/admin/languages/" class="breadcrumb" ><?php  putGS("Languages");  ?></A></TD>
       </TR>
       </TABLE>
    </TD>
</TR>
</TABLE>

<P>
<CENTER>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Adding new language"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>

<TR>
	<TD COLSPAN="2">
	   <BLOCKQUOTE>
        <?php 
        if ($cName == "") { ?>
            <LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
            <?php  
        }
        if ($cOrigName == "") { ?>
           	<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Native name').'</B>'); ?></LI>
            <?php
        }
        if ($cCodePage == "") { ?>
            <LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Code page').'</B>'); ?></LI>
            <?php  
        }
        if ($created) {  ?>
            <LI><?php  putGS('The language $1 has been successfuly added.','<B>'.decS($cName).'</B>'); ?></LI>
            <?php  
        } else {
            if ($correct != 0) { ?>
                <LI><?php putGS('The language could not be added.'); ?></LI>
                <LI><?php  putGS('Please check if a language with the same name does not already exist.'); ?></LI>
                <?php  
            } 
        } ?>
        </BLOCKQUOTE>
    </TD>
</TR>
<TR>
	<TD COLSPAN="2">
    	<DIV ALIGN="CENTER">
        <?php  
        if (($correct) && ($created)) { ?>
            <INPUT TYPE="button" class="button" NAME="New" VALUE="<?php  putGS('Add another language'); ?>" ONCLICK="location.href='/admin/languages/add.php<?php  if ($Back) { ?>?Back=<?php  print encURL($Back); } ?>'">
    	   <INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='/admin/languages/'">
            <?php  
        } else { ?>
    	   <INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/admin/languages/add.php<?php  if ($Back) { ?>?Back=<?php  print encURL($Back); } ?>'">
            <?php  
        } ?>
        </DIV>
	</TD>
</TR>
</TABLE>
</CENTER>
<P>

<?php CampsiteInterface::CopyrightNotice(); ?>