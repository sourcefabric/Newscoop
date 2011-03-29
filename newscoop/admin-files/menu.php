<?php
camp_load_translation_strings('home');

global $ADMIN, $g_user;

// Page title
$siteTitle = (!empty($Campsite['site']['title'])) ? htmlspecialchars($Campsite['site']['title']) : putGS("Newscoop") . $Campsite['VERSION'];

// locale setting for datepicker
$locale = !empty($_COOKIE['TOL_Language']) ? $_COOKIE['TOL_Language'] : 'en';
?>
<!DOCTYPE html>
<html lang="<?php echo $locale; ?>" dir="ltr">
<head>
    <meta charset="utf-8" />
    <title><?php p($siteTitle); ?></title>

    <meta http-equiv="Expires" content="now" />

    <?php include dirname(__FILE__) . '/html_head.php'; ?>
    
    <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/fg.menu.js" type="text/javascript"></script>
    <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery.widgets.js" type="text/javascript"></script>
    <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/fancybox/jquery.fancybox-1.3.4.pack.js" type="text/javascript"></script>

    <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/fg.menu.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/widgets.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/fancybox/jquery.fancybox-1.3.4.css" />

    <!--[if IE 6]>
	<style type="text/css">
		.fg-menu-ipod .fg-menu li { width: 95%; }
		.fg-menu-ipod .ui-widget-content { border:0; }
	</style>
	<![endif]-->

    <script type="text/javascript"><!--
        <?php if (strpos($_SERVER['HTTP_REFERER'], 'login.php') !== FALSE) { ?>
        if (opener && !opener.closed && opener.setSecurityToken) {
            opener.setSecurityToken(g_security_token);
            opener.focus();
            window.close();
        }
        <?php } ?>
        
        var user_msgs = '';
        <?php if (!empty($_SESSION['camp_user_msgs'])) { ?>
        user_msgs = "<?php echo str_replace('"', "'", $_SESSION['camp_user_msgs'][0]['msg']); ?>";
        <?php
            $_SESSION['camp_user_msgs'] = array();
        } ?>
    //--></script>
</head>
<body>
<div class="meta-bar">
    <ul>
        <li><a href="/<?php p($ADMIN); ?>/auth/logout"><?php putGS('Logout'); ?></a></li>
        <li><a href="<?php p($Campsite['site']['help_url']); ?>" target="_blank"><?php putGS('Help'); ?></a></li>
        <li><?php putGS("Signed in: $1", '<strong>' . $g_user->getRealName() . '</strong>'); ?></li>
    </ul>
</div>

<?php include_once APPLICATION_PATH . '/layouts/scripts/admin_menu.phtml' ?>
