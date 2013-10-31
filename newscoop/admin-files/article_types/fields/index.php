<?php
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleType.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Translation.php');

$translator = \Zend_Registry::get('container')->getService('translator');

if (!Saas::singleton()->hasPermission('ManageArticleTypes')) {
    camp_html_display_error($translator->trans("You do not have the right to manage article types.", array(), 'article_type_fields'));
    exit;
}

$articleTypeName = Input::Get('f_article_type');
// return value is sorted by language
$allLanguages = Language::GetLanguages(null, null, null, array(), array(), true);

$lang = camp_session_get('LoginLanguageId', 1);
$languageObj = new Language($lang);


$articleType = new ArticleType($articleTypeName);
$fields = $articleType->getUserDefinedColumns(null, true, true);

$crumbs = array();
$crumbs[] = array($translator->trans("Configure"), "");
$crumbs[] = array($translator->trans("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array($articleTypeName, "");
$crumbs[] = array($translator->trans("Article type fields", array(), 'article_type_fields'), "");

echo camp_html_breadcrumbs($crumbs);

$row_rank = 0;

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

<style type="text/css">
.color_sel_hidden {
    display: none;
}
.color_sel_visible {
    margin-top: -8px;
    border-color: #c0c0c0;
    border-width: 8px;
    border-style: solid;
    margin-left: 25px;
    position: absolute;
}
.color_one_current {
    border-color: #404040;
    border-width: 1px;
    border-style: solid;

    float:left;
    width:14px;
    height:14px;
    cursor:pointer
}
.color_one_list {
    border-color: #404040;
    border-width: 1px;
    border-style: solid;

    float:right;
    width:14px;
    height:14px;
    cursor:pointer
}

</style>
<script type="text/javascript">

window.save_field_color = function(article_type, field_name, color_value) {

    var url = '<?php echo $Campsite['WEBSITE_URL']; ?>/admin/multidate/setfieldcolor?f_article_type=' + article_type + '&f_field_name=' + field_name + '&f_color_value=' + color_value;

    callServer(['ArticleTypeField', 'SetFieldColor'], [
        article_type, field_name, color_value
    ], function(msg) {
        flashMessage(msg);
    });

};

</script>

<?php

$color_list = array(
'#ff4040',
'#ff4080',
'#ff8040',
'#ff8080',

'#ff40ff',

'#40ff40',
'#80ff40',
'#40ff80',
'#80ff80',

'#ffff40',

'#4040ff',
'#8040ff',
'#4080ff',
'#8080ff',

'#40ffff',

'#808080',
);

?>

<TABLE class="action_buttons" STYLE="padding-top: 5px;" BORDER="0" CELLPADDING="1" CELLSPACING="0">
<TBODY>
<TR>
    <TD><A HREF="/<?php echo $ADMIN; ?>/article_types/"><IMG BORDER="0" SRC="<?php echo $Campsite['ADMIN_IMAGE_BASE_URL']; ?>/left_arrow.png"></A></TD>
    <TD><B><A HREF="/<?php echo $ADMIN; ?>/article_types/"></A></B></TD>
</TR>
</TBODY>
</TABLE>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons">
<TR>
    <TD><A HREF="/<?php echo $ADMIN; ?>/article_types/fields/add.php?f_article_type=<?php print urlencode($articleTypeName); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A>
	</TD>
    <TD><B><A HREF="/<?php echo $ADMIN; ?>/article_types/fields/add.php?f_article_type=<?php print urlencode($articleTypeName); ?>" ><?php  echo $translator->trans("Add new field"); ?></A></B>
	</TD>
	<TD><DIV STYLE="width:15px;"></DIV></TD>
		<TD><A HREF="javascript: void(0);"
               ONCLICK="if (allShown == 0) {
                            ShowAll(field_ids);
                            allShown = 1;
                            document.getElementById('showtext').innerHTML = '<?php echo $translator->trans("Hide human-readable field names", array(), 'article_type_fields'); ?>';
                            document['show'].src='<?php print $Campsite['ADMIN_IMAGE_BASE_URL']; ?>/viewmagminus.png';
                        } else {
                            HideAll(field_ids);
                            allShown = 0;
                            document.getElementById('showtext').innerHTML = '<?php echo $translator->trans("Edit and translate human-readable field names", array(), 'article_type_fields'); ?>';
                            document['show'].src='<?php print $Campsite['ADMIN_IMAGE_BASE_URL']; ?>/viewmagplus.png';
                        }">
		      <IMG NAME="show" SRC="<?php echo $Campsite['ADMIN_IMAGE_BASE_URL']; ?>/viewmagplus.png" BORDER="0"></A></TD>
    	<TD><B><A HREF="javascript: void(0);"
                    ONCLICK="if (allShown == 0) {
                                ShowAll(field_ids);
                                allShown = 1;
                                document.getElementById('showtext').innerHTML = '<?php echo $translator->trans("Hide human-readable field names", array(), 'article_type_fields'); ?>';
                                document['show'].src='<?php print $Campsite['ADMIN_IMAGE_BASE_URL']; ?>/viewmagminus.png';
                                } else {
                                HideAll(field_ids);
                                allShown = 0;
                                document.getElementById('showtext').innerHTML = '<?php echo $translator->trans("Edit and translate human-readable field names", array(), 'article_type_fields'); ?>';
                                document['show'].src='<?php print $Campsite['ADMIN_IMAGE_BASE_URL']; ?>/viewmagplus.png';
                                }"><DIV ID="showtext"><?php echo $translator->trans("Edit and translate human-readable field names", array(), 'article_type_fields'); ?></DIV></A></B></TD>



