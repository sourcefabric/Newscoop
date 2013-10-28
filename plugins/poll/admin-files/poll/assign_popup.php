<?php
$translator = \Zend_Registry::get('container')->getService('translator');

if (!$g_user->hasPermission("plugin_poll")) {
	camp_html_display_error($translator->trans("You do not have the right to manage poll.", array(), 'plugin_poll'));
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
	<title><?php echo $translator->trans("Attach poll", array(), 'plugin_poll'); ?></title>

	<meta http-equiv="Expires" content="now" />

	<?php include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/html_head.php"); ?>
	<?php include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php"); ?>
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
