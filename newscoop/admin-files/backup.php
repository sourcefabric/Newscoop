<?php
camp_load_translation_strings("home");

// check permission
if (!$g_user->hasPermission('ManageBackup')) {
    camp_html_display_error(getGS("You do not have the right to manage backup."));
    exit;
}

// make backup list and define file name based on index
$files = getBackupList();
$fileIndex = Input::Get('index', 'integer', 0);
if (!empty($files[$fileIndex]['name'])) {
    $file = CS_PATH_SITE . DIR_SEP . 'backup' . DIR_SEP . $files[$fileIndex]['name'];
} else {
    $file = null;
}

// main controller
$action = Input::Get('action', 'string', null);
switch ($action) {

    case 'backup':
        set_time_limit(0);
        ob_end_flush();
        flush();
        echo str_repeat(' ', 2048);
        echo '<pre>';
        $options = array('--default-dir');
        include CS_PATH_SITE.DIR_SEP . 'bin' . DIR_SEP . 'newscoop-backup';
        echo '</pre><script type="text/javascript">window.opener.location.reload();</script>';
        echo '<center><a href=# onclick="window.close()">'.getGS('Close').'</a></center>';
        exit(0);

    case 'delete':
        if (!is_readable($file)) {
            camp_html_goto_page("/$ADMIN/backup.php");
        }
        $do = unlink($file);
        if($do === true) {
            camp_html_add_msg(getGS('The file $1 was deleted successfully.', basename($file)), 'ok');
        } else {
            camp_html_add_msg(getGS('There was an error trying to delete the file $1.', basename($file)));
        }
        camp_html_goto_page("/$ADMIN/backup.php");
        break;

    case 'restore':
        if (!is_readable($file)) {
            camp_html_goto_page("/$ADMIN/backup.php");
        }
        set_time_limit(0);
        ob_end_flush();
        flush();
        echo str_repeat(' ', 2048);
        echo '<pre>';
        $options = array(
            'f' => true,
            'e' => true,
            'b' => $file,
        );
        include CS_PATH_SITE.DIR_SEP . 'bin' . DIR_SEP . 'newscoop-restore';
        echo '</pre><script type="text/javascript">window.opener.location.reload();</script>';
        echo '<center><a href=# onclick="window.close()">'.getGS('Close').'</a></center>';
        exit(0);

    case 'download':
        if (!is_readable($file)) {
            camp_html_goto_page("/$ADMIN/backup.php");
        }
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Length: ' . filesize($file));
        header('Content-Type: application/x-gzip');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Transfer-Encoding: binary');
        set_time_limit(0);
        $fp = fopen($file, 'r');
        while (!feof($fp)) {
            print(fread($fp, 8192));
            flush();
            ob_flush();
        }
        fclose($fp);
        exit(0);

    case 'upload':
        foreach ($_FILES as $file) {
            if ($file['type'] == 'application/x-tar' || $file['type'] == 'application/x-gzip'
            || $file['type'] == 'application/gzip') {
                move_uploaded_file($file["tmp_name"], CS_PATH_SITE . DIR_SEP . 'backup' . DIR_SEP . $file['name']);
                camp_html_add_msg(getGS('The file $1 has been uploaded successfully.', $file['name']), 'ok');
            } else {
                camp_html_add_msg(getGS("You have tried to upload wrong backup file."));
            }
        }
        $files = getBackupList();
        break;
}

// show breadcrumbs
$crumbs = array();
$crumbs[] = array(getGS("Actions"), "");
$crumbs[] = array(getGS("Backup/Restore"), "");
$breadcrumbs = camp_html_breadcrumbs($crumbs);
echo $breadcrumbs;


// view template
?>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/campsite.js"></script>

<!--  CONTENT  -->
<div class="wrapper mid-sized">
<table border="0" cellspacing="0" cellpadding="0" class="action_buttons">
    <tr>
    <td valign="bottom"><b><?php echo getGS("Free disk space") . ': '
        . ceil(disk_free_space($Campsite['CAMPSITE_DIR'])/1024/1024) . ' ' . getGS('Mb');?></b></td>
    <td valign="bottom" style="padding-left: 10px;">
        <a href="#" onclick="if (confirm('<?php putGS('Are you sure you want to make new backup?')?>')) window.open('backup.php?action=backup', 'Backup', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=700, height=550, top=100, left=100');">
        <img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" border="0">
        <b><?php putGS("Make new backup")?></b>
        </a>
    </td>
    <td valign="bottom" style="padding-left: 10px;">
        <a href="#" onclick="$('#uploader').show();">
        <img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/tol.gif" border="0">
        <b><?php putGS("Upload backup file")?></b>
        </a>
    </td>
    </tr>
