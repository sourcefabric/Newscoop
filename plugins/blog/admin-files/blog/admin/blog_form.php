<html>
<?php
camp_load_translation_strings("plugin_blog");

// User role depend on path to this file. Tricky: moderator folder is just symlink to admin files!
if (strpos($call_script, '/blog/admin/') !== false && $g_user->hasPermission('plugin_blog_admin')) {
    $is_admin = true;
}
if (strpos($call_script, '/blog/moderator/') !== false && $g_user->hasPermission('plugin_blog_moderator')) {
    $is_moderator = true;
}

// Check permissions
if (!$g_user->hasPermission('plugin_blog_admin')) {
    camp_html_display_error(getGS('You do not have the right to manage blogs.'));
    exit;
}

$f_blog_id = Input::Get('f_blog_id', 'int');
$Blog = new Blog($f_blog_id);

if ($Blog->store($is_admin)) {
    camp_html_add_msg(getGS('Blog saved.'), 'ok');
    ?>
    <script language="javascript">
        window.opener.location.reload();
        window.close();
    </script>
    <?php
    exit();
}

?>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="Expires" content="now">
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/admin-style/admin_stylesheet.css">
	<title><?php $Blog->exists() ? putGS('Edit Blog') : putGS('Add new Blog'); ?></title>
	<?php include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php"); ?>
	<style type="text/css">@import url(<?php echo $Campsite["WEBSITE_URL"]; ?>/js/jscalendar/calendar-system.css);</style>
    <script type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/js/jscalendar/calendar.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/js/jscalendar/lang/calendar-<?php echo camp_session_get('TOL_Language', 'en'); ?>.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/js/jscalendar/calendar-setup.js"></script>

    <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
</head>
<body>

<?php camp_html_display_msgs(); ?>

<table style="margin-top: 10px; margin-left: 15px; margin-right: 15px;" cellpadding="0" cellspacing="0" width="95%" class="table_input">
<tr>
  <td style="padding: 3px";>
    <b><?php $Blog->exists() ? putGS('Edit Blog') : putGS('Add new Blog'); ?></b>
    <hr style="color: #8baed1";>
  </td>
</tr>
<tr>
  <td>
    <?php p($Blog->getForm(basename(__FILE__), $is_admin)); ?>
  </td>
</tr>
</table>
</body>
</html>
