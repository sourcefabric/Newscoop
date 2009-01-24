<?php

require_once($_SERVER['DOCUMENT_ROOT']."/db_connect.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/lib_campsite.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Input.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/ArticleTypeField.php");

if (!$g_user->hasPermission('ChangeArticle')
    || !$g_user->hasPermission('ManageIssue')) {
    camp_html_display_error('You must have the permissions to add/change '
    . 'issues and articles in order to set the author for all articles.');
    exit;
}

$f_src_author_field = Input::Get('f_src_author_field', 'string', '');
if (!empty($f_src_author_field)) {
	$fields = ArticleTypeField::FetchFields($f_src_author_field, null, 'text');
	if (count($fields) == 0) {
	    camp_html_add_msg("Invalid or empty field $1. You must select a valid dynamic field.", $f_src_author_field);
	}

	foreach ($fields as $field) {
		camp_set_author($field, $errors);
		if (count($errors) == 0) {
			camp_html_add_msg(getGS("The author was set successfuly for articles of type '$1' from the field '$2'.",
			                        $field->getArticleType(), $field->getPrintName()), 'ok');
		} else {
            camp_html_add_msg(getGS("There were errors setting the author for articles of type '$1' from the field '$2'.",
                                    $field->getArticleType(), $field->getPrintName()));
		}
        foreach ($errors as $error) {
            camp_html_add_msg($error);
        }
	}
    camp_html_display_msgs();
}

$availableFields = ArticleTypeField::FetchFields(null, null, 'text');

?>

<br/>
<form name="f_set_author" method="post">
<TABLE BORDER="0" CELLSPACING="4" CELLPADDING="2" CLASS="table_input">
<TR>
    <TD VALIGN="TOP" align="left" nowrap>
        <?php putGS("Select field from which to generate the author"); ?>:
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
        <input type="submit" name="f_submit" value="<?php putGS("Submit"); ?>">
    </td>
</tr>
</table>
</form>

<?php camp_html_copyright_notice(false); ?>
