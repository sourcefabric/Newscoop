<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
camp_load_translation_strings("bug_reporting");

global $Campsite;
global $ADMIN_DIR;
global $ADMIN;
?>
<br>
<br>
<table border="0">
<tr>
    <td valign="top">
        <form name="dialog" method="POST" action="<?php echo "/$ADMIN/feedback/do_feedback.php"; ?>">
        <input type="hidden" value="1" NAME="f_isFromInterface">

        <table border="0" cellspacing="0" align="left" class="table_input"  width="600px">
        <tr>
            <td align="left">
                <table border="0" cellspacing="0" cellpadding="3" align="left">
                <tr>
                    <td colspan="2"><b><font color="red"><?php putGS("Feedback") ?></font></b>
                        <hr noshade size="1" color="black">
                    </td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;
                    </td>
                </tr>
                <tr valign="top">
                    <td colspan="2"><?php
                        putGS("What would you like to leave as feedback?")
                    ?></td>
                </tr>
                <tr>
                    <td align="left" nowrap>&nbsp;</td>
                    <td>
                        <textarea cols="50" rows="4" class="input_text" name="f_description"></textarea>
                    </td>
                </tr>
                <tr>
                    <td align="left" nowrap><?php
                        putGS("Email (optional)");
                    ?>
                    </td>
                    <td><input type="text" class="input_text" name="f_email" value="" size="32" maxlength="255">
                    </td>
                </tr>
                </table>
            </td>
        </tr>
        </td>
    <tr>
            <td>
                <table border="0" cellspacing="0" cellpadding="6" align="center"
                width="100%">
                <tr>
                    <td colspan="2">
                        <div align="center"><input type="submit"
                        class="button" value="<?php putGS("Send"); ?>"></div>
                    </td>
                </tr>
                </table>
            </td>
        </tr>
        </table>
        </form>
    </td>
</tr>
</table>
