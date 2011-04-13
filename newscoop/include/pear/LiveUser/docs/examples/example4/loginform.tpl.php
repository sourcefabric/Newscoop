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
        <h1><b><i>BackOffice</i></b></h1>
        <p>&nbsp;</p>
        <p>Greetings! Please insert your username and password:</p>
        <!-- BEGIN failure -->
        <p style="notice">Username or password wrong.<br />
        Please try it again!</p>
        <!-- END failure -->
        <!-- BEGIN expired -->
        <p class="notice"><b>You are expired.</p>
        <!-- END expired -->
        <!-- BEGIN idled -->
        <p class="notice">>You are idled.</p>
        <!-- END idled -->
        <!-- BEGIN inactive -->
        <p class="notice">Your account has been deactivated.<br />
        Please contact the <a href="mailto:krausbn@php.net">webmaster</a>!</p>
        <!-- END inactive -->
        <form name="loginform" action="{form_action}" method="post">
            <table class="table1" cellspacing="0" cellpadding="0">
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td valign="top">
                        <table class="table2" cellpadding="3" cellspacing="0">
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="2">Login
                                    <hr style="width:260px; text-align:center;" />
                                </td>
                            </tr>
                            <tr>
                                <td align="right">Username:</td>
                                <td>
                                    <input type="text" name="username" maxlength="32" size="20" />
                                </td>
                            </tr>
                            <tr>
                                <td align="right">Password:</td>
                                <td>
                                    <input type="password" name="password" maxlength="32" size="20" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <hr style="width: 260px; text-align: center;" />
                                       <input type="submit" name="x" value="Login" />
                                       <input type="hidden" name="Submit" value="1" />
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
    </form>
    </div>

    <script type="text/javascript">
    <!--
      if (document.forms[0][0].value != '') {
          document.forms[0][1].focus();
      } else {
          document.forms[0][0].focus();
      }
    // -->
    </script>
</body>
</html>