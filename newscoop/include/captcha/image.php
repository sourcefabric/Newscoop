<?php
session_start();
require('php-captcha.inc.php');

$aFonts = array('fonts/VeraBd.ttf', 'fonts/VeraIt.ttf', 'fonts/Vera.ttf');

$oVisualCaptcha = new PhpCaptcha($aFonts, 200, 60);
//$oVisualCaptcha->SetBackgroundImages(array('capback1.jpg', 'capback2.jpg'));
//$oVisualCaptcha->SetFontColorRange(120, 255);
$oVisualCaptcha->Create();
?>