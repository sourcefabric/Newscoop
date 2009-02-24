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
    $f_blog_root_topic_id = Input::Get('f_blog_root_topic_id', 'int', 0);
    $f_blog_root_mood_id = Input::Get('f_blog_root_mood_id', 'int', 0);
    $f_blog_image_derivates = Input::Get('f_blog_image_derivates', 'string');
    
    SystemPref::Set('PLUGIN_BLOGCOMMENT_USE_CAPTCHA', $f_blogcomment_use_captcha);
    SystemPref::Set('PLUGIN_BLOGCOMMENT_MODE', $f_blogcomment_mode);
    SystemPref::Set('PLUGIN_BLOG_ROOT_TOPIC_ID', $f_blog_root_topic_id);
    SystemPref::Set('PLUGIN_BLOG_ROOT_MOOD_ID', $f_blog_root_mood_id);
    SystemPref::Set('PLUGIN_BLOG_IMAGE_DERIVATES', $f_blog_image_derivates);
    
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
        <td align="left"><?php putGS("Blog topic root"); ?></td>
        <td>
            <select name="f_blog_root_topic_id" />
                <?php
                foreach (Topic::GetTree() as $path) {
                    $option='';
                    $currentTopic = camp_array_peek($path, false, -1);
                    $name = $currentTopic->getName($language_id);
                    
                    if (empty($name)) {
                        // Backwards compatibility
                        $name = $currentTopic->getName(1);
                        if (empty($name)) {
                            continue;
                        }
                    }
                    foreach ($path as $topicObj) {
                        $name = $topicObj->getName($language_id);
                        if (empty($name)) {
                            $name = $topicObj->getName(1);
                            if (empty($name)) {
                                $name = "-----";
                            }
                        }
                        $option .= " / ".htmlspecialchars($name);
                    }
                    $selected = $currentTopic->getTopicId() == SystemPref::Get('PLUGIN_BLOG_ROOT_TOPIC_ID') ? 'selected' : '';
                    p("<option value=\"{$currentTopic->getTopicId()}\" $selected>$option</option>");
                }
                ?>
            </select>
       </td>
    </tr>
        <tr>
        <td align="left"><?php putGS("Blog mood root"); ?></td>
        <td>
            <select name="f_blog_root_mood_id" />
                <?php
                foreach (Topic::GetTree() as $path) {
                    $option='';
                    $currentTopic = camp_array_peek($path, false, -1);
                    $name = $currentTopic->getName($language_id);
                    
                    if (empty($name)) {
                        // Backwards compatibility
                        $name = $currentTopic->getName(1);
                        if (empty($name)) {
                            continue;
                        }
                    }
                    foreach ($path as $topicObj) {
                        $name = $topicObj->getName($language_id);
                        if (empty($name)) {
                            $name = $topicObj->getName(1);
                            if (empty($name)) {
                                $name = "-----";
                            }
                        }
                        $option .= " / ".htmlspecialchars($name);
                    }
                    $selected = $currentTopic->getTopicId() == SystemPref::Get('PLUGIN_BLOG_ROOT_MOOD_ID') ? 'selected' : '';
                    p("<option value=\"{$currentTopic->getTopicId()}\" $selected>$option</option>");
                }
                ?>
            </select>
       </td>
    </tr>
    <tr>
        <td align="left"><?php putGS("Image derivate commands (one command per line)"); ?></td>
        <td><textarea name="f_blog_image_derivates" rows=10 cols=50><?php p(SystemPref::Get("PLUGIN_BLOG_IMAGE_DERIVATES")) ?></textarea></td>
    </tr>
    <tr>
    	<td colspan="2" align="center" style="padding-top: 10px;">
    		<input type="submit" name="save" value="<?php putGS("Save"); ?>" class="button">
    	</td>
    </tr>
</TABLE>
</form>