<?php
/**
 * @package Newscoop
 * @subpackage Soundcloud plugin
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

$subdir = $Campsite['SUBDIR'];
if (file_exists(WWW_DIR.DIR_SEP.'js'.DIR_SEP.'admin.js')) {
    $js = 'js';
} else {
    $js = 'javascript';
}
?>
<!DOCTYPE html><html dir="ltr" lang="en"><head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <script src="<?= $subdir ?>/<?= $js ?>/jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
  <script src="<?= $subdir ?>/<?= $js ?>/jquery/jquery-ui-1.8.6.custom.min.js" type="text/javascript"></script>
  <script src="<?= $subdir ?>/<?= $js ?>/jquery/i18n/jquery.ui.datepicker-ru.js" type="text/javascript"></script>
  <script src="<?= $subdir ?>/<?= $js ?>/admin.js" type="text/javascript"></script>
  <script src="<?= $subdir ?>/<?= $js ?>/jquery/fancybox/jquery.fancybox-1.3.4.pack.js" type="text/javascript"></script>

  <link rel="stylesheet" type="text/css" href="<?= $subdir ?>/admin-style/jquery-ui-1.8.6.custom.css" />
  <link rel="stylesheet" type="text/css" href="<?= $subdir ?>/admin-style/admin_stylesheet_new.css" />
  <link rel="stylesheet" type="text/css" href="<?= $subdir ?>/admin-style/admin_stylesheet.css" />

  <script type="text/javascript">
      var g_admin_img = '<?= $subdir ?>/admin-style/images';
      var g_admin_url = '/<?= $ADMIN; ?>';
  </script>
</head>

<body>
<?php
$attachement = true;
include 'master.php';
?>
</body>
</html>