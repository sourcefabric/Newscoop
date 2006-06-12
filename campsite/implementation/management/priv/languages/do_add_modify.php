<?php
camp_load_translation_strings("languages");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/localizer/Localizer.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/TimeUnit.php');
require_once($_SERVER['DOCUMENT_ROOT']."/parser_utils.php");

if (!$g_user->hasPermission('ManageLanguages')) {
	camp_html_display_error(getGS("You do not have the right to add new languages."));
	exit;
}

$cLang = Input::Get('cLang', 'int', 0, true);
$editMode = ($cLang != 0);
$cName = Input::Get('cName');
$cOrigName = Input::Get('cOrigName');
$cCode = Input::Get('cCode');
$cMonth1 = Input::Get('cMonth1', 'string', '', true);
$cMonth2 = Input::Get('cMonth2', 'string', '', true);
$cMonth3 = Input::Get('cMonth3', 'string', '', true);
$cMonth4 = Input::Get('cMonth4', 'string', '', true);
$cMonth5 = Input::Get('cMonth5', 'string', '', true);
$cMonth6 = Input::Get('cMonth6', 'string', '', true);
$cMonth7 = Input::Get('cMonth7', 'string', '', true);
$cMonth8 = Input::Get('cMonth8', 'string', '', true);
$cMonth9 = Input::Get('cMonth9', 'string', '', true);
$cMonth10 = Input::Get('cMonth10', 'string', '', true);
$cMonth11 = Input::Get('cMonth11', 'string', '', true);
$cMonth12 = Input::Get('cMonth12', 'string', '', true);
$cWDay1 = Input::Get('cWDay1', 'string', '', true);
$cWDay2 = Input::Get('cWDay2', 'string', '', true);
$cWDay3 = Input::Get('cWDay3', 'string', '', true);
$cWDay4 = Input::Get('cWDay4', 'string', '', true);
$cWDay5 = Input::Get('cWDay5', 'string', '', true);
$cWDay6 = Input::Get('cWDay6', 'string', '', true);
$cWDay7 = Input::Get('cWDay7', 'string', '', true);
$Back = Input::Get('Back', 'string', '', true);
$D = Input::Get('D', 'string', '', true);
$W = Input::Get('W', 'string', '', true);
$M = Input::Get('M', 'string', '', true);
$Y = Input::Get('Y', 'string', '', true);

$correct = 1;
$created = 0;
if (($cName == "") || ($cOrigName == "") || ($cCode == "") ) {
    $correct = 0;
}

if ($editMode) {
    $languageObj =& new Language($cLang);
}

if ($correct) {
	$columns = array('Name' => $cName,
					 'Code' => $cCode,
					 'OrigName' => $cOrigName,
					 'Month1' => $cMonth1,
					 'Month2' => $cMonth2,
					 'Month3' => $cMonth3,
					 'Month4' => $cMonth4,
					 'Month5' => $cMonth5,
					 'Month6' => $cMonth6,
					 'Month7' => $cMonth7,
					 'Month8' => $cMonth8,
					 'Month9' => $cMonth9,
					 'Month10' => $cMonth10,
					 'Month11' => $cMonth11,
					 'Month12' => $cMonth12,
					 'WDay1' => $cWDay1,
					 'WDay2' => $cWDay2,
					 'WDay3' => $cWDay3,
					 'WDay4' => $cWDay4,
					 'WDay5' => $cWDay5,
					 'WDay6' => $cWDay6,
					 'WDay7' => $cWDay7);
    if ($editMode) {
		$languageObj->update($columns);
    }
    else {
    	$languageObj =& new Language();
    	$languageObj->create($columns);
    	$cLang = $languageObj->getLanguageId();
    }
	TimeUnit::SetTimeUnit('D', $cLang, $D);
	TimeUnit::SetTimeUnit('W', $cLang, $W);
	TimeUnit::SetTimeUnit('M', $cLang, $M);
	TimeUnit::SetTimeUnit('Y', $cLang, $Y);
    header("Location: /$ADMIN/languages/index.php");
    exit;
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Languages"), "/$ADMIN/languages/");
$crumbs[] = array(getGS("Adding new language"), "");
$breadcrumbs = camp_html_breadcrumbs($crumbs);
echo $breadcrumbs;

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
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
        if ($cCode == "") { ?>
            <LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Code').'</B>'); ?></LI>
            <?php
        }
        ?>
        </BLOCKQUOTE>
    </TD>
</TR>
<TR>
	<TD COLSPAN="2">
    	<DIV ALIGN="CENTER">
    	   <INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/languages/add_modify.php<?php if ($editMode) { p("?Lang=".$cLang); } ?>'">
        </DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>