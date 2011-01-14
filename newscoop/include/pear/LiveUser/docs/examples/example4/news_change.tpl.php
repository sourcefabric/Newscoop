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
                                        <td colspan="2" style="100px;">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" align="center">
                                            <table width="108" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td rowspan="2" valign="top">&nbsp;</td>
                                                    <td><span style="font-size: 10px;">{user}</span></td>
                                                </tr>
                                                <tr valign="bottom">
                                                    <td colspan="2" height="40"><span style="font-size: 10px;">Last login:<br />{lastLogin}</span></td>
                                                </tr>
                                                <tr valign="bottom">
                                                    <td colspan="2" height="40"><span style="font-size: 10xp;"><a href="news_change.php?logout=1">Logout</a></span></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td valign="top" width="500">
                                <h1 class="center">change / delete news</h1>
                                <script src="functions.js" type="text/javascript" language="javascript"></script>
                                {script_msg}
                                <table width="90%" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td colspan="4">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td><b>Date</b></td>
                                        <td><b>Message</b></td>
                                        <td><b>Author</b></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">&nbsp;</td>
                                    </tr>
                                    <!-- BEGIN row -->
                                    <tr onMouseOver="setPointer(this, '{color_h}')" onMouseOut="setPointer(this, '{color_n}')">
                                        <td valign="top" style="background-color: {color_n};">{time}</td>
                                        <td valign="top" style="background-color: {color_n};">{news}</td>
                                        <td valign="top" style="background-color: {color_n};">{author}</td>
                                        <td valign="top" style="background-color: {color_n};">
                                            <!-- BEGIN change -->
                                            <a href="{link_change}">Change</a>
                                            <!-- END change -->
                                            &nbsp;&nbsp;
                                            <!-- BEGIN delete -->
                                            <a href="{link_delete}">Delete</a>
                                             <!-- END delete -->
                                        </td>
                                    </tr>
                                    <!-- END row -->
                                    <tr>
                                        <td colspan="4">&nbsp;</td>
                                    </tr>
                                </table>
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