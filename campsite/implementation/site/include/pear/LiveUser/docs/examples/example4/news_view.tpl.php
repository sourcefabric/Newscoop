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
                                                    <td><span style="font-size: 10px;">{user}</span></td>
                                                </tr>
                                                <tr valign="bottom">
                                                    <td colspan="2" height="40"><span style="font-size: 10px;">Last login:<br />{lastLogin}</span></td>
                                                </tr>
                                                <tr valign="bottom">
                                                    <td colspan="2" height="40"><span style="font-size: 10px;"><a href="news_view.php?logout=1">Logout</a></span></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td valign="top" width="500">
                                <h1 class="center">view news</h1>
                                <p>&nbsp;</p>
                                <!-- BEGIN row -->
                                <table width="450" border="1" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td><b>{time}</b></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p>{news}</p>
                                            <p><a href="mailto:{email}">{author}</a></p>
                                        </td>
                                    </tr>
                                </table>
                                <p>&nbsp;</p>
                                <!-- END row -->
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