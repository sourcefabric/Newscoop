<?php
    $language = '';

    session_start();

    if (array_key_exists('language', $_POST)) {
        $language = $_POST['language'];
    } elseif (array_key_exists('language', $_SESSION)) {
        $language = $_SESSION['language'];
    } else {
        $language = 'en';
    }

    $_SESSION['language'] = $language;
    session_write_close();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html>
<head>
    <title>Example 3</title>
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
    <frameset cols="200,*" rows="*">
        <frame name="nav"  frameborder="0" scrolling="no" noresize="noresize" src="nav.php?language=<?php echo $language; ?>" />
        <frame name="main"  frameborder="0" src="main.php" />
        <noframes>
        <body>Your browser does not
        handle frames!</body>
        </noframes>
    </frameset>
</html>