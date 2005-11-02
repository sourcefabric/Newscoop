<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("languages");
require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/languages.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
$languages = Language::GetLanguages();
$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Languages"), "");
$breadcrumbs = camp_html_breadcrumbs($crumbs);
echo $breadcrumbs;

if ($User->hasPermission('ManageLanguages')) { ?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="action_buttons">
<TR>
    <TD><A HREF="/<?php echo $ADMIN; ?>/languages/add_modify.php?Back=<?php print urlencode($_SERVER['REQUEST_URI']); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
    <TD><A HREF="/<?php echo $ADMIN; ?>/languages/add_modify.php?Back=<?php print urlencode($_SERVER['REQUEST_URI']); ?>" ><B><?php  putGS("Add new language"); ?></B></A></TD>
</TR>
</TABLE>
<?php  } ?>

<P>
<?php 
if (count($languages) > 0) {
	$color= 0; ?>
	<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
	<TR class="table_list_header">
		<TD ALIGN="LEFT" VALIGN="TOP"><?php  putGS("Language"); ?></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><?php  putGS("Native name"); ?></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><?php  putGS("Code"); ?></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><?php  putGS("Code page"); ?></TD>
        <?php if ($User->hasPermission('ManageLanguages')) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Edit"); ?></B></TD>
        <?php  }
        if ($User->hasPermission('DeleteLanguages')) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Delete"); ?></B></TD>
        <?php  } ?>
	</TR>
    <?php 
    foreach ($languages as $language) { ?>	
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
			<A HREF="/<?php echo $ADMIN; ?>/languages/add_modify.php?Lang=<?php p($language->getLanguageId()); ?>">Edit</A>
		</TD>
	<?php  }
	if ($User->hasPermission('DeleteLanguages')) { ?>
	<TD ALIGN="CENTER">
		<A HREF="/<?php echo $ADMIN; ?>/languages/do_del.php?Language=<?php p($language->getLanguageId()); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the language $1?', $language->getNativeName());?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="<?php  putGS('Delete language $1', htmlspecialchars($language->getNativeName())); ?>" TITLE="<?php  putGS('Delete language $1', htmlspecialchars($language->getNativeName())); ?>"></A>
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
<?php camp_html_copyright_notice(); ?>