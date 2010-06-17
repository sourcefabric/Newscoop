<?php
camp_load_translation_strings("backup");

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
        include CS_PATH_SITE.DIR_SEP . 'bin' . DIR_SEP . 'campsite-backup';
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
        include CS_PATH_SITE.DIR_SEP . 'bin' . DIR_SEP . 'campsite-restore';
        echo '</pre><script type="text/javascript">window.opener.location.reload();</script>';
        echo '<center><a href=# onclick="window.close()">'.getGS('Close').'</a></center>';
        exit(0);

    case 'download':
        if (!is_readable($file)) {
            camp_html_goto_page("/$ADMIN/backup.php");
        }
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Length: ' . getRealSize($file));
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
}

// show breadcrumbs
$crumbs = array();
$crumbs[] = array(getGS("Actions"), "");
$crumbs[] = array(getGS("Backup/Restore"), "");
$breadcrumbs = camp_html_breadcrumbs($crumbs);
echo $breadcrumbs;


// view template
?>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/campsite.js"></script>
<p />
<table border="0" cellspacing="0" cellpadding="0" class="action_buttons">
    <tr>
    <td valign="bottom"><b><?php echo getGS("Free disk space") . ': '
        . ceil(disk_free_space($Campsite['CAMPSITE_DIR'])/1024/1024) . ' ' . getGS('Mb');?></b></td>
    <td valign="bottom" style="padding-left: 10px;">
        <a href="#" onclick="if (confirm('<?php putGS('Are you sure you want to make new backup?')?>')) window.open('backup.php?action=backup', '', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=700, height=550, top=100, left=100');">
        <img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" border="0">
        <b><?php putGS("Make new backup")?></b>
        </a>
    </td>
    </tr>
</table>
<p />
<?php
camp_html_display_msgs();
?>
<table border="0" cellspacing="1" cellpadding="3" class="table_list">
    <tr class="table_list_header">
        <td align="left" valign="middle"><b><?php putGS("File"); ?></b></td>
        <td align="left" valign="middle"><b><?php putGS("Creation date"); ?></b></td>
        <td align="left" valign="middle"><b><?php echo getGS("Size") . ', ' . GetGS("Mb"); ?></b></td>
        <td align="left" valign="middle"><b><?php putGS("Download"); ?></b></td>
        <td align="left" valign="middle"><b><?php putGS("Restore"); ?></b></td>
        <td align="left" valign="middle"><b><?php putGS("Delete"); ?></b></td>
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
        print "<tr $tr_class><td>{$file['name']}</td><td align=\"center\">{$file['time']}</td><td align=\"center\">{$file['size']}</td>";
        print '<td align="center"><a href="backup.php?action=download&index='.$key.'"><img src="'
            .$Campsite["ADMIN_IMAGE_BASE_URL"].'/save.png" border="0" alt="'.getGS('Download file').'" title="'.getGS('Download file').'"></a>';
        print '<td align="center"><a href="#" onclick="if (confirm(\''.getGS('Are you sure you want to restore the file $1?',
            htmlspecialchars($file['name'])).'\')) window.open(\'backup.php?action=restore&index='.$key.'\', \'\', \'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=700, height=550, top=100, left=100\');"><img src="'
            .$Campsite["ADMIN_IMAGE_BASE_URL"].'/help.png" border="0" alt="'.getGS('Restore file').'" title="'.getGS('Restore file').'"></a>';
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
<?php camp_html_copyright_notice();

// internal filesize function returns maximum 4Gb size
function getRealSize($file) {
    clearstatcache();
    $INT = 4294967295;
    $size = filesize($file);
    $fp = fopen($file, 'r');
    fseek($fp, 0, SEEK_END);
    if (ftell($fp)==0) $size += $INT;
    fclose($fp);
    if ($size<0) $size += $INT;

    return $size;
}

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
        if ($fileType != "link" && $fileType != "dir" && substr($file, 0, 6) == 'backup') {
            $tmp = array();
            $tmp['name'] = $file;
            $tmp['size'] = ceil(getRealSize($fullPath)/1024/1024);
            $tmp['time'] = date("Y-F-d H:i:s", filectime($fullPath));
            $files[] = $tmp;
        }
    }
    sort($files);
    return array_reverse($files);
}
?>