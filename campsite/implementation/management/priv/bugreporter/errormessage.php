<?php 
require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');
global $g_documentRoot, $Campsite, $ADMIN_DIR, $ADMIN;
require_once ($g_documentRoot.'/classes/Language.php'); 
?>
<br>

<br>
<table border="0">
<tr>
    <td rowspan="2" valign="top">
        <script type="text/javascript" src=
        "/javascript/campsite.js">
        </script>

        <form name="dialog" method="POST" action=<?php 
            echo ("\"/$ADMIN/bugreporter/"); 
        ?>senderrorform.php" />

        <!-- <form name="dialog" method="post" action=
        "http://localhost/trac/autotracj/newreport"> -->
        <!-- <form name="dialog" method="POST"
        action="http://test.n-space.org/mocktrac/echo.php"
        onsubmit="return validateForm(this, 0, 1, 0, 1, 8);"> -->
        <input type="hidden" name="isPostFromBugreporter" value="1">
        <input type="hidden" name="id" value=<?php 
            echo ( "'" . $reporter->getId() . "'"); 
        ?> >
        <input type=
        "hidden" name="software" value=<?php
            echo ("'" . urlencode ($reporter->getSoftware ()) . "'");
        ?> > <input type="hidden"
        name="str" value=<?php
            echo ("'" . urlencode ($reporter->getStr ()) . "'");
        ?> >
        <input type="hidden" name="num" value=<?php
            echo ("'" . urlencode ($reporter->getErrorNum ()) . "'");
        ?> > <input type="hidden" name=
        "time" value=<?php
            echo ("'" . urlencode ($reporter->getTime ()) . "'");
        ?> >
        <input type="hidden" name="file" value=<?php
            echo ("'" . urlencode ($reporter->getFile ()) . "'");
        ?> >
        <input type="hidden" name="line" value=<?php
            echo ("'" . urlencode ($reporter->getLine ()) . "'");
        ?> >
        <input type="hidden" name="backtrace" value=<?php
            echo ("'" . urlencode ($reporter->getBacktraceString ()) . "'");
        ?> >
        <table border="0" cellspacing="0" align="left" class="table_input"
        width="600px">
        <tr>
            <td align="left">
                <table border="0" cellspacing="0" cellpadding="3" align="left">
                <tr>
                    <td colspan="2"><b><font color="red"><?php putGS("Error Report") ?></font></b>
                        <hr noshade size="1" color="black">
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p><?php
                            putGS ("Campsite has encountered a problem. ");
                            putGS ("We are sorry for the inconvenience. The Campsite development team will be investigating this issue.");
                            ?></p>
                        <p><?php
                           putGS ("In order to help them pinpoint the issue please take a moment to describe what happened.");
                           ?></p>
                           </td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;
                    </td>
                </tr>
                <tr valign="top">
                    <td colspan="2"><?php
                        putGS ("What were you trying to do when this happened?")
                    ?></td>
                </tr>
                <tr>
                    <td align="left" nowrap>&nbsp;</td>
                    <td>
                        <textarea cols="50" rows="4" class="input_text" name="description"></textarea>
                    </td>
                </tr>
                <tr>
                    <td align="left" nowrap><?php
                        putGS ("Email (optional)");
                    ?>
                    </td>
                    <td><input type="text" class="input_text" name="email" value=""
                        size="32" maxlength="255">
                    </td>
                </tr>
                </table>
            </td>
        </tr>
        <tr id="user_details_show_link">
            <td style="padding-left: 6px; padding-top: 6px;">
                <a href=
                "javascript:%20void(0);" onclick=
                "ToggleRowVisibility('user_details_dialog'); 
                ToggleRowVisibility('user_details_hide_link');
                ToggleRowVisibility('user_details_show_link');">
                <img src=<?php
                    echo ("'" . $Campsite["ADMIN_IMAGE_BASE_URL"] . "/viewmag+.png'");
                ?> id="my_icon" border=
                "0" align="center"> Show error details</a>
            </td>
        </tr>
        <tr id="user_details_hide_link" style="display: none;">
            <td style="padding-left: 6px; padding-top: 6px;"><a href=
                "javascript:%20void(0);" onclick=
                "ToggleRowVisibility('user_details_dialog'); 
                ToggleRowVisibility('user_details_hide_link');
                ToggleRowVisibility('user_details_show_link');">
                <img src=<?php 
                    echo ($Campsite["ADMIN_IMAGE_BASE_URL"] . "/viewmag-.png");
                ?> id="my_icon" border=
                "0" align="center"> Hide error details</a>
            </td>
        </tr>
        <tr id="user_details_dialog" style="display: none;">
            <td>
                <!-- <div style="overflow: auto; height: 150px; background-color: #EEEEEE;
                border: 1px solid black; padding-left: 3px;"> 
                -->
                <table border="0" cellspacing="0" cellpadding="3" align="center"
                width="100%">
                <tr>
                    <td align="left" nowrap><?php 
                        putGS ("Error ID:");
                    ?>
                    </td>
                    <td><?php
                            echo ($reporter->getId());
                        ?> 
                    </td>
                </tr>
                <tr>
                    <td align="left" nowrap><?php
                        putGS ("Software:");
                    ?>
                    </td>
                    <td><?php
                        echo ( $reporter->getSoftware () );
                        ?> 
                    </td>
                </tr>
                <tr>
                    <td align="left" nowrap><?php
                        putGS ("Error Message:");
                        ?>
                    </td>
                    <td><?php
                        echo ( $reporter->getStr () );
                        ?> 
                    </td>
                </tr>
                <tr>
                    <td align="left" nowrap><?php
                        putGS ("Error Number:");
                        ?>
                    </td>
                    <td><?php
                        echo ( $reporter->getErrorNum () );
                        ?>
                    </td>
                </tr>
                <tr>
                    <td align="left" nowrap><?php
                        putGS ("Time:");
                        ?>
                    </td>
                    <td><?php
                        echo ( $reporter->getTime () );
                        ?> 
                    </td>
                </tr>
                <tr valign="top">
                    <td align="left" nowrap><?php
                        putGS ("Backtrace:");
                        ?>
                    </td>
                    <td>
                        <div style="overflow: auto; height: 150px;
                        background-color: #EEEEEE; border: 1px solid
                        black; padding-left: 3px;"><pre><?php
                            echo ( $reporter->getBacktraceString() );
                        ?></pre>
                        &nbsp;
                    </td>
                </table>
                <!-- </div> -->
            </td>
    </tr>
    <tr>
            <td>
                <table border="0" cellspacing="0" cellpadding="6" align="center"
                width="100%">
                <tr>
                    <td colspan="2">
                        <div align="center"><input type="submit"
                        class="button" name="Send" value="Send"></div>
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
<?php
    require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/camp_html.php");
    camp_html_copyright_notice();
?>
