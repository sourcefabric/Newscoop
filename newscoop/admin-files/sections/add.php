<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/sections/section_common.php");
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/articles/editor_load_tinymce.php");

$translator = \Zend_Registry::get('container')->getService('translator');

if (!$g_user->hasPermission('ManageSection')) {
    camp_html_display_error($translator->trans('You do not have the right to add sections.', array(), 'sections'));
    exit;
}

$f_publication_id = Input::Get('Pub', 'int', 0);
$f_issue_number = Input::Get('Issue', 'int', 0);
$f_language_id = Input::Get('Language', 'int', 0);

if (!Input::IsValid()) {
    camp_html_display_error($translator->trans('Invalid input: $1', array('$1' => Input::GetErrorString())), $_SERVER['REQUEST_URI']);
    exit;
}
$publicationObj = new Publication($f_publication_id);
$issueObj = new Issue($f_publication_id, $f_language_id, $f_issue_number);
$newSectionNumber = Section::GetUnusedSectionNumber($f_publication_id, $f_issue_number);

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj);
camp_html_content_top($translator->trans('Add new section', array(), 'sections'), $topArray, true, true, array($translator->trans("Sections") => "/$ADMIN/sections/?Pub=$f_publication_id&Issue=$f_issue_number&Language=$f_language_id"));

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
    <b><?php echo $translator->trans('Add new section', array(), 'sections'); ?></b>
    <hr noshade size="1" color="black" />
  </td>
</tr>
<tr>
  <td align="right" ><?php echo $translator->trans('Name'); ?>:</td>
  <td>
    <input type="text" class="input_text" name="f_name" size="32" alt="blank" emsg="<?php echo $translator->trans('You must fill in the $1 field.', array('$1' => "'".$translator->trans('Name')."'")); ?>">
  </td>
</tr>
<tr>
  <td align="right" valign="top"><?php echo $translator->trans('Description'); ?>:</td>
	<td>
	  <textarea name="f_description" ID="f_description" class="tinymce" rows="20" cols="80"></textarea>
	</td>
</tr>
<tr>
  <td align="right" ><?php echo $translator->trans('Number'); ?>:</td>
  <td><input
    type="text" class="input_text" name="f_number" value="<?php p($newSectionNumber); ?>" size="5" alt="number|0" emsg="<?php echo $translator->trans('You must fill in the $1 field.', array('$1' => "'".$translator->trans('Number')."'")); ?>">
  </td>
</tr>
<tr>
  <td align="right" ><?php echo $translator->trans('URL Name', array(), 'sections'); ?>:</td>
  <td>
    <input type="text" class="input_text" name="f_url_name" SIZE="32" value="<?php p($newSectionNumber); ?>" alt="alnum|1|A|true|false|_" emsg="<?php echo $translator->trans('The $1 field may only contain letters, digits and underscore (_) character.', array('$1' => "'" . $translator->trans('URL Name', array(), 'sections') . "'")); ?>">
  </td>
</tr>
<?php
	if(SaaS::singleton()->hasPermission('ManageSectionSubscriptions')) {
?>
<tr>
  <td align="right"><?php echo $translator->trans("Subscriptions"); ?>:</td>
  <td>
    <input type="checkbox" name="f_add_subscriptions" class="input_checkbox"> <?php  echo $translator->trans("Add section to all subscriptions.", array(), 'sections'); ?>
  </td>
</tr>
<?php
	}
?>
<tr>
  <td colspan="2">
    <div align="center">
      <input type="hidden" name="f_publication_id" value="<?php  p($f_publication_id); ?>">
      <input type="hidden" name="f_issue_number" value="<?php  p($f_issue_number); ?>">
      <input type="hidden" name="f_language_id" value="<?php  p($f_language_id); ?>">
      <input type="hidden" name="f_language_selected" ID="f_language_selected" value="<?php p($editorLanguage); ?>">
      <input type="submit" class="button" name="Save" value="<?php echo $translator->trans('Save'); ?>">
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
