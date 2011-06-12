<?php
camp_load_translation_strings("article_type_fields");
camp_load_translation_strings("api");
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleType.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Translation.php');

$articleTypeName = Input::Get('f_article_type');
// return value is sorted by language
$allLanguages = Language::GetLanguages(null, null, null, array(), array(), true);

$lang = camp_session_get('LoginLanguageId', 1);
$languageObj = new Language($lang);


$articleType = new ArticleType($articleTypeName);
$fields = $articleType->getUserDefinedColumns(null, true, true);

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array($articleTypeName, "");
$crumbs[] = array(getGS("Article type fields"), "");

echo camp_html_breadcrumbs($crumbs);

if ($g_user->hasPermission("ManageArticleTypes")) {
	include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");
	?>
<script>
var field_ids = new Array;
var allShown = 0;
</script>
<?php
for ($i = 0; $i < count($fields); $i++) { ?>
<script>
field_ids.push("translate_field_"+<?php p($i); ?>);
</script>
<?php } ?>

<TABLE class="action_buttons" STYLE="padding-top: 5px;" BORDER="0" CELLPADDING="1" CELLSPACING="0">
<TBODY>
<TR>
    <TD><A HREF="/<?php echo $ADMIN; ?>/article_types/"><IMG BORDER="0" SRC="<?php echo $Campsite['ADMIN_IMAGE_BASE_URL']; ?>/left_arrow.png"></A></TD>
    <TD><B><A HREF="/<?php echo $ADMIN; ?>/article_types/"><?php putGS("Back to Article Types List"); ?></A></B></TD>
</TR>
</TBODY>
</TABLE>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons">
<TR>
    <TD><A HREF="/<?php echo $ADMIN; ?>/article_types/fields/add.php?f_article_type=<?php print urlencode($articleTypeName); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A>
	</TD>
    <TD><B><A HREF="/<?php echo $ADMIN; ?>/article_types/fields/add.php?f_article_type=<?php print urlencode($articleTypeName); ?>" ><?php  putGS("Add new field"); ?></A></B>
	</TD>
	<TD><DIV STYLE="width:15px;"></DIV></TD>
		<TD><A HREF="javascript: void(0);"
               ONCLICK="if (allShown == 0) {
                            ShowAll(field_ids);
                            allShown = 1;
                            document.getElementById('showtext').innerHTML = '<?php putGS("Hide display names"); ?>';
                            document['show'].src='<?php print $Campsite['ADMIN_IMAGE_BASE_URL']; ?>/viewmagminus.png';
                        } else {
                            HideAll(field_ids);
                            allShown = 0;
                            document.getElementById('showtext').innerHTML = '<?php putGS("Show display names"); ?>';
                            document['show'].src='<?php print $Campsite['ADMIN_IMAGE_BASE_URL']; ?>/viewmagplus.png';
                        }">
		      <IMG NAME="show" SRC="<?php echo $Campsite['ADMIN_IMAGE_BASE_URL']; ?>/viewmagplus.png" BORDER="0"></A></TD>
    	<TD><B><A HREF="javascript: void(0);"
                    ONCLICK="if (allShown == 0) {
                                ShowAll(field_ids);
                                allShown = 1;
                                document.getElementById('showtext').innerHTML = '<?php putGS("Hide display names"); ?>';
                                document['show'].src='<?php print $Campsite['ADMIN_IMAGE_BASE_URL']; ?>/viewmagminus.png';
                                } else {
                                HideAll(field_ids);
                                allShown = 0;
                                document.getElementById('showtext').innerHTML = '<?php putGS("Show display names"); ?>';
                                document['show'].src='<?php print $Campsite['ADMIN_IMAGE_BASE_URL']; ?>/viewmagplus.png';
                                }"><DIV ID="showtext"><?php putGS("Show display names"); ?></DIV></A></B></TD>



</TR>
</TABLE>
<?php  } ?>

<P>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
<TR class="table_list_header">
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php putGS("Order"); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Template Field Name"); ?></B></TD>

	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Type"); ?></B></TD>
	<?php  if ($g_user->hasPermission("ManageArticleTypes")) { ?>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Display Name"); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Translate"); ?></B></TD>
    <TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Is Content"); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Show/Hide"); ?></B></TD>

	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Delete"); ?></B></TD>
	<?php  } ?>
