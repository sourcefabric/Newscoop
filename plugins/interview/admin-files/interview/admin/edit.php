<?php
camp_load_translation_strings("plugin_interview");
camp_load_translation_strings('home');

// locale setting for datepicker
$locale = !empty($_COOKIE['TOL_Language']) ? $_COOKIE['TOL_Language'] : 'en';
?>
<html>
<head>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
	<title><?php putGS("Edit Interview"); ?></title>
	<?php include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php"); ?>

  <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/jquery-ui-1.8.6.custom.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet_new.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet.css" />
  <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
  <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/jquery/jquery-ui-1.8.6.custom.min.js" type="text/javascript"></script>
  <?php if ($locale != 'en') { ?>
  <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/jquery/i18n/jquery.ui.datepicker-<?php echo $locale; ?>.js" type="text/javascript"></script>
  <?php } ?>
  <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/jquery/jquery-ui-timepicker-addon.min.js" type="text/javascript"></script>
  <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/admin.js" type="text/javascript"></script>
    <script type="text/javascript">
    var g_admin_img = '<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>';
    function activate_fields(type)
    {
        var value;
        
        if (type == 'guest') {
            if (document.getElementsByName('f_guest_user_id')[0].value == '__new__') {
                value = false;
            } else {
                value = true;
            }
            document.getElementsByName('f_guest_new_user_login')[0].disabled = value;
            document.getElementsByName('f_guest_new_user_email')[0].disabled = value;
        }
        
        if (type == 'moderator') {
            if (document.getElementsByName('f_moderator_user_id')[0].value == '__new__') {
                value = false;
            } else {
                value = true;
            }
            document.getElementsByName('f_moderator_new_user_login')[0].disabled = value;
            document.getElementsByName('f_moderator_new_user_email')[0].disabled = value;
        } 
    }    
    </script>  
</head>
<body>
<?php

// Check permissions
if (!$g_user->hasPermission('plugin_interview_admin')) {
    camp_html_display_error(getGS('You do not have the right to manage interviews.'));
    exit;
}

$f_interview_id = Input::Get('f_interview_id', 'int', 0, true);

if (!Input::IsValid()) {
    camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
    exit;
}

$Interview = new Interview($f_interview_id);

// new usernames may exist
foreach(array('guest') as $type) {
    if ($_REQUEST['f_'.$type.'_user_id'] == '__new__') {
        require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/users/users_common.php");
    
        if (User::UserNameExists($_REQUEST['f_'.$type.'_new_user_login']) || Phorum_user::UserNameExists($_REQUEST['f_'.$type.'_new_user_login'])) {
            $errorMsg = getGS('User name $1 already exists, please choose a different login name.', $_REQUEST['f_'.$type.'_new_user_login']);
            camp_html_add_msg($errorMsg);
            $error = true;
        }
    }    
};

if (!$error && $Interview->store()) {
    ?>
    <script language="javascript">
        window.opener.location.reload();
        window.close();
    </script>
    <?php
    exit();
}

?>
<?php camp_html_display_msgs(); ?>
<table style="margin-top: 10px; margin-left: 15px; margin-right: 15px;" cellpadding="0" cellspacing="0" width="95%" class="table_input">
    <TR>
    	<TD style="padding: 3px";>
    		<B><?php $Interview->exists() ? putGS('Edit Interview') : putGS('Add new Interview'); ?></B>
    		<hr style="color: #8baed1";>
    	</TD>
    </TR>
    <tr>
        <td>
            <?php p($Interview->getForm('edit.php', array(), true)); ?>
        </td>
    </tr>
</table>
</body>
</html>
