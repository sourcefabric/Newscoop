<?php

// User role depend on path to this file. Tricky: moderator folder is just symlink to admin files!
if (strpos($call_script, '/blog/admin/') !== false && $g_user->hasPermission('plugin_blog_admin')) {
    $is_admin = true;   
}

// Check permissions
if (!$is_admin) {
    camp_html_display_error(getGS('You do not have the right to manage blogs.'));
    exit;
}

if (Input::Get('save')) {
    $f_blogcomment_use_captcha = Input::Get('f_blogcomment_use_captcha', 'string', 'N');
    $f_blogcomment_mode = Input::Get('f_blogcomment_mode', 'string', 'registered');
    
    SystemPref::Set('PLUGIN_BLOGCOMMENT_USE_CAPTCHA', $f_blogcomment_use_captcha);
    SystemPref::Set('PLUGIN_BLOGCOMMENT_MODE', $f_blogcomment_mode);
    
    camp_html_add_msg(getGS("Blog preferences updated."), "ok");
}

camp_html_display_msgs();
?>

<br />

<FORM name="selector" method="post">
<table border="0" cellspacing="6" align="left" class="table_input" width="600px">
    <tr>
        <td colspan="2" align="left">
            <strong><?php putGS("Blog Settings"); ?></strong><br>
        </td>
    </tr>
    <tr>
        <td align="left"><?php putGS("Use captcha for blog comments form"); ?></td>
        <td><input type="checkbox" name="f_blogcomment_use_captcha" value="Y" <?php if (SystemPref::Get("PLUGIN_BLOGCOMMENT_USE_CAPTCHA") == 'Y') p("checked"); ?> /></td>
    </tr>
    <tr>
        <td align="left"><?php putGS("Allow post comments to"); ?></td>
        <td>
            <input type="radio" name="f_blogcomment_mode" value="registered" <?php if (SystemPref::Get("PLUGIN_BLOGCOMMENT_MODE") == 'registered') p("checked"); ?> /> <?php putGS("Only registered Users"); ?><br>
            
            <input type="radio" name="f_blogcomment_mode" value="name" <?php if (SystemPref::Get("PLUGIN_BLOGCOMMENT_MODE") == 'name') p("checked"); ?> /> <?php putGS("Registered or indicated by Name"); ?><br>
                    
            <input type="radio" name="f_blogcomment_mode" value="email" <?php if (SystemPref::Get("PLUGIN_BLOGCOMMENT_MODE") == 'email') p("checked"); ?> /> <?php putGS("Registered or indicated by Name and Email"); ?>
    
        </td>
    </tr>
    <tr>
    	<td colspan="2" align="center" style="padding-top: 10px;">
    		<input type="submit" name="save" value="<?php putGS("Save"); ?>" class="button">
    	</td>
    </tr>
</TABLE>
</form>