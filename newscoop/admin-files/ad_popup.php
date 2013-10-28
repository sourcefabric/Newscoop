<?php
require_once($GLOBALS['g_campsiteDir']."/db_connect.php");
$translator = \Zend_Registry::get('container')->getService('translator');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
$ADReason = Input::Get('ADReason', 'string', $translator->trans('You do not have the right to access this page.', array(), 'home'), true);
?>
<head>
	<link rel="shortcut icon" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/images/7773658c3ccbf03954b4dacb029b2229.ico" />
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet.css" />
	<title><?php echo $translator->trans("Error"); ?></title>
</head>
<p>
<center>
<table border="0" cellspacing="0" cellpadding="8" class="message_box" align="center" style="margin-top: 20px; margin-bottom: 20px; margin-right: 10px;">
<tr>
	<td colspan="2">
		<b><font color="red"><?php echo $translator->trans("Error"); ?> </font></b>
		<hr noshade size="1" color="black" />
	</td>
</tr>
<tr>
	<td colspan="2" align="center" style="padding-left: 15px; padding-right: 15px;">
		<?php  print htmlspecialchars($ADReason); ?>
	</td>
</tr>
<tr>
	<td colspan="2" align="center">
		<a href="javascript:self.close()"><b><?php  echo $translator->trans('Close'); ?></b></a>
	</td>
</tr>
</table>
</center>

<?php camp_html_copyright_notice(); ?>
