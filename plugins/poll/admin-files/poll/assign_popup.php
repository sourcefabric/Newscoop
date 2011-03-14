<?php
camp_load_translation_strings("plugin_poll");
camp_load_translation_strings('home');

if (!$g_user->hasPermission("plugin_poll")) {
	camp_html_display_error(getGS("You do not have the right to manage poll."));
	exit;
}

$f_include = Input::Get('f_include', 'string', false);
$f_poll_item = Input::Get('f_poll_item', 'string');
$f_language_id = Input::Get('f_language_id', 'int');
$f_publication_id = Input::Get('f_publication_id', 'int');
$f_issue_nr = Input::Get('f_issue_nr', 'int');
$f_section_nr = Input::Get('f_section_nr', 'int');
$f_article_nr = Input::Get('f_article_nr', 'int');

$GLOBALS['_popup'] = TRUE; // set popup flag for template

// locale setting for datepicker
$locale = !empty($_COOKIE['TOL_Language']) ? $_COOKIE['TOL_Language'] : 'en';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
	<title><?php putGS("Attach poll"); ?></title>

	<meta http-equiv="Expires" content="now" />

    <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/jquery-ui-1.8.6.custom.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet_new.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet.css" />
	<?php include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php"); ?>

    <script type="text/javascript"><!--
        var g_admin_img = '<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>';
    //--></script>

    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery-1.4.2.min.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery-ui-1.8.6.custom.min.js"></script>
    <?php if ($locale != 'en') { ?>
    <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/i18n/jquery.ui.datepicker-<?php echo $locale; ?>.js" type="text/javascript"></script>
    <?php } ?>
    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/admin.js"></script>
</head>
<body>

<?php
switch ($f_include) {
    case 'edit.php':
        include 'edit.php';
    break;
    
    default:
        include 'assign.php';
    break;
}
?>

</body>
</html>
