<?php
$translator = \Zend_Registry::get('container')->getService('translator');

global $ADMIN, $g_user;

// Page title
$siteTitle = (!empty($Campsite['site']['title'])) ? htmlspecialchars($Campsite['site']['title']) : $translator->trans("Newscoop", array(), 'home') . $Campsite['VERSION'];

// locale setting for datepicker
$locale = !empty($_COOKIE['TOL_Language']) ? $_COOKIE['TOL_Language'] : 'en';
?>
<!DOCTYPE html>
<html lang="<?php echo $locale; ?>" dir="ltr">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="Expires" content="now" />
  <title><?php p($siteTitle); ?></title>

  <script type="text/javascript"><!--
    var website_url = "<?php echo $Campsite['WEBSITE_URL'];?>";

    var localizer = localizer || {};
    localizer.processing = '<?php echo $translator->trans('Processing...', array(), 'home'); ?>';
    localizer.session_expired = '<?php echo $translator->trans('Session expired.', array(), 'home'); ?>';
    localizer.please = '<?php echo $translator->trans('Please', array(), 'home'); ?>';
    localizer.login = '<?php echo $translator->trans('login', array(), 'home'); ?>';
  //--></script>

    <?php include dirname(__FILE__) . '/html_head.php'; ?>

    <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/jquery/fg.menu.js" type="text/javascript"></script>
    <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/jquery/jquery.widgets.js" type="text/javascript"></script>
    <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/jquery/fancybox/jquery.fancybox-1.3.4.pack.js" type="text/javascript"></script>

    <link rel="shortcut icon" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/images/7773658c3ccbf03954b4dacb029b2229.ico" />

    <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/fg.menu.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/widgets.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/js/jquery/fancybox/jquery.fancybox-1.3.4.css" />

    <!--[if IE 6]>
    <style type="text/css">
        .fg-menu-ipod .fg-menu li { width: 95%; }
        .fg-menu-ipod .ui-widget-content { border:0; }
    </style>
    <![endif]-->

  <script type="text/javascript">
  <!--
    var g_admin_url = '/<?php echo $ADMIN; ?>';
    var g_security_token = '<?php echo SecurityToken::GetToken(); ?>';
    <?php if (strpos($_SERVER['HTTP_REFERER'], 'login.php') !== FALSE) { ?>
    if (opener && !opener.closed && opener.setSecurityToken) {
        opener.setSecurityToken(g_security_token);
        opener.focus();
        window.close();
    }
    <?php } ?>
    var g_admin_img = '<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>';
    $(document).ready(function () {
        $.datepicker.setDefaults( $.datepicker.regional['<?php echo $locale; ?>'] );
    });
    //-->
  </script>
  <script type="text/javascript">
  <!--
  $(function () {
      // BUTTONS
      $('.fg-button').hover(
          function () { $(this).removeClass('fg-button-ui-state-default').addClass('fg-button-ui-state-focus'); },
          function () { $(this).removeClass('fg-button-ui-state-focus').addClass('fg-button-ui-state-default'); }
      );

      // MENUS
      $('#newscoop_menu_content').topmenu({
          content: $('#newscoop_menu_content').next().html(),
          flyOut: true,
          showSpeed: 150
      });
      <?php if (isset($showAdminActions)) { ?>
      $('#newscoop_menu_action').topmenu({
          content: $('#newscoop_menu_action').next().html(),
          flyOut: true,
          showSpeed: 150
      });
      <?php
      }
      if (isset($showConfigureMenu)) {
      ?>
      $('#newscoop_menu_configure').topmenu({
          content: $('#newscoop_menu_configure').next().html(),
          flyOut: true,
          showSpeed: 150
      });
      <?php
      }
      if (isset($showUserMenu)) {
      ?>
      $('#newscoop_menu_users').topmenu({
          content: $('#newscoop_menu_users').next().html(),
          flyOut: true,
          showSpeed: 150
      });
      <?php } ?>
      $('#newscoop_menu_plugins').topmenu({
          content: $('#newscoop_menu_plugins').next().html(),
          flyOut: true,
          showSpeed: 150
      });
  });
  //-->
  </script>
  <script type="text/javascript">
  $(document).ready(function () {
      var sticky_limit = 0;
      $(window).scroll(function () {
          if ($('.sticky').size() == 0) {
              return false; // no sticky
          }

          var windowTop = $(window).scrollTop();
          var stickyTop = $('.sticky').offset().top;
          if (windowTop > stickyTop && sticky_limit == 0) {
              $('.sticky').css('width', '100%').css('position', 'fixed').css('top', '0');
              sticky_limit = stickyTop;
          }
          if (sticky_limit > 0 && windowTop < sticky_limit) {
              $('.sticky').css('position', 'relative');
              sticky_limit = 0;
          }
      });
  });

  var user_msgs = '';
  <?php if (!empty($_SESSION['camp_user_msgs'])) { ?>
  user_msgs = "<?php echo str_replace('"', "'", $_SESSION['camp_user_msgs'][0]['msg']); ?>";
  <?php
    $_SESSION['camp_user_msgs'] = array();
  } ?>
  </script>
</head>
<body>
<div class="meta-bar">
    <ul>
        <li><a href="/<?php p($ADMIN); ?>/logout"><?php echo $translator->trans('Logout'); ?></a></li>
        <li><a href="<?php p($Campsite['site']['help_url']); ?>" target="_blank"><?php echo $translator->trans('Help', array(), 'home'); ?></a></li>
        <li><?php echo $translator->trans("Signed in: $1", array('$1' => '<strong>' . $g_user->getFirstName() . '</strong>'), 'home'); ?></li>
    </ul>
</div>

<?php include_once APPLICATION_PATH . '/layouts/scripts/admin_menu.phtml' ?>
