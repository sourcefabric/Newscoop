<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title>LiveUser Example 5</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <style type="text/css" media="screen">@import "layout_frontend.css";</style>
</head>

<body>
    <h1>Test for the LiveUser class</h1>
    <p>This example is intended to be used with the Auth/MDB2 and the Perm/MDB2_Complex driver.</p>
    <p>To set this up follow these steps:</p>
    <ol>
        <li>Copy the files in this directory into your web root.</li>
        <li>Configure your DSN and the PEAR path in the conf.php.</li>
        <li>Make sure you read README in the examples root folder.</li>
        <li>Importing demodata.xml to your test database using the demodata.php script</li>
    </ol>
    <p>If you want to test this example in your local path with out installing LiveUser in PEAR on
    your server then uncomment these two lines in <b>main.inc.php</b> and point $path_to_liveuser_dir to live user dir :)<br />
    <br />
    $path_to_liveuser_dir = './pear/'.PATH_SEPARATOR;<br />
    ini_set('include_path', $path_to_liveuser_dir.ini_get('include_path'));<br />
    </p>

    <div class="content">
        <h2><a href="home.php">Proceed to example 5</a></h2>
    </div>

</body>
</html>
