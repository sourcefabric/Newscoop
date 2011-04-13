<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title>LiveUser Login Screen</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <style type="text/css">
    <!--
    table {
        background-color: #CCCCCC;
        border-color: 1px solid #000;
    }
    body {
        font-family: Verdana, Arial, Helvetica, sans-serif;
        font-size: 12px;
        color: #000000;
        background-color: #FFFFFF
    }

    .center {
        text-align: center;
    }
    .center table {
        margin: auto;
    }
    -->
    </style>
</head>

<body>
    <form name="loginform" method="post" action="<?php echo !isset($target) ? 'example.php' : $target; ?>">
    <div class="center">
        <table width="300" border="0" cellspacing="0" cellpadding="5">
            <tr>
                <td colspan="2"><b>Example login</b></td>
            </tr>
            <tr>
                <td>Handle:</td>
                <td>
                    <input type="text" name="handle" maxlength="80" value="" />
                </td>
            </tr>
             <tr>
                <td>Password:</td>
                <td>
                    <input type="password" name="passwd" maxlength="80" value="" />
                </td>
            </tr>
            <tr>
                <td>Remember me:</td>
                <td>
                    <input type="checkbox" name="rememberMe" />
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="center">
                        <input type="submit" name="x" value="Login" />
                        <input type="hidden" name="Submit" value="1" />
                    </div>
                </td>
            </tr>
        </table>
    </div>
    </form>
</body>
</html>