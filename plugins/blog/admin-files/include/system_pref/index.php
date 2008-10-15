<tr>
	<td colspan="2"><hr></td>
</tr>
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