</table>
<p />
<div id="uploader" style="display:none">
<fieldset class="buttons">
<form method="POST" enctype="multipart/form-data">
<input type="submit" class="button right-floated" name="save" value="<?php putGS('Save'); ?>" />
<input type="hidden" name="action" value="upload" />
<input type="file" name="archivefile" size="30">

</form>
</fieldset>
</div>
<p />
<?php
camp_html_display_msgs();
?>
<table border="0" cellspacing="1" cellpadding="3" class="table_list full-sized">
    <tr class="table_list_header">
        <td align="left" valign="middle"><b><?php putGS("File"); ?></b></td>
        <td align="left" valign="middle"><b><?php putGS("Creation date"); ?></b></td>
        <td align="center" valign="middle"><b><?php echo getGS("Size") . ', ' . GetGS("Mb"); ?></b></td>
        <td align="center" valign="middle"><b><?php putGS("Download"); ?></b></td>
        <td align="center" valign="middle"><b><?php putGS("Restore"); ?></b></td>
        <td align="center" valign="middle"><b><?php putGS("Delete"); ?></b></td>
    </tr>
<?php
if ($files) {
    $color = 0;
    foreach ($files as $key => $file) {
        if ($color) {
            $color = 0;
            $tr_class = "class=\"list_row_even\"";
        } else {
            $color = 1;
            $tr_class = "class=\"list_row_odd\"";
        }
        print "<tr $tr_class><td>{$file['name']}</td><td align=\"left\">{$file['time']}</td><td align=\"center\">{$file['size']}</td>";
        print '<td align="center"><a href="backup.php?action=download&index='.$key.'"><img src="'
            .$Campsite["ADMIN_IMAGE_BASE_URL"].'/save.png" border="0" alt="'.getGS('Download file').'" title="'.getGS('Download file').'"></a>';
?>
		<td align="center">
			<a href="#" onclick="
				if( confirm( '<?php echo getGS('Warning: the existing data and themes will be deleted!') ?>'
					+'\n'+'<?php echo getGS('Are you sure you want to restore the file $1?', htmlspecialchars($file['name'])) ?>') )
					window.open('backup.php?action=restore&index=<?php echo $key ?>', 'Backup',
						'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=700, height=550, top=100, left=100');">
			<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"] ?>/help.png" border="0" alt="<?php echo getGS('Restore file') ?>"
				title="<?php echo getGS('Restore file') ?>"></a>
<?php
        print '<td align="center"><a href="backup.php?action=delete&index='.$key.'" onclick="return confirm(\''
            .getGS('Are you sure you want to delete the file $1?',htmlspecialchars($file['name'])).'\');"><img src="'
            .$Campsite["ADMIN_IMAGE_BASE_URL"].'/delete.png" border="0" alt="'.getGS('Delete file').'" title="'.getGS('Delete file').'"></a>';
        print '</tr>';
    }
} else {
    echo '<tr><td colspan="3">'.getGS('Backup list is empty.').'</td></tr>' ;
}
?>
</table>

<!--  END CONTENT  -->
</div>
<?php camp_html_copyright_notice();

function getBackupList() {
    $files = array();
    $backupDir = CS_PATH_SITE . DIR_SEP . 'backup';
    $handle = opendir($backupDir);
    while (($file = readdir($handle))!=false) {
        $fullPath = $backupDir. DIR_SEP . $file;
        if (!is_readable($fullPath)) {
            continue;
        }
        $fileType = filetype($fullPath);
        if ($fileType != "dir" && $file != '.htaccess') {
            $tmp = array();
            $tmp['name'] = $file;
            $tmp['size'] = ceil(filesize($fullPath)/1024/1024);
            $tmp['time'] = date("Y-F-d H:i:s", filectime($fullPath));
            $files[] = $tmp;
        }
    }
    sort($files);
    return array_reverse($files);
}
?>
