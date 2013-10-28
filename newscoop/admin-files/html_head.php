<?php

$translator = \Zend_Registry::get('container')->getService('translator');

// locale for datepicker
$locale = !empty($_COOKIE['TOL_Language']) ? $_COOKIE['TOL_Language'] : 'en';
if ($locale == 'cz') {
    $locale = 'cs';
}
?>

<link rel="shortcut icon" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/images/7773658c3ccbf03954b4dacb029b2229.ico" />
<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/jquery-ui-1.8.6.custom.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet.css" />



<?php if(!isset($f_context_box)) {?>
    <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet_new.css" />
<?php } else { ?>
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet_context.css" />
<?php } ?>


<?php if (isset($f_multidate_box)) {?> <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/multidate.css" /> <?php }?>

<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/content.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/ColVis.css" />

<script src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/jquery/jquery-1.7.1.min.js" type="text/javascript"></script>
<script src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/jquery/jquery-ui-1.8.6.custom.min.js" type="text/javascript"></script>

<?php if ($locale != 'en') { ?>
<script src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/jquery/i18n/jquery.ui.datepicker-<?php echo $locale; ?>.js" type="text/javascript"></script>
<?php } ?>
<script src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/jquery/jquery-ui-timepicker-addon.min.js" type="text/javascript"></script>

<script type="text/javascript"><!--
    var website_url = '<?php echo $Campsite['WEBSITE_URL'];?>';
    var g_admin_url = '/<?php echo $ADMIN; ?>';
    var g_security_token = '<?php echo SecurityToken::GetToken(); ?>';
    var g_admin_img = '<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>';
    var popupFlash = false;

    var localizer = localizer || {
        processing: '<?php echo $translator->trans('Processing...', array(), 'home'); ?>',
        session_expired: '<?php echo $translator->trans('Session expired.', array(), 'home'); ?>',
        please: '<?php echo $translator->trans('Please', array(), 'home'); ?>',
        login: '<?php echo $translator->trans('login', array(), 'home'); ?>',
        connection_interrupted : '<?php echo $translator->trans('Connection interrupted', array(), 'home') ?>',
        try_again_later : '<?php echo $translator->trans('try again later', array(), 'home') ?>'
    };

    $(function() {
        $.datepicker.setDefaults( $.datepicker.regional['<?php echo $locale; ?>'] );
    });
//--></script>

<script src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/jquery/jquery.cookie.js" type="text/javascript"></script>
<script src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/jquery/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/jquery/ColVis.min.js" type="text/javascript"></script>

<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/admin.js"></script>
