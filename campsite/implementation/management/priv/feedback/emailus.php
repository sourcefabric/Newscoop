<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
camp_load_translation_strings('bug_reporting');
global $ADMIN_DIR;
global $Campsite;

?>
<br />
<table class="table_input" align="left" valign="top" width="800px" style="padding: 5px;">
<tr>
    <td colspan="2">
<?php

    echo '<font size="+2"><b>';
    putGS("Campsite has encountered a problem.");
    echo "</b></font>";
    echo '<hr noshade size="1" color="black">';

?>
<p>
<?php
    putGS("Please take a minute to send us an email.");
    echo "<br><br>";
    putGS("Simply copy and paste the error report below and send it to:");
    echo (" <b>");
    echo $Campsite["SUPPORT_EMAIL"];
    echo ("</b>");
?>.
</p>
<p>
    <?php
    putGS("Thank you.");
    ?>
</p>
<br />
    </td>
</tr>

</table>