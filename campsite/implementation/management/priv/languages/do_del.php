<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("$ADMIN_DIR/languages");
require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/languages.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('DeleteLanguages')) {
    print_r($User);
    exit;
	CampsiteInterface::DisplayError(getGS("You do not have the right to delete languages."));
	exit;
}

$Language = Input::Get('Language', 'int');
if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(getGS('Invalid input: $1', Input::GetErrorString()), $BackLink);
	exit;    
}

$languageObj =& new Language($Language);
if (!$languageObj->exists()) {
	header("Location: /$ADMIN/logout.php");
	exit;    
}

$del = 1;

query ("SELECT COUNT(*) FROM Publications WHERE IdDefaultLanguage=$Language", 'q_pub');
fetchRowNum($q_pub);
if (getNumVar($q_pub,0) != 0) {
	$del= 0; 
	$msg[] = putGS('There are $1 publication(s) left.',getNumVar($q_pub)); 
} 
    
query ("SELECT COUNT(*) FROM Issues WHERE IdLanguage=$Language", 'q_iss');
fetchRowNum($q_iss);
if (getNumVar($q_iss,0) != 0) {
    $del= 0; 
    $msg[] = putGS('There are $1 issue(s) left.',getNumVar($q_iss)); 
} 
    
query ("SELECT COUNT(*) FROM Sections WHERE IdLanguage=$Language", 'q_sect');
fetchRowNum($q_sect);
if (getNumVar($q_sect,0) != 0) {
    $del= 0; 
    $msg[] = putGS('There are $1 section(s) left.',getNumVar($q_sect)); 
} 
    
query ("SELECT COUNT(*) FROM Articles WHERE IdLanguage=$Language", 'q_art');
fetchRowNum($q_art);
if (getNumVar($q_art,0) != 0) {
    $del= 0; 
    $msg[] = putGS('There are $1 article(s) left.',getNumVar($q_art)); 
} 
    
query ("SELECT COUNT(*) FROM Dictionary WHERE IdLanguage=$Language", 'q_kwd');
fetchRowNum($q_kwd);
if (getNumVar($q_kwd,0) != 0) {
    $del= 0; 
    $msg[] = putGS('There are $1 keyword(s) left.',getNumVar($q_kwd)); 
}
    
query ("SELECT COUNT(*) FROM Classes WHERE IdLanguage=$Language", 'q_cls');
fetchRowNum($q_cls);
if (getNumVar($q_cls,0) != 0) {
    $del= 0; 
    $msg[] = putGS('There are $1 classes(s) left.',getNumVar($q_cls));     
}
    
query ("SELECT COUNT(*) FROM Countries WHERE IdLanguage=$Language", 'q_country');
fetchRowNum($q_country);
if (getNumVar($q_country,0) != 0) {
    $del= 0; 
    $msg[] = putGS('There are $1 countries left.',getNumVar($q_country));     
}

if ($del) {
	unlink($_SERVER['DOCUMENT_ROOT'] . "/" . $languageObj->getCode() . ".php");
	Localizer::DeleteLanguageFilesRecursive($languageObj->getCode());
	$languageObj->delete();
	$logtext = getGS('Language $1 deleted', $languageObj->getName()); 
	Log::Message($logtext, $User->getUserName(), 102);
	header("Location: /$ADMIN/languages/index.php");
	exit;
}
?>
<HEAD>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
	<TITLE><?php  putGS("Deleting language"); ?></TITLE>
</HEAD>

<BODY >

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
<TR>
	<TD class="page_title">
	    <?php  putGS("Deleting language"); ?>
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
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Deleting language"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	   <BLOCKQUOTE>
        <?php  
        foreach ($msg as $error) { ?>
            <LI><?php p($error); ?></LI>
            <?php
        }
        ?>
		<LI><?php  putGS('The language $1 could not be deleted.','<B>'.$languageObj->getNativeName().'</B>'); ?></LI>
	   </BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
    	<DIV ALIGN="CENTER">        
        	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/admin/languages/'">
    	</DIV>
	</TD>
</TR>
</TABLE></CENTER>
<P>

<?php CampsiteInterface::CopyrightNotice(); ?>