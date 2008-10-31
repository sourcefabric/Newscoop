<?php
    require_once 'conf.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title>Navigation</title>
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
    <h3>Navigation</h3>
    <table border="0" cellpadding="5">
<?php
    // get the area_define_name and the area_name of each area in current language.
    $areas = $luadmin->perm->getAreas(array(
        'fields' => array('area_define_name', 'name'),
        'filters' => array('language_id' => $language_selected)
    ));
    // print navigation
    foreach ($areas as $row) {
?>
        <tr>
            <td><li></td>
            <td><a href="<?php echo strtolower($row['area_define_name']); ?>.php" target="main"><?php echo $row['name']; ?></a></td>
        </tr>
<?php
    }
?>
    </table>
    <p>&nbsp;</p>
    <form method="POST" action="example.php" target="_parent">
        <select name="language" size="1" onChange="submit()">
<?php
    $languages = array(
        'de' => array(
            'de' => 'Deutsch',
            'en' => 'Englisch'
        ),
        'en' => array(
            'de' => 'German',
            'en' => 'English'
        ),
    );
    // print language options
    foreach ($languages[$language_selected] as $code => $language) {
        $selected = $code == $language_selected ? ' selected="selected"' : '';
?>
            <option value="<?php echo $code;?>"<?php echo $selected; ?>><?php echo $language; ?></option>';
<?php
    }
?>
        </select>
    </form>
    <p>&nbsp;</p>
    <p>&nbsp; </p>
</body>
</html>
