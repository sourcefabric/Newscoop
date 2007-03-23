<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Example 4</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <meta http-equiv="Content-Language" content="en" />
    <link rel="stylesheet" href="style.css" type="text/css" media="all" />
</head>
<body>
    <div class="center">
        <table id="table3" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <table id="table4" cellspacing="0" cellpadding="0">
                        <tr>
                            <td valign="top" width="130">
                                <table width="108" border="0" cellspacing="0" cellpadding="0" name="navigation">
                                    <tr>
                                        <td colspan="2" height="40">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td width="108" nowrap="nowarp"><a href="news_new.php">Write news </a></td>
                                    </tr>
                                    <tr>
                                        <td width="108" nowrap="nowarp"><a href="news_change.php">Change / delete news</a></td>
                                    </tr>
                                    <tr>
                                        <td width="108" nowrap="nowarp"><a href="news_view.php">View news</a></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="height: 100px;">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" align="center">
                                            <table width="108" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td rowspan="2" valign="top">&nbsp;</td>
                                                    <td><font style="font-size: 10px;">{user}</span></td>
                                                </tr>
                                                <tr valign="bottom">
                                                    <td colspan="2" height="40"><span style="font-size: 10px;">Last login:<br />{lastLogin}</span></td>
                                                </tr>
                                                <tr valign="bottom">
                                                    <td colspan="2" height="40"><span style="font-size: 10px;"><a href="news_new.php?logout=1">Logout</a></span></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td valign="top" width="500">
                                <h1 class="center">write news</h1>
                                {script_msg}
                                <form action="{form_action}" method="POST">
                                    <table style="width: 450; border: 0px;" cellpadding="0" cellspacing="5">
                                        <tr>
                                            <td><b>Message:</b></td>
                                            <td>
                                                <textarea name="news" cols="40" rows="6">{message}</textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Valid:</b></td>
                                            <td>
                                                <input type="text" name="valid_to" maxlength="2" value="{valid}" size="4" /> &nbsp;Weeks</td>
                                        </tr>
                                        <!-- BEGIN group -->
                                        <tr>
                                            <td><b>For Group:</b></td>
                                            <td>
                                                <select name="group_id" size="1">
                                                    <!-- BEGIN choose_group -->
                                                    <option value="{value}" {selected}>{label}</option>
                                                    <!-- END choose_group -->
                                                </select>
                                            </td>
                                        </tr>
                                        <!-- END group -->
                                        <tr>
                                            <td colspan="2">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>
                                                <input type="submit" name="Button" value="Send" />
                                                <!-- BEGIN button_abort -->
                                                <input type="submit" name="Button2" value="Abort" />
                                                <!-- END button_abort -->
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- BEGIN set_group -->
                                    <input type="hidden" name="group_id" value="{group_id}" />
                                    <!-- END set_group -->
                                    <!-- BEGIN set_news -->
                                    <input type="hidden" name="news_id" value="{news_id}" />
                                    <!-- END set_news -->
                                    <!-- BEGIN action -->
                                    <input type="hidden" name="action" value="change" />
                                    <!-- END action -->
                                </form>
                                <p>&nbsp;</p>
                            </td>
                        </tr>
                        <tr class="center">
                            <td colspan="2"><a href="mailto:krausbn@php.net">&copy; 2003 by Björn Kraus</a></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>