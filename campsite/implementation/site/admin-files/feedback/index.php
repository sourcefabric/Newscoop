<?php
require_once($GLOBALS['g_campsiteDir'].'/classes/Language.php');
camp_load_translation_strings("bug_reporting");
camp_load_translation_strings("feedback");

global $Campsite;
global $ADMIN_DIR;
global $ADMIN;
?>
<br>
<br>
<form name="dialog" method="POST" action="<?php echo "/$ADMIN/feedback/do_feedback.php"; ?>">
<input type="hidden" value="1" NAME="f_isFromInterface">

<table border="0" cellspacing="0" cellpadding="6" align="left" class="table_input"  width="500px">
<tr>
    <td align="left">
        <table border="0" cellspacing="0" cellpadding="3" align="left">
        <tr>
            <td colspan="2"><b><font color="red"><?php putGS("Feedback") ?></font></b>
                <hr noshade size="1" color="black">
            </td>
        </tr>
        <tr valign="top">
            <td colspan="2" style="padding-bottom: 15px;"><?php
                putGS("Please feel free to tell us about a problem you are having, make a suggestion, or offer any other feedback.")
            ?></td>
        </tr>
        <tr>
        	<td align="left" nowrap><?php putGS("Subject"); ?>:</td>
            <td><input type="text" class="input_text" name="f_description" value="" size="32" maxlength="255"></td>
        </tr>
        <tr>
            <td align="left" valign="top" nowrap><?php putGS("Comment"); ?>:</td>
            <td>
                <textarea cols="50" rows="6" class="input_text" name="f_body"></textarea>
            </td>
        </tr>
        <tr>
            <td align="left" nowrap>
             	<?php putGS("Email:"); ?> <?php putGS("(optional)"); ?>
            </td>
            <td><input type="text" class="input_text" name="f_email" value="" size="32" maxlength="255">
            </td>
        </tr>
        </table>
    </td>
</tr>
<tr>
    <td>
        <table border="0" cellspacing="0" cellpadding="6" align="center"
        width="100%">
        <tr>
            <td colspan="2" align="">
                <div align="center"><input type="submit"
                class="button" value="<?php putGS("Send"); ?>"></div>
            </td>
        </tr>
        </table>
    </td>
</tr>
</table>
</form>
