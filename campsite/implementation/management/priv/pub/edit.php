<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/pub_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/TimeUnit.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/UrlType.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Alias.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Language.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManagePub')) {
	camp_html_display_error(getGS("You do not have the right to edit publication information."));
	exit;
}

$Pub = Input::Get('Pub', 'int');
$TOL_Language = Input::Get('TOL_Language');

$languages = Language::GetLanguages();
$defaultLanguage = array_pop(Language::GetLanguages(null, $TOL_Language));
$urlTypes = UrlType::GetUrlTypes();
$timeUnits = TimeUnit::GetTimeUnits($TOL_Language);
$publicationObj =& new Publication($Pub);
$aliases = Alias::GetAliases(null, $Pub);

$pubTimeUnit =& new TimeUnit($publicationObj->getTimeUnit(), $publicationObj->getLanguageId());
if (!$pubTimeUnit->exists()) {
	$pubTimeUnit =& new TimeUnit($publicationObj->getTimeUnit(), 1);
}

echo camp_html_content_top(getGS("Configure publication"), array("Pub" => $publicationObj));
?> 
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
	<TD><A HREF="/<?php echo $ADMIN; ?>/pub/"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
	<TD><A HREF="/<?php echo $ADMIN; ?>/pub/"><B><?php  putGS("Publication List"); ?></B></A></TD>
	<TD style="padding-left: 20px;"><A HREF="/<?php echo $ADMIN; ?>/issues/?Pub=<?php  p($Pub); ?>"><B><?php  putGS("Go To Issues"); ?></B></A></TD>
	<TD ><A HREF="/<?php echo $ADMIN; ?>/issues/?Pub=<?php  p($Pub); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/go_to.png" BORDER="0"></A></TD>
</TR>
</TABLE>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons">
<TR>
<?php  if ($User->hasPermission("ManagePub")) { ?>    <P>
	<TD>
		<A HREF="/<?php echo $ADMIN; ?>/pub/add.php?Back=<?php p(urlencode($_SERVER['REQUEST_URI'])); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A>
	</TD>
	<TD>
		<A HREF="/<?php echo $ADMIN; ?>/pub/add.php?Back=<?php p(urlencode($_SERVER['REQUEST_URI'])); ?>"><B><?php  putGS("Add new publication"); ?></B></A>
	</TD>
<?php  } ?>
<?php
if ($User->hasPermission("DeletePub")) {
?>
	<TD style="padding-left: 10px;"><A HREF="do_del.php?Pub=<?php p($Pub); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the publication $1?', htmlspecialchars($publicationObj->getName())); ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0"></A></TD>
	<TD><A HREF="do_del.php?Pub=<?php p($Pub); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the publication $1?', htmlspecialchars($publicationObj->getName())); ?>');"><B><?php  putGS("Delete"); ?></B></A></TD>
<?php } ?>
</TR>
</TABLE>


<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_edit.php"  >
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Configure publication"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<tr><td colspan=2><b><?php putGS("General attributes"); ?></b></td></tr>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cName" VALUE="<?php  p(htmlspecialchars($publicationObj->getName())); ?>" SIZE="32" MAXLENGTH="255">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Default Site Alias"); ?>:</TD>
	<TD>
		<SELECT NAME="cDefaultAlias" class="input_select">
		<?php
			foreach ($aliases as $alias) {
				camp_html_select_option($alias->getId(), $publicationObj->getDefaultAliasId(), $alias->getName());		
			}
		?>
		</SELECT>&nbsp;
		<a href="/<?php p($ADMIN); ?>/pub/aliases.php?Pub=<?php echo $Pub ?>"><?php putGS("Edit aliases"); ?></a>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Default language"); ?>:</TD>
	<TD>
	<SELECT NAME="cLanguage" class="input_select">
	<?php 
	foreach ($languages as $language) {
		camp_html_select_option($language->getLanguageId(), $publicationObj->getDefaultLanguageId(), $language->getNativeName());
	}
	?>
	</SELECT>&nbsp;
<a href="/admin/languages/"><?php putGS("Edit languages"); ?></a>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("URL Type"); ?>:</TD>
	<TD>
	<SELECT NAME="cURLType" class="input_select">
	<?php
		foreach ($urlTypes as $urlType) {
			camp_html_select_option($urlType->getId(), $publicationObj->getUrlTypeId(), $urlType->getName());
		}
	?>
	</SELECT>
	</TD>
</TR>

<tr><td colspan=2><HR NOSHADE SIZE="1" COLOR="BLACK"></td></tr>
<tr><td colspan=2><b><?php putGS("Subscriptions defaults"); ?></b></td></tr>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Pay Period"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cPayTime" VALUE="<?php p(htmlspecialchars($publicationObj->getPayTime())); ?>" SIZE="5" MAXLENGTH="5"> <?php  p($pubTimeUnit->getName()); ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Time Unit"); ?>:</TD>
	<TD>
    <SELECT NAME="cTimeUnit" class="input_select">
	<?php 
	foreach ($timeUnits as $timeUnit) {
		camp_html_select_option($timeUnit->getUnit(), $publicationObj->getTimeUnit(), $timeUnit->getName());
	}
	?>
    </SELECT>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Unit Cost"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cUnitCost" VALUE="<?php  p($publicationObj->getUnitCost()); ?>" SIZE="10" MAXLENGTH="10">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Currency"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cCurrency" VALUE="<?php p(htmlspecialchars($publicationObj->getCurrency())); ?>" SIZE="10" MAXLENGTH="10">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Paid Period"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cPaid" VALUE="<?php p($publicationObj->getPaidTime()); ?>" SIZE="10" MAXLENGTH="10"> <?php  p($pubTimeUnit->getName()); ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Trial Period"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cTrial" VALUE="<?php p($publicationObj->getTrialTime()); ?>" SIZE="10" MAXLENGTH="10"> <?php  p($pubTimeUnit->getName()); ?>
	</TD>
</TR>
	<tr><td colspan=2 align=center><a href="deftime.php?Pub=<?php echo $Pub; ?>"><?php putGS("Countries defaults"); ?></a></td></tr>

	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
		<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
		<!--<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/pub/'">-->
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
</FORM>
<P>
<?php camp_html_copyright_notice(); ?>
