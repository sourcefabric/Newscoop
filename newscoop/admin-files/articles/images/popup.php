<?php
require_once($GLOBALS['g_campsiteDir'].'/include/campsite_constants.php');
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/article_common.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/Image.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ImageSearch.php');

$translator = \Zend_Registry::get('container')->getService('translator');

if (!$g_user->hasPermission("AttachImageToArticle")) {
	$errorStr = $translator->trans('You do not have the right to attach images to articles.', array(), 'article_images');
	camp_html_display_error($errorStr, null, true);
	exit;
}

$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_image_attach_mode = camp_session_get('f_image_attach_mode', 'new');

if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid input: $1', array('$1' => Input::GetErrorString())), $_SERVER['REQUEST_URI'], true);
	exit;
}

$articleObj = new Article($f_language_selected, $f_article_number);
?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="Expires" content="now" />
  <link rel="shortcut icon" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/images/7773658c3ccbf03954b4dacb029b2229.ico" />
  <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet_new.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet.css" />
  <title><?php echo $translator->trans("Attach Image To Article", array(), 'article_images'); ?></title>
</head>
<body>
<?php camp_html_display_msgs(); ?> 
<table style="margin-top: 10px; margin-left: 5px;" cellpadding="0" cellspacing="0">
<tr>
	<?php if ($g_user->hasPermission('AddImage')) { ?>
	<td style="padding: 3px; background-color: #EEE; border-top: 1px solid #8baed1; border-left: 1px solid #8baed1; <?php if ($f_image_attach_mode != "new") { ?>border-bottom: 1px solid #8baed1;<?php } ?>"><a href="<?php echo camp_html_article_url($articleObj, $f_language_id, "images/popup.php", "", "&f_image_attach_mode=new"); ?>"><img src="<?php p($Campsite['ADMIN_IMAGE_BASE_URL']); ?>/add.png" border="0"><b><?php echo $translator->trans("Attach New Image", array(), 'article_images'); ?></b></a></td>
	<?php } ?>

	<td style="padding: 3px; background-color: #EEE; border-top: 1px solid #8baed1; border-right: 1px solid #8baed1; border-left: 1px solid #8baed1; <?php if ($f_image_attach_mode != "existing") { ?>border-bottom: 1px solid #8baed1;<?php } ?>"><a href="<?php echo camp_html_article_url($articleObj, $f_language_id, "images/popup.php", "", "&f_image_attach_mode=existing"); ?>"><img src="<?php p($Campsite['ADMIN_IMAGE_BASE_URL']); ?>/add.png" border="0"><b><?php echo $translator->trans("Attach Existing Image", array(), 'article_images'); ?></b></a></td>
</tr>
</table>

<?php
if ($f_image_attach_mode == "existing") {
    include("search.php");
} else {
    include("add.php");
}?>

</body>
</html>
