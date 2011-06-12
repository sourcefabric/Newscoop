<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/country/country_common.php");
require_once($GLOBALS['g_campsiteDir']. "/classes/SimplePager.php");
camp_load_translation_strings("api");

if (!$g_user->hasPermission('ManageCountries') && !$g_user->hasPermission('DeleteCountries')) {
	camp_html_goto_page("/$ADMIN/");
}

$f_country_language_selected = camp_session_get('f_country_language_selected', '');
$f_country_offset = camp_session_get('f_country_offset', 0);
if (empty($f_country_language_selected)) {
	$f_country_language_selected = null;
}
$ItemsPerPage = 20;
$languages = Language::GetLanguages(null, null, null, array(), array(), true);
$countries = Country::GetCountries($f_country_language_selected, null, null,
				array("LIMIT" => array("START" => $f_country_offset, "MAX_ROWS" => $ItemsPerPage)));
$numCountries = Country::GetNumCountries($f_country_language_selected);

$pager = new SimplePager($numCountries, $ItemsPerPage, "f_country_offset", "index.php?");

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Countries"), "");
echo camp_html_breadcrumbs($crumbs);

?>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" class="action_buttons">
<?php  if ($g_user->hasPermission("ManageCountries")) { ?>
<TR>
	<TD>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
		<TR>
            <TD><A HREF="/<?php echo $ADMIN; ?>/country/add.php"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
            <TD><A HREF="/<?php echo $ADMIN; ?>/country/add.php"><B><?php  putGS("Add new country"); ?></B></A></TD>
		</TR>
		</TABLE>
	</TD>
</TR>
<?php  } ?>
<TR>
	<TD ALIGN="RIGHT">
		<FORM METHOD="GET" ACTION="" NAME="">
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="search_dialog">
		<TR>
			<TD><?php  putGS('Language') ?>:</TD>
			<TD>
				<SELECT NAME="f_country_language_selected" class="input_select">
				<OPTION></option>
				<?php
				foreach ($languages as $language) {
					camp_html_select_option($language->getLanguageId(), $f_country_language_selected, $language->getNativeName());
			    }
				?>
				</SELECT>
			</TD>
			<TD><INPUT TYPE="submit" class="button" NAME="Search" VALUE="<?php  putGS('Search'); ?>"></TD>
		</TR>
		</TABLE>
		</FORM>
	</TD>
</TR>
</TABLE>
<p>

<table class="action_buttons">
<TR>
	<TD>
		<?php  echo $pager->render(); ?>
	</TD>
</TR>
</TABLE>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
<TR class="table_list_header">
	<?php  if ($g_user->hasPermission("ManageCountries")) { ?>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Name <SMALL>(click to edit)</SMALL>"); ?></B></TD>
	<?php  } else { ?>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Name"); ?></B></TD>
	<?php  } ?>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Language"); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Code"); ?></B></TD>
	<?php  if ($g_user->hasPermission("ManageCountries")) { ?>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Translate"); ?></B></TD>
	<?php  }
	if ($g_user->hasPermission("DeleteCountries")) { ?>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Delete"); ?></B></TD>
	<?php  } ?>
</TR>

<?php
$color = 0;
$previousCountryCode = "xx";
foreach ($countries as $country) { ?>
    <TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
	<?php  if ($g_user->hasPermission("ManageCountries")) { ?>
	<TD <?php  if ($previousCountryCode == $country->getCode()) { ?>class="translation_indent"<?php } ?>>
		<A HREF="/<?php p($ADMIN); ?>/country/edit.php?f_country_code=<?php  p(urlencode($country->getCode())); ?>&f_country_language=<?php  p(urlencode($country->getLanguageId())); ?>"><?php p(htmlspecialchars($country->getName())); ?>&nbsp;</A>
	</TD>

	<?php  } else { ?>
	<TD <?php  if ($previousCountryCode == $country->getCode()) { ?>class="translation_indent"<?php } ?>>
		<?php  p(htmlspecialchars($country->getName())); ?>&nbsp;
	</TD>
	<?php  } ?>

	<TD>
	<?php
	$language = new Language($country->getLanguageId());
	p(htmlspecialchars($language->getNativeName()));
	?>
	</TD>

	<TD ALIGN="CENTER">
	<?php
		p(htmlspecialchars($country->getCode()));
	?>
    </TD>

    <?php  if ($g_user->hasPermission("ManageCountries")) { ?>
		<TD ALIGN="CENTER">
			<A HREF="/<?php p($ADMIN); ?>/country/translate.php?f_country_code=<?php p(urlencode($country->getCode())); ?>&f_country_language=<?php p($country->getLanguageId()); ?>"><?php  putGS("Translate"); ?></A>
		</TD>
	<?php  }
	if ($g_user->hasPermission("DeleteCountries")) { ?>
		<TD ALIGN="CENTER">
			<A HREF="/<?php p($ADMIN); ?>/country/do_del.php?f_country_code=<?php p(urlencode($country->getCode())); ?>&f_country_language=<?php p($country->getLanguageId()); ?>&<?php echo SecurityToken::URLParameter(); ?>" onclick="return confirm('<?php  putGS('Are you sure you want to delete the country $1?' ,htmlspecialchars($country->getName()).' ('.htmlspecialchars($language->getNativeName()).')'); ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="Delete country <?php p(htmlspecialchars($country->getName())); ?>" TITLE="Delete country <?php  p(htmlspecialchars($country->getName())); ?>" ></A>
		</TD>
	<?php  } ?>
	</TR>
	<?php
	$previousCountryCode = $country->getCode();
} ?>
</table>
<table class="action_buttons">
<TR>
	<TD>
		<?php  echo $pager->render(); ?>
	</TD>
</TR>
</TABLE>
<?php camp_html_copyright_notice(); ?>
