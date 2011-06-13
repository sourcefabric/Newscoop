<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/templates/template_common.php");

if (!$g_user->hasPermission('ManageTempl') || !$g_user->hasPermission("DeleteTempl")) {
    camp_html_display_error(getGS("You do not have the right to modify templates."));
    exit;
}

if ($f_path_name = Input::Get('f_path_name', 'string', '')) {
    $f_name = substr($f_path_name, strrpos($f_path_name, '/')+1);
    $f_path = substr($f_path_name, 0, strrpos($f_path_name, $f_name)-1);
} else {
    $f_path = Input::Get('f_path', 'string', '');
    $f_name = Input::Get('f_name', 'string', '');
}

$f_path = preg_replace('#//+#', '/', $f_path);

if ($f_path == '/') {
    $f_path = '';
}

$f_content = Input::Get('f_content', 'string', '', true);

$backLink  = "/$ADMIN/templates/";
if (!Template::IsValidPath($f_path.DIR_SEP.$f_name)) {
    camp_html_goto_page($backLink);
}
$filename = Template::GetFullPath($f_path, $f_name);
$templateName = (!empty($f_path) ? $f_path."/" : "").$f_name;
if ($templateName[0] == '/') {
    $templateName = substr($templateName, 1);
}
$templateObj = new Template($templateName);

if (!file_exists($filename)) {
    camp_html_display_error(getGS("Invalid template file $1" , $f_path."/$f_name"), $backLink);
    exit;
}

if (!is_writable($filename)) {
    camp_html_add_msg(camp_get_error_message(CAMP_ERROR_WRITE_FILE, $filename));
}

$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
$imageExtensions = array("png", "jpg", "jpeg", "jpe", "gif");

$templateDisplayName = $f_name;
if ($templateObj->exists()) {
    $templateDisplayName .= ' ('.getGS("Template ID:").' '.$templateObj->getTemplateId().')';
}

$f_lifetime = (int)$templateObj->getCacheLifetime();

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Templates"), "/$ADMIN/templates/");
$crumbs = array_merge($crumbs, camp_template_path_crumbs($f_path));
$crumbs[] = array(getGS("Edit template").": $templateDisplayName", "");
echo camp_html_breadcrumbs($crumbs);

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

camp_html_display_msgs();

