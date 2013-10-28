<?php
$translator = \Zend_Registry::get('container')->getService('translator');

require_once(CS_PATH_SITE.DIR_SEP . 'scripts' . DIR_SEP . 'file_processing.php');

// check permission
if (!$g_user->hasPermission('ManageBackup')) {
    camp_html_display_error($translator->trans("You do not have the right to manage backups.", array(), 'home'));
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
        $options = array('--default-dir', '--keep-session');
        include CS_PATH_SITE.DIR_SEP . 'bin' . DIR_SEP . 'newscoop-backup';
        echo '</pre><script type="text/javascript">window.opener.location.reload();</script>';
        echo '<center><a href=# onclick="window.close()">'.$translator->trans('Close').'</a></center>';
        exit(0);

    case 'delete':
        if (!is_readable($file)) {
            camp_html_goto_page("/$ADMIN/backup.php");
        }
        $do = unlink($file);
        if($do === true) {
            camp_html_add_msg($translator->trans('The file $1 was deleted successfully.', array('$1' => basename($file)), 'home'), 'ok');
        } else {
            camp_html_add_msg($translator->trans('There was an error trying to delete the file $1.', array('$1' => basename($file)), 'home'));
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
            '--keep-session'
        );
        include CS_PATH_SITE.DIR_SEP . 'bin' . DIR_SEP . 'newscoop-restore';
        echo '</pre><script type="text/javascript">window.opener.location.reload();</script>';
        echo '<center><a href=# onclick="window.close()">'.$translator->trans('Close').'</a></center>';
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
		// it looks that a problem could happen here if the server is out of its disk space
		try {
	        $fp = fopen($file, 'r');
	        while (!feof($fp)) {
	            print(fread($fp, 8192));
	            flush();
	            ob_flush();
	        }
	        fclose($fp);
		}
		catch (Exception $exc) {
			echo $translator->trans('Download was not successful. Check please that the server is not out of disk space.', array(), 'home');
		}
        exit(0);

    case 'upload':
        foreach ($_FILES as $file) {
			if (UPLOAD_ERR_OK != $file['error']) {
				$err_msg = camp_upload_errors($file['error']);
                camp_html_add_msg($translator->trans('Upload of file $1 was not successful.', array('$1' => $file['name']), 'home') . ' ' . $err_msg);
				continue;
			}

            if ($file['type'] == 'application/x-tar' || $file['type'] == 'application/x-gzip'
            || $file['type'] == 'application/gzip' || $file['type'] == 'application/x-compressed-tar') {
				// if not enough space, throws exception on the move attempt
				$move_failed = false;
				$move_dest = CS_PATH_SITE . DIR_SEP . 'backup' . DIR_SEP . $file['name'];
				try {
					move_uploaded_file($file['tmp_name'], $move_dest);
					camp_html_add_msg($translator->trans('The file $1 has been uploaded successfully.', array('$1' => $file['name']), 'home'), 'ok');
				}
				catch (Exception $exc) {
					$move_failed = true;
					camp_html_add_msg($translator->trans('The file $1 could not be moved. Check you have enough of disk space.', array('$1' => $file['name']), 'home'));
				}
				// try to remove the (partially) moved file if the move was not successful
				if ($move_failed) {
					try {
						unlink($move_dest);
					}
					catch (Exception $exc) {}
				}
            } else {
                camp_html_add_msg($translator->trans("You have tried to upload an invalid backup file.", array(), 'home'));
            }
        }
        $files = getBackupList();
        break;
}

// show breadcrumbs
$crumbs = array();
$crumbs[] = array($translator->trans("Actions"), "");
$crumbs[] = array($translator->trans("Backup/Restore", array(), 'home'), "");
$breadcrumbs = camp_html_breadcrumbs($crumbs);
echo $breadcrumbs;


// view template
?>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/campsite.js"></script>

<!--  CONTENT  -->
<div class="wrapper mid-sized">
<table border="0" cellspacing="0" cellpadding="0" class="action_buttons">
    <tr>
    <td valign="bottom"><b><?php echo $translator->trans("Free disk space", array(), 'home') . ': '
        . ceil(disk_free_space($Campsite['CAMPSITE_DIR'])/1024/1024) . ' ' . $translator->trans('MiB', array(), 'home');?></b></td>
    <td valign="bottom" style="padding-left: 10px;">
        <a href="#" onclick="if (confirm('<?php echo $translator->trans('Are you sure you want to make a new backup?', array(), 'home')?>')) window.open('backup.php?action=backup', 'Backup', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=700, height=550, top=100, left=100');">
        <img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" border="0">
        <b><?php echo $translator->trans("Make a new backup", array(), 'home')?></b>
        </a>
    </td>
    <td valign="bottom" style="padding-left: 10px;">
        <a href="#" onclick="$('#uploader').show();">
        <img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/tol.gif" border="0">
        <b><?php echo $translator->trans("Upload backup file", array(), 'home')?></b>
        </a>
    </td>
    </tr>
</table>
<p />
<div id="uploader" style="display:none">
<fieldset class="buttons">
<form method="POST" enctype="multipart/form-data">
<input type="submit" class="button right-floated" name="save" value="<?php echo $translator->trans('Save'); ?>" />
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
        <td align="left" valign="middle"><b><?php echo $translator->trans("File"); ?></b></td>
        <td align="left" valign="middle"><b><?php echo $translator->trans("Creation date"); ?></b></td>
        <td align="center" valign="middle"><b><?php echo $translator->trans("Size", array(), 'home') . ', ' . $translator->trans("Mb", array(), 'home'); ?></b></td>
        <td align="center" valign="middle"><b><?php echo $translator->trans("Download", array(), 'home'); ?></b></td>
        <td align="center" valign="middle"><b><?php echo $translator->trans("Restore", array(), 'home'); ?></b></td>
        <td align="center" valign="middle"><b><?php echo $translator->trans("Delete"); ?></b></td>
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
            .$Campsite["ADMIN_IMAGE_BASE_URL"].'/save.png" border="0" alt="'.$translator->trans('Download file', array(), 'home').'" title="'.$translator->trans('Download file', array(), 'home').'"></a>';
?>
		<td align="center">
			<a href="#" onclick="
				if( confirm( '<?php echo $translator->trans('Warning: the existing data and themes will be deleted!', array(), 'home') ?>'
					+'\n'+'<?php echo $translator->trans('Are you sure you want to restore the file $1?', array('$1' => htmlspecialchars($file['name'])), 'home') ?>') )
					window.open('backup.php?action=restore&index=<?php echo $key ?>', 'Backup',
						'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=700, height=550, top=100, left=100');">
			<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"] ?>/help.png" border="0" alt="<?php echo $translator->trans('Restore file', array(), 'home') ?>"
				title="<?php echo $translator->trans('Restore file', array(), 'home') ?>"></a>
<?php
        print '<td align="center"><a href="backup.php?action=delete&index='.$key.'" onclick="return confirm(\''
            .$translator->trans('Are you sure you want to delete the file $1?',array('$1' => htmlspecialchars($file['name'])), 'home').'\');"><img src="'
            .$Campsite["ADMIN_IMAGE_BASE_URL"].'/delete.png" border="0" alt="'.$translator->trans('Delete file', array(), 'home').'" title="'.$translator->trans('Delete file', array(), 'home').'"></a>';
        print '</tr>';
    }
} else {
    echo '<tr><td colspan="3">'.$translator->trans('Backup list is empty.', array(), 'home').'</td></tr>' ;
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
