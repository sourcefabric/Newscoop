<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("$ADMIN_DIR/languages");
require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/languages.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
$languages = Language::GetAllLanguages();
?>
<HEAD>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
	<TITLE><?php  putGS("Languages"); ?></TITLE>
</HEAD>


<BODY >
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
<TR>
	<TD class="page_title">
	    <?php  putGS("Languages"); ?>
	</TD>
</tr>
</TABLE>

<?php if ($User->hasPermission('ManageLanguages')) { ?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
<TR>
    <TD><A HREF="add_modify.php?Back=<?php print urlencode($_SERVER['REQUEST_URI']); ?>" ><IMG SRC="/admin/img/icon/add.png" BORDER="0"></A></TD>
    <TD><A HREF="add_modify.php?Back=<?php print urlencode($_SERVER['REQUEST_URI']); ?>" ><B><?php  putGS("Add new language"); ?></B></A></TD>
</TR>
</TABLE>
<?php  } ?>

<P>
<?php 
if (count($languages) > 0) {
	$color= 0; ?>
	<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" WIDTH="100%" class="table_list">
	<TR class="table_list_header">
		<TD ALIGN="LEFT" VALIGN="TOP"><?php  putGS("Language"); ?></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><?php  putGS("Native name"); ?></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><?php  putGS("Code"); ?></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><?php  putGS("Code page"); ?></TD>
        <?php if ($User->hasPermission('ManageLanguages')) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Edit"); ?></B></TD>
        <?php  }
        if ($User->hasPermission('DeleteLanguages')) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Delete"); ?></B></TD>
        <?php  } ?>
	</TR>
    <?php 
    foreach ($languages as $language) {
    //for($loop=0;$loop<$nr;$loop++) {
	//fetchRow($Languages);
	//if ($i) { ?>	
	<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		<TD>
			<?php  p(htmlspecialchars($language->getName())); ?>
		</TD>
		<TD>
			<?php p(htmlspecialchars($language->getNativeName())); ?>
		</TD>
		<TD>
			<?php p(htmlspecialchars($language->getCode())); ?>&nbsp;
		</TD>
		<TD >
			<?php p(htmlspecialchars($language->getCodePage())); ?>&nbsp;
		</TD>
	    <?php if ($User->hasPermission('ManageLanguages')) { ?> 
		<TD ALIGN="CENTER">
			<A HREF="add_modify.php?Lang=<?php p($language->getLanguageId()); ?>">Edit</A>
		</TD>
	<?php  }
	if ($User->hasPermission('DeleteLanguages')) { ?>
	<TD ALIGN="CENTER">
		<A HREF="/admin/languages/do_del.php?Language=<?php p($language->getLanguageId()); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the language $1?', $language->getNativeName());?>');"><IMG SRC="/<?php p($ADMIN); ?>/img/icon/delete.png" BORDER="0" ALT="<?php  putGS('Delete language $1', htmlspecialchars($language->getNativeName())); ?>" TITLE="<?php  putGS('Delete language $1', htmlspecialchars($language->getNativeName())); ?>"></A>
		</TD>
	<?php  } ?>
	</TR>
    <?php 
    } // foreach
} else { ?>
    <BLOCKQUOTE>
	<LI><?php  putGS('No language.'); ?></LI>
    </BLOCKQUOTE>
<?php  } ?>
<?php CampsiteInterface::CopyrightNotice(); ?>