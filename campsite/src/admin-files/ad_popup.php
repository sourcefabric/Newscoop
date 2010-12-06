<?php
require_once($GLOBALS['g_campsiteDir']."/db_connect.php");
camp_load_translation_strings("home");
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
$ADReason = Input::Get('ADReason', 'string', getGS('You do not have the right to access this page.'), true);
?>
<head>
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet.css" />
	<title><?php putGS("Error"); ?></title>
</head>
<p>
<center>
<table border="0" cellspacing="0" cellpadding="8" class="message_box" align="center" style="margin-top: 20px; margin-bottom: 20px; margin-right: 10px;">
<tr>
	<td colspan="2">
		<b><font color="red"><?php putGS("Error"); ?> </font></b>
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
		<a href="javascript:self.close()"><b><?php  putGS('Close'); ?></b></a>
	</td>
</tr>
</table>
</center>

<?php camp_html_copyright_notice(); ?>
