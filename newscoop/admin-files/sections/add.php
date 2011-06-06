<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/sections/section_common.php");
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/articles/editor_load_tinymce.php");

if (!$g_user->hasPermission('ManageSection')) {
    camp_html_display_error(getGS('You do not have the right to add sections.'));
    exit;
}

$f_publication_id = Input::Get('Pub', 'int', 0);
$f_issue_number = Input::Get('Issue', 'int', 0);
$f_language_id = Input::Get('Language', 'int', 0);

if (!Input::IsValid()) {
    camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
    exit;
}
$publicationObj = new Publication($f_publication_id);
$issueObj = new Issue($f_publication_id, $f_language_id, $f_issue_number);
$newSectionNumber = Section::GetUnusedSectionNumber($f_publication_id, $f_issue_number);

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj);
camp_html_content_top(getGS('Add new section'), $topArray, true, true, array(getGS("Sections") => "/$ADMIN/sections/?Pub=$f_publication_id&Issue=$f_issue_number&Language=$f_language_id"));

$languageObj = new Language($f_language_id);
if (!is_object($languageObj)) {
    $languageObj = new Language(1);
}
$editorLanguage = camp_session_get('TOL_Language', $languageObj->getCode());
editor_load_tinymce('f_description', $g_user, 0, $editorLanguage, 'section');
?>
<p>
<form name="section_add" method="POST" action="/<?php echo $ADMIN; ?>/sections/do_add.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<table border="0" cellspacing="0" cellpadding="0" class="box_table">
<tr>
  <td colspan="2">
    <b><?php putGS('Add new section'); ?></b>
    <hr noshade size="1" color="black" />
  </td>
</tr>
<tr>
  <td align="right" ><?php putGS('Name'); ?>:</td>
  <td>
    <input type="text" class="input_text" name="f_name" size="32" alt="blank" emsg="<?php putGS('You must fill in the $1 field.', "'".getGS('Name')."'"); ?>">
  </td>
</tr>
<tr>
  <td align="right" valign="top"><?php putGS('Description'); ?>:</td>
	<td>
	  <textarea name="f_description" ID="f_description" class="tinymce" rows="20" cols="80"></textarea>
	</td>
</tr>
<tr>
  <td align="right" ><?php putGS('Number'); ?>:</td>
  <td><input
    type="text" class="input_text" name="f_number" value="<?php p($newSectionNumber); ?>" size="5" alt="number|0" emsg="<?php putGS('You must fill in the $1 field.',"'".getGS('Number')."'"); ?>">
  </td>
</tr>
<tr>
  <td align="right" ><?php putGS('URL Name'); ?>:</td>
  <td>
    <input type="text" class="input_text" name="f_url_name" SIZE="32" value="<?php p($newSectionNumber); ?>" alt="blank" emsg="<?php putGS('You must fill in the $1 field.',"'".getGS('URL Name')."'"); ?>">
  </td>
</tr>
<tr>
  <td align="right"><?php putGS("Subscriptions"); ?>:</td>
  <td>
    <input type="checkbox" name="f_add_subscriptions" class="input_checkbox"> <?php  putGS("Add section to all subscriptions."); ?>
  </td>
</tr>
<tr>
  <td colspan="2">
    <div align="center">
      <input type="hidden" name="f_publication_id" value="<?php  p($f_publication_id); ?>">
      <input type="hidden" name="f_issue_number" value="<?php  p($f_issue_number); ?>">
      <input type="hidden" name="f_language_id" value="<?php  p($f_language_id); ?>">
      <input type="hidden" name="f_language_selected" ID="f_language_selected" value="<?php p($editorLanguage); ?>">
      <input type="submit" class="button" name="Save" value="<?php putGS('Save'); ?>">
    </div>
  </td>
</tr>
</table>
</form>
<p>
<script type="text/javascript">
document.section_add.f_name.focus();
</script>
<?php camp_html_copyright_notice(); ?>