if (in_array($extension, $imageExtensions)) {
    $urlPath = substr($filename, strlen($Campsite['TEMPLATE_DIRECTORY']));
    ?>
    <p>
    <table cellpadding="6" style="border: 1px dashed black; margin-left: 15px;">
    <tr>
        <td style="padding: 10px;">
            <img border="0" src="<?php p($Campsite['TEMPLATE_BASE_URL'].$urlPath); ?>?time=<?php p(time()); ?>">
        </td>
    </tr>
    </table>
    <p>
    <?php
} else {
    if (empty($f_content)) {
        if (is_readable($filename)) {
            $contents = file_get_contents($filename);
        } else {
            $contents = getGS("File cannot be read.");
        }
    } else {
        $contents = $f_content;
    }

    $templates = Template::GetAllTemplates(array('ORDER BY' => array('Level' => 'ASC', 'Name' => 'ASC')));
    ?>
    <p></p>
    <table>
      <tr>
        <td>
        <script language="javascript">
        function openFile()
        {
           if (document.forms['template_load'].elements['f_path_name'].value == "") {
               return false;
           }

           if (editAreaLoader.getValue('cField') !== document.forms['template_edit'].elements['oldValue'].value) {
               if (!confirm('<?php putGS('Changes have not been saved. Load new template without saving the current one?') ?>')) {
                   return false;;
               }
           }
           document.forms['template_load'].submit();
        }
        </script>
        <table class="box_table">
        <tr>
            <td align="left" colspan="2">
            <form name="template_load" method="post" action="/<?php echo $ADMIN; ?>/templates/edit_template.php">
                <?php echo SecurityToken::FormParameter(); ?>
                <table >
                <tr>
                    <td>
                        <b><?php putGS("Edit template:"); ?></b>
                        <select name="f_path_name" class="input_select" onChange="openFile()">
                            <option value="">---</option>
                            <?php
                            foreach ($templates as $template) {
                                if (1 || camp_is_text_file($template->getName())) {
                                    camp_html_select_option('/'.$template->getName(), $f_path.'/'.$f_name, $template->getName());
                                }

                            }
                            ?>
                        </select>
                    </td>
                </tr>
                </table>
                </form>
            </td>
        </tr>
        </table>

       </td>
       <td>

         <?php
         if ($g_user->hasPermission("DeleteTempl")
                 && is_writable($Campsite['TEMPLATE_DIRECTORY'].$f_path)) {
         ?>
        <table class="box_table">
        <tr>
            <td align="left" colspan="2">
                <form method="POST" action="/<?php echo $ADMIN; ?>/templates/do_replace.php" onsubmit="return <?php camp_html_fvalidate(); ?>;" ENCTYPE="multipart/form-data" >
                <?php echo SecurityToken::FormParameter(); ?>
                <input type="hidden" name="f_path" value="<?php p(htmlspecialchars($f_path)); ?>">
                <input type="hidden" name="f_old_name" value="<?php p(htmlspecialchars($f_name)); ?>">
                <table >
                <tr>
                    <td>
                        <b><?php putGS("Replace current with:"); ?></b> <input type="FILE" name="f_file" class="input_file" alt="file|<?php echo implode(",",camp_get_text_extensions()).",".implode(",", camp_get_image_extensions()); ?>" emsg="<?php putGS("You must select a file to upload."); ?>">
                    </td>
                    <td>
                        <input type="submit" name="replace" value="<?php putGS("Replace"); ?>" class="button">
                    </td>
                </tr>
                </table>
                </form>
            </td>
        </tr>
        </table>
        <?php } ?>
        </td>
      </tr>
    </table>

    <p>
    <form name="template_edit" method="POST" action="/<?php echo $ADMIN; ?>/templates/do_edit.php"  >
    <?php echo SecurityToken::FormParameter(); ?>
    <input type="hidden" NAME="Path" VALUE="<?php  p($f_path); ?>">
    <input type="hidden" NAME="Name" VALUE="<?php  p($f_name); ?>">
    <input type="hidden" NAME="oldValue" VALUE="<?php p(htmlspecialchars($contents)); ?>">
    <table border="0" cellspacing="0" cellpadding="0" class="box_table">
    <tr>
      <td colspan="2">
        <textarea rows="40" cols="120" name="cField" id="cField" wrap="NO" class="input_textarea"><?php p(htmlspecialchars($contents)); ?></textarea>
        <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/editarea/edit_area/edit_area_full.js"></script>
        <script type="text/javascript">
        $(function() {
            editAreaLoader.init({
                id : "cField",
                start_highlight: true,
                allow_toggle: true,
                toolbar: "search, go_to_line, |, undo, redo, |, select_font, |, syntax_selection, highlight, reset_highlight, word_wrap, |, change_smooth_selection, fullscreen, |, help",
                syntax: "smarty",
                syntax_selection_allow: "css,html,js,php,smarty,xml",
                replace_tab_by_spaces: 2
            });
        });
        </script>
      </td>
    </tr>
    <tr>
        <td>
            <?php  putGS('Cache lifetime, sec'); ?>: <input type="text" size="10" name="Lifetime" value="<?php  p($f_lifetime); ?>">
        </td>
    </tr>
    <tr>
        <td align="center">
            <?php  if ($g_user->hasPermission("DeleteTempl") && is_writable($filename)) { ?>
            <input type="submit" class="button" name="Save" value="<?php putGS('Save'); ?>">
            <?php  } else { ?>
            <input type="button" class="button" name="Done" value="<?php putGS('Done'); ?>" onclick="location.href='<?php echo "/$ADMIN/templates/?Path=".urlencode($f_path); ?>'">
            <?php  } ?>
        </td>
    </tr>
    </table>
    </form>

<?php } ?>
<p>
<p>

<?php camp_html_copyright_notice(); ?>