</TR>
</TABLE>
<?php  } ?>

<P>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
<TR class="table_list_header">
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php echo $translator->trans("Order"); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php echo $translator->trans("Template Field Name", array(), 'article_type_fields'); ?></B></TD>

	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php echo $translator->trans("Type"); ?></B></TD>
	<?php  if ($g_user->hasPermission("ManageArticleTypes")) { ?>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php echo $translator->trans("Display Name", array(), 'article_type_fields'); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php echo $translator->trans("Translate"); ?></B></TD>
    <TD ALIGN="LEFT" VALIGN="TOP"><B><?php echo $translator->trans("Is Content", array(), 'article_type_fields'); ?></B></TD>
    <TD ALIGN="LEFT" VALIGN="TOP"><B><?php echo $translator->trans("Event Color", array(), 'article_type_fields'); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php echo $translator->trans("Show/Hide", array(), 'article_type_fields'); ?></B></TD>

	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php echo $translator->trans("Delete"); ?></B></TD>
	<?php  } ?>
</TR>
<?php
$color= 0;
$i = 0;
$duplicateFieldsCount = 0;
foreach ($fields as $field) {
        $row_rank += 1;

	if ($field->getStatus() == 'hidden') {
		$hideShowText = $translator->trans('show', array(), 'article_type_fields');
		$hideShowStatus = 'show';
		$hideShowImage = "is_hidden.png";
	}
	else {
		$hideShowText = $translator->trans('hide', array(), 'article_type_fields');
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
		<a href="javascript: void(0);" onclick="HideAll(field_ids); ShowElement('translate_field_<?php p($i); ?>');"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/localizer.png" alt="<?php echo $translator->trans("Translate"); ?>" title="<?php echo $translator->trans("Translate"); ?>" border="0"></a>
	</td>

    <TD ALIGN="CENTER">
        <?php if ($field->getType() == ArticleTypeField::TYPE_BODY) { ?>
        <input type="checkbox" title="<?php echo $translator->trans('Usage at automatic statistics', array(), 'article_type_fields'); ?>" <?php if ($field->isContent()) { ?>checked<?php } ?> id="set_is_content_<?php echo $i; ?>" name="set_is_content_<?php echo $i; ?>" 
        onclick="if (confirm('<?php echo $translator->trans('Are you sure you want to make $1 a $2 field?', array('$1' => $field->getPrintName(), '$2' => $contentType), 'article_type_fields'); ?>')) { location.href='/<?php p($ADMIN); ?>/article_types/fields/set_is_content.php?f_article_type=<?php print urlencode($articleTypeName); ?>&f_field_name=<?php  print urlencode($field->getPrintName()); ?>&f_is_content=<?php print $setContentField; ?>&<?php echo SecurityToken::URLParameter(); ?>' } else { document.getElementById('set_is_content_<?php echo $i; ?>').checked = <?php echo $isContentField; ?> }">
        <?php } else { ?>
        <?php echo $translator->trans('N/A'); ?>
        <?php } ?>
    </TD>

<TD>
<?php
    if ($field->getType() != ArticleTypeField::TYPE_COMPLEX_DATE) {
        echo $translator->trans('N/A');
    }
    else {
        $cur_color = $field->getColor();
        $color_div = '';

        $color_div .= '<div id="color_sel_' . $row_rank . '" class="color_sel_hidden color_sel_visible">';
        foreach ($color_list as $one_color) {
            $color_div .= '<div class="color_one_list" style="background:' . $one_color . ';" onClick="$(\'#color_val_' . $row_rank . '\').css(\'backgroundColor\', \'' . $one_color . '\'); $(\'#color_sel_' . $row_rank . '\').addClass(\'color_sel_hidden\'); window.save_field_color(\'' . $articleTypeName . '\', \'' . $field->getPrintName() . '\', \'' . $one_color . '\'); return false;";></div>';
        }
        $color_div .= '</div>';
        $color_div .= '<div class="color_one_current" id="color_val_' . $row_rank . '" style="background-color:' . $cur_color . ';" href="#" onClick="$(\'#color_sel_' . $row_rank . '\').toggleClass(\'color_sel_hidden\')"; return false;"></div>';
        echo $color_div;
    }
?>
</TD>

	<TD ALIGN="CENTER">
		<A HREF="/<?php p($ADMIN); ?>/article_types/fields/do_hide.php?f_article_type=<?php print urlencode($articleTypeName); ?>&f_field_name=<?php  print urlencode($field->getPrintName()); ?>&f_status=<?php print $hideShowStatus; ?>&<?php echo SecurityToken::URLParameter(); ?>" onclick="return confirm('<?php echo $translator->trans('Are you sure you want to $1 the article type field $2?', array('$1' => $hideShowText, '$2' => $field->getPrintName()), 'article_type_fields'); ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/<?php echo $hideShowImage; ?>" BORDER="0" ALT="<?php echo $translator->trans('$1 article type field $2', array('$1' => ucfirst($hideShowText), '$2' => $field->getPrintName()), 'article_type_fields'); ?>" TITLE="<?php  echo $translator->trans('$1 article type $2', array('$1' => ucfirst($hideShowText), '$2' => $field->getPrintName()), 'article_type_fields');?>" ></A>
	</TD>

	<?php  if ($g_user->hasPermission("ManageArticleTypes")) { ?>
	<TD ALIGN="CENTER">
		<A HREF="/<?php p($ADMIN); ?>/article_types/fields/do_del.php?f_article_type=<?php print urlencode($articleTypeName); ?>&f_field_name=<?php print urlencode($field->getPrintName()); ?>&<?php echo SecurityToken::URLParameter(); ?>" onclick="return confirm('<?php echo $translator->trans('Are you sure you want to delete the field $1?', array('$1' => htmlspecialchars($field->getPrintName().' '.$translator->trans('You will also delete all fields with this name from all articles of this type from all publications.'))), 'article_type_fields');  ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="<?php echo $translator->trans('Delete field $1', array('$1' => htmlspecialchars($field->getPrintName())), 'article_type_fields'); ?>" TITLE="<?php  $translator->trans('Delete field $1', array('$1' => htmlspecialchars($field->getPrintName())), 'article_type_fields'); ?>" ></A>
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
		    			<td><?php echo $translator->trans("Add translation:", array(), 'article_type_fields'); ?></td>
		    			<td>
							<SELECT NAME="f_field_language_id" class="input_select" alt="select" emsg="<?php echo $translator->trans("You must select a language."); ?>">
							<option value="0"><?php echo $translator->trans("---Select language---"); ?></option>
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
		    			<td><input type="text" name="f_field_translation_name" value="" class="input_text" size="15" alt="blank" emsg="<?php echo $translator->trans('You must enter a name for the field.', array(), 'article_type_fields'); ?>"></td>
		    			<td><input type="submit" name="f_submit" value="<?php echo $translator->trans("Translate"); ?>" class="button"></td>
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
	echo "<div class=\"indent\"><p class=\"failure_message\">** " . $translator->trans('The field name was already in use as a base property of the article. The field content will not be displayed in the templates.', array(), 'article_type_fields') . "</p></div>";
}
?>
<?php camp_html_copyright_notice(); ?>
