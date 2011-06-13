<?php

require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/pub/pub_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Alias.php");

$Pub = Input::Get('Pub', 'int');

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;
}

$publicationObj = new Publication($Pub);
$aliases = Alias::GetAliases(null, $Pub);

camp_html_content_top(getGS("Publication Aliases"), array("Pub" => $publicationObj));

if ($g_user->hasPermission("ManagePub")) { ?>
<p>
<TABLE class="action_buttons">
<TR>
    <TD><A HREF="/<?php echo $ADMIN; ?>/pub/add_alias.php?Pub=<?php p($Pub); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
    <TD><A HREF="/<?php echo $ADMIN; ?>/pub/add_alias.php?Pub=<?php  p($Pub); ?>" ><B><?php  putGS("Add new alias"); ?></B></A></TD>
</TR>
</TABLE>
<?php } ?>

<P>
<?php
$color = 0;
?>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
<TR class="table_list_header">
	<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Alias (click to edit)"); ?></B></TD>
	<?php if ($g_user->hasPermission("ManagePub")) { ?>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Delete"); ?></B></TD>
	<?php } ?>
</TR>
<?php
foreach ($aliases as $alias) {  ?>
	<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		<TD>
			<?php if ($g_user->hasPermission("ManagePub")) { ?><A HREF="/<?php p($ADMIN); ?>/pub/edit_alias.php?Pub=<?php p($Pub); ?>&Alias=<?php p($alias->getId()); ?>"><?php } ?><?php  p(htmlspecialchars($alias->getName())); ?><?php if ($g_user->hasPermission("ManagePub")) { ?></A><?php } ?>
		</TD>
		<?php if ($g_user->hasPermission("ManagePub")) { ?>
		<TD ALIGN="CENTER">
			<?php
			if ($publicationObj->getDefaultAliasId() != $alias->getId()) { ?>
			<A HREF="/<?php p($ADMIN); ?>/pub/do_del_alias.php?Pub=<?php p($Pub); ?>&Alias=<?php p($alias->getId()); ?>&<?php echo SecurityToken::URLParameter(); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the alias $1?', $alias->getName()); ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="<?php  putGS('Delete alias $1', htmlspecialchars($alias->getName())); ?>" TITLE="<?php  putGS('Delete alias $1', htmlspecialchars($alias->getName())); ?>" ></A>
			<?php } ?>
		</TD>
		<?php } ?>
	</TR>
<?php
}
?>

</TABLE>

<?php camp_html_copyright_notice(); ?>