</TR>
<?php
$color= 0;
$i = 0;
$duplicateFieldsCount = 0;
foreach ($fields as $field) {
	if ($field->getStatus() == 'hidden') {
		$hideShowText = getGS('show');
		$hideShowStatus = 'show';
		$hideShowImage = "is_hidden.png";
	}
	else {
		$hideShowText = getGS('hide');
		$hideShowStatus = 'hide';
		$hideShowImage = "is_shown.png";
	}
	if ($field->isContent()) {
	    $contentType = 'non content';
        $isContentField = 'true';
        $setContentField = 'false';
	} else {
	    $contentType = 'content';
        $isContentField = 'false';
        $setContentField = 'true';
	}
	$fieldName = $field->getPrintName();
	$article = new MetaArticle();
	if ($article->has_property($fieldName) || method_exists($article, $fieldName)) {
		$duplicateFieldName = true;
		$duplicateFieldsCount++;
	} else {
		$duplicateFieldName = false;
	}

?>



<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
	<TD>
		<TABLE><TR>
		<TD>
		<DIV STYLE="width: 15px;">
            <?php if (count($fields) > 1 && $i < count($fields) - 1) { ?><A HREF="/<?php echo $ADMIN; ?>/article_types/fields/do_reorder.php?f_article_type=<?php print urlencode($articleTypeName); ?>&f_field_name=<?php print $field->getPrintName(); ?>&f_move=down&<?php echo SecurityToken::URLParameter(); ?>"><IMG BORDER="0" SRC="<?php echo $Campsite['ADMIN_IMAGE_BASE_URL']; ?>/down_arrow.png"></A><?php } else { ?>&nbsp;<?php } ?>
		</DIV>
		</TD>
		<TD>
		<DIV STYLE="width: 15px;">
            <?php if (count($fields) > 1 && $i != 0) { ?><A HREF="/<?php echo $ADMIN; ?>/article_types/fields/do_reorder.php?f_article_type=<?php print urlencode($articleTypeName); ?>&f_field_name=<?php print $field->getPrintName(); ?>&f_move=up&<?php echo SecurityToken::URLParameter(); ?>"><IMG BORDER="0" SRC="<?php echo $Campsite['ADMIN_IMAGE_BASE_URL']; ?>/up_arrow.png"></A><?php } else { ?>&nbsp;<?php } ?>
		</DIV>
		</TD>
		</TR></TABLE>
	</TD>

	<TD>
        <?php if ($duplicateFieldName) { echo '<div class="failure_message">'; } ?>
        <A HREF="/<?php echo $ADMIN; ?>/article_types/fields/rename.php?f_article_type=<?php print urlencode($articleTypeName); ?>&f_field_name=<?php print $field->getPrintName(); ?>"><?php  print htmlspecialchars($field->getPrintName()); ?></A>&nbsp;
		<?php if ($duplicateFieldName) { echo '**</div>'; } ?>
	</TD>

	<TD>
        <A HREF="/<?php echo $ADMIN; ?>/article_types/fields/retype.php?f_article_type=<?php print urlencode($articleTypeName); ?>&f_field_name=<?php print $field->getPrintName(); ?>"><?php print htmlspecialchars($field->getVerboseTypeName($languageObj->getLanguageId())); ?></A>
	</TD>

	<TD>
		<?php print htmlspecialchars($field->getDisplayName()); ?> <?php print htmlspecialchars($field->getDisplayNameLanguageCode()); ?>
	</TD>

	<td>
		<a href="javascript: void(0);" onclick="HideAll(field_ids); ShowElement('translate_field_<?php p($i); ?>');"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/localizer.png" alt="<?php putGS("Translate"); ?>" title="<?php putGS("Translate"); ?>" border="0"></a>
	</td>

    <TD ALIGN="CENTER">
        <?php if ($field->getType() == ArticleTypeField::TYPE_BODY) { ?>
        <input type="checkbox" <?php if ($field->isContent()) { ?>checked<?php } ?> id="set_is_content_<?php echo $i; ?>" name="set_is_content_<?php echo $i; ?>" onclick="if (confirm('<?php putGS('Are you sure you want to make $1 a $2 field?', $field->getPrintName(), $contentType); ?>')) { location.href='/<?php p($ADMIN); ?>/article_types/fields/set_is_content.php?f_article_type=<?php print urlencode($articleTypeName); ?>&f_field_name=<?php  print urlencode($field->getPrintName()); ?>&f_is_content=<?php print $setContentField; ?>&<?php echo SecurityToken::URLParameter(); ?>' } else { document.getElementById('set_is_content_<?php echo $i; ?>').checked = <?php echo $isContentField; ?> }">
        <?php } else { ?>
        <?php putGS('N/A'); ?>
        <?php } ?>
    </TD>

	<TD ALIGN="CENTER">
		<A HREF="/<?php p($ADMIN); ?>/article_types/fields/do_hide.php?f_article_type=<?php print urlencode($articleTypeName); ?>&f_field_name=<?php  print urlencode($field->getPrintName()); ?>&f_status=<?php print $hideShowStatus; ?>&<?php echo SecurityToken::URLParameter(); ?>" onclick="return confirm('<?php putGS('Are you sure you want to $1 the article type field $2?', $hideShowText, $field->getPrintName()); ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/<?php echo $hideShowImage; ?>" BORDER="0" ALT="<?php  putGS('$1 article type field $2', ucfirst($hideShowText), $field->getPrintName()); ?>" TITLE="<?php  putGS('$1 article type $2', ucfirst($hideShowText), $field->getPrintName()); ?>" ></A>
	</TD>

	<?php  if ($g_user->hasPermission("ManageArticleTypes")) { ?>
	<TD ALIGN="CENTER">
		<A HREF="/<?php p($ADMIN); ?>/article_types/fields/do_del.php?f_article_type=<?php print urlencode($articleTypeName); ?>&f_field_name=<?php print urlencode($field->getPrintName()); ?>&<?php echo SecurityToken::URLParameter(); ?>" onclick="return confirm('<?php echo getGS('Are you sure you want to delete the field $1?', htmlspecialchars($field->getPrintName())).' '.getGS('You will also delete all fields with this name from all articles of this type from all publications.');  ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="<?php  putGS('Delete field $1', htmlspecialchars($field->getPrintName())); ?>" TITLE="<?php  putGS('Delete field $1', htmlspecialchars($field->getPrintName())); ?>" ></A>
	</TD>
	<?php } ?>
</TR>


    <tr id="translate_field_<?php p($i); ?>" style="display: none;"><td colspan="7">
    	<table>

		<?php
		$color2 = 0;
		$isFirstTranslation = true;
		$fieldTranslations = $field->getTranslations();
		foreach ($fieldTranslations as $languageId => $transName) {
		?>
		<TR <?php  if ($color2) { $color2 = 0; ?>class="list_row_even"<?php  } else { $color2 = 1; ?>class="list_row_odd"<?php  } ?>">
			<TD <?php if ($isFirstTranslation) { ?>style="border-top: 2px solid #8AACCE;"<?php } ?> valign="middle" align="center">
				<?php
				$language = new Language($languageId);
				p($language->getCode());
				?>
			</TD>
			<TD <?php if ($isFirstTranslation) { ?>style="border-top: 2px solid #8AACCE;"<?php } ?> valign="middle" align="left" width="450px">
				<?php
				echo htmlspecialchars($transName);
				?>
			</TD>
			</tr>
			<?php
			$isFirstTranslation = false;
		}
		?>




    	<tr>
    	<td colspan="2">
            <FORM method="POST" action="/<?php echo $ADMIN; ?>/article_types/fields/do_translate.php?f_article_type=<?php p($articleTypeName); ?>" >
			<?php echo SecurityToken::FormParameter(); ?>
    		<input type="hidden" name="f_field_id" value="<?php p($field->getPrintName()); ?>">
    		<table cellpadding="0" cellspacing="0" style="border-top: 1px solid #CFC467; border-bottom: 1px solid #CFC467; background-color: #FFFCDF ; padding-left: 5px; padding-right: 5px;" width="100%">
    		<tr>
    			<td align="left">
    				<table cellpadding="2" cellspacing="1">
    				<tr>
		    			<td><?php putGS("Add translation:"); ?></td>
		    			<td>
							<SELECT NAME="f_field_language_id" class="input_select" alt="select" emsg="<?php putGS("You must select a language."); ?>">
							<option value="0"><?php putGS("---Select language---"); ?></option>
							<?php
						 	foreach ($allLanguages as $tmpLanguage) {
						 		if ($languageObj->getLanguageId() == $tmpLanguage->getLanguageId()) $selected = true;
						 		else $selected = false;
						 	    camp_html_select_option($tmpLanguage->getLanguageId(),
						 								$selected,
						 								$tmpLanguage->getNativeName());
					        }
							?>
							</SELECT>
		    			</td>
		    			<td><input type="text" name="f_field_translation_name" value="" class="input_text" size="15" alt="blank" emsg="<?php putGS('You must enter a name for the field.'); ?>"></td>
		    			<td><input type="submit" name="f_submit" value="<?php putGS("Translate"); ?>" class="button"></td>
		    		</tr>
		    		</table>
		    	</td>
    		</tr>
    		</table>
    		</FORM>

    	</td>
    	</tr>
    	</table>
	</td>
    </tr>





<?php
$i++;
} // foreach
?>
</TABLE>
<?php
if ($duplicateFieldsCount > 0) {
	echo "<div class=\"indent\"><p class=\"failure_message\">** " . getGS('The field name was already in use as a base property of the article. The field content will not be displayed in the templates.') . "</p></div>";
}
?>
<?php camp_html_copyright_notice(); ?>
