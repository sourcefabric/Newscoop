<?php

require_once($GLOBALS['g_campsiteDir']."/db_connect.php");
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/lib_campsite.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Input.php");
require_once($GLOBALS['g_campsiteDir']."/classes/ArticleTypeField.php");
$translator = \Zend_Registry::get('container')->getService('translator');

if (!$g_user->hasPermission('ChangeArticle')
    || !$g_user->hasPermission('ManageIssue')) {
    camp_html_display_error($translator->trans('You must have the permissions to add/change issues and articles in order to set the author for all articles.', array(), 'home'));
    exit;
}

$f_src_author_field = Input::Get('f_src_author_field', 'string', '');
if (!empty($f_src_author_field)) {
	$fields = ArticleTypeField::FetchFields($f_src_author_field, null, 'text', false, false, false, true, true);
	if (count($fields) == 0) {
	    camp_html_add_msg($translator->trans("Invalid or empty field $1. You must select a valid dynamic field.", array('$1' => $f_src_author_field), 'home'));
	}

	foreach ($fields as $field) {
		camp_set_author($field, $errors);
		if (count($errors) == 0) {
			camp_html_add_msg($translator->trans("The author was set successfuly for articles of type $1 from the field $2.",
			                        array('$1' => $field->getArticleType(), '$2' => $field->getPrintName()), 'home'), 'ok');
		} else {
            camp_html_add_msg($translator->trans("There were errors setting the author for articles of type $1 from the field $2.",
                                    array('$1' => $field->getArticleType(), '$2' => $field->getPrintName()), 'home'));
		}
        foreach ($errors as $error) {
            camp_html_add_msg($error);
        }
	}
    camp_html_display_msgs();
}

$availableFields = ArticleTypeField::FetchFields(null, null, 'text', false, false, false, true, true);

?>

<br/>
<form name="f_set_author" method="post">
<TABLE BORDER="0" CELLSPACING="4" CELLPADDING="2" CLASS="table_input">
<TR>
    <TD VALIGN="TOP" align="left" nowrap>
        <?php echo $translator->trans("Select the field from which to generate the author", array(), 'home'); ?>:
    </td>
    <td valign="top" align="left">
        <select name="f_src_author_field">
        <?php
        foreach ($availableFields as $field) {
        	echo "<option value=\"" . htmlspecialchars($field->getPrintName())
        	     . "\">" . $field->getPrintName() . "</option>\n";
        }
        ?>
        </select>
    </td>
</tr>
<tr>
    <td colspan="2" align="center">
        <input type="submit" name="f_submit" value="<?php echo $translator->trans("Submit"); ?>">
    </td>
</tr>
</table>
</form>

<?php camp_html_copyright_notice(false); ?>
