<?php
$f_blogcomment_use_captcha = Input::Get('f_blogcomment_use_captcha', 'string', 'N');
$f_blogcomment_mode = Input::Get('f_blogcomment_mode', 'string', 'registered');

SystemPref::Set('PLUGIN_BLOGCOMMENT_USE_CAPTCHA', $f_blogcomment_use_captcha);
SystemPref::Set('PLUGIN_BLOGCOMMENT_MODE', $f_blogcomment_mode);
?>