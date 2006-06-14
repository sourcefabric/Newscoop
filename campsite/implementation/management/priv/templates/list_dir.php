<TABLE BORDER="0" CELLSPACING="2" CELLPADDING="0">
<TR>
	<TD VALIGN="TOP">
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
		<TR class="table_list_header">
			<TD VALIGN="TOP"><B><?php  putGS('Folders'); ?> </B></TD>
			<?php
			if ($g_user->hasPermission("DeleteTempl")) {
				echo '<TD><B> '.getGS('Delete').' </B></TD>';
			}
			?>
		</TR>
		<?php
		// Once entered here, in the TemplateManagement, because the parameter
		// 'What' is 0 by default, even if the value set before is still set
		// its meaning is lost, so we can use the variable to switch between
		// file and folders; let's say 0 is for folders and 1 for files
		// we only need it when deleting items

		$basedir = $Campsite['TEMPLATE_DIRECTORY'].urldecode($listbasedir);
		$handle = opendir($basedir);
		while (($file = readdir($handle))!=false) {
			$full = "$basedir/$file";
		    $filetype = filetype($full);
		    $isDir = false;
		    $isFile = false;
		    // Avoid the links
		    if ($filetype == "dir") {
		    	$isDir = true;
		    } else if ($filetype != "link") {
		    	$isFile = true;
		    }
		    // If it's a file
		    if ($isFile) {
		        // Fill the array with filenames
		        $files[] = $file;
		    }
		    // if it's a directory but not  '..' or '.'
		    else if ($isDir && ($file != ".") && ($file != "..")){
		        // filling the array with directory names
		        $dirs[] = $file;
		    }
		}

		$currentFolder = $listbasedir;
		if (empty($currentFolder)) {
			$currentFolder = "/";
		}
		if (isset($dirs)) {
			sort($dirs);
			$color = 0;
			foreach ($dirs as $dirname) {
				$tr_class = "";
				if ($color) {
					$color = 0;
					$tr_class = "class=\"list_row_even\"";
				} else {
					$color = 1;
					$tr_class = "class=\"list_row_odd\"";
				}
				print "<TR $tr_class><TD valign=\"center\"><IMG SRC='".$Campsite["ADMIN_IMAGE_BASE_URL"]."/folder.png' BORDER='0'>&nbsp;<A HREF='?Path=".urlencode("$listbasedir/$dirname")."'>$dirname</A></TD>";

				if ($g_user->hasPermission("DeleteTempl")) {
					print '<TD ALIGN="CENTER"><A HREF="/'.$ADMIN.'/templates/do_del.php?What=0&Path='.urlencode($listbasedir).'&Name='.urlencode($dirname).'" onclick="return confirm(\''.getGS('Are you sure you want to delete the folder $1 from $2?',htmlspecialchars($dirname),htmlspecialchars($currentFolder)).'\');"><IMG SRC="'.$Campsite["ADMIN_IMAGE_BASE_URL"].'/delete.png" BORDER="0" ALT="'.getGS('Delete folder').'" TITLE="'.getGS('Delete folder').'"></A></TD></TR>';
				} else {
					echo '</TR>';
				}
		    }
		} else {
			echo '<TR><TD COLSPAN="2">'.getGS('No folders.').'</TD></TR>' ;
		}
		?>
		</TABLE>
	</TD>

	<TD VALIGN="TOP">
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
		<TR class="table_list_header">
			<TD  VALIGN="TOP"><B> <?php  putGS('Files'); ?> </B></TD>
			<?php
			if ($g_user->hasPermission("ManageTempl")) {
				echo '<TD  VALIGN="TOP" ><B> '.getGS('Duplicate').' </B></TD>';
			}
			if($g_user->hasPermission("DeleteTempl")) {
				echo '<TD  VALIGN="TOP" ><B> '.getGS('Delete').' </B></TD>';
			}
			?>
		</TR>
		<?php
		if (isset($files)) {
			sort($files);
			$color = 0;
			foreach ($files as $filename) {
				if ($color) {
					$color = 0;
					$tr_class = "class=\"list_row_even\"";
				} else {
					$color = 1;
					$tr_class = "class=\"list_row_odd\"";
				}
				$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
				$imageExtensions = array("png", "jpg", "jpeg", "jpe", "gif");
				if (camp_is_template_file($filename)) {
					print "<TR $tr_class><TD valign=\"center\"><IMG SRC='".$Campsite["ADMIN_IMAGE_BASE_URL"]."/generic.gif' BORDER='0'>&nbsp;<A HREF='/$ADMIN/templates/edit_template.php?f_path=" .urlencode($listbasedir)."&f_name=".urlencode($filename)."'>$filename</A></TD>";
				} elseif (in_array($extension, $imageExtensions)) {
					print "<TR $tr_class><TD><IMG SRC='".$Campsite["ADMIN_IMAGE_BASE_URL"]."/image.png' BORDER='0'> <A HREF='/$ADMIN/templates/edit_template.php?f_path=" .urlencode($listbasedir)."&f_name=".urlencode($filename)."'>$filename</a></TD>";
				} else {
					print "<TR $tr_class><TD><IMG SRC='".$Campsite["ADMIN_IMAGE_BASE_URL"]."/generic.gif' BORDER='0'> $filename</TD>";
				}
				if ($g_user->hasPermission("ManageTempl")){
					print '<TD ALIGN="CENTER"><A HREF="/'.$ADMIN.'/templates/dup.php?Path='.urlencode($listbasedir).'&Name='.urlencode($filename).'"><IMG SRC="'.$Campsite["ADMIN_IMAGE_BASE_URL"].'/duplicate.png" BORDER="0" ALT="'.getGS('Duplicate file').'" TITLE="'.getGS('Duplicate file').'"></A></TD>';
				}
				if ($g_user->hasPermission("DeleteTempl")) {
					print '<TD ALIGN="CENTER"><A HREF="/'.$ADMIN.'/templates/do_del.php?What=1&Path='.urlencode($listbasedir).'&Name='.urlencode($filename).'" onclick="return confirm(\''.getGS('Are you sure you want to delete the template $1 from folder $2?', htmlspecialchars($filename),htmlspecialchars($currentFolder)).'\');"><IMG SRC="'.$Campsite["ADMIN_IMAGE_BASE_URL"].'/delete.png" BORDER="0" ALT="'.getGS('Delete file').'" TITLE="'.getGS('Delete file').'"></A></TD></TR>';
				}
			}
		}
		else{
			echo '<TR><TD COLSPAN="2">'.getGS('No templates.').'</TD></TR>' ;
		}
		?>
		</TABLE>
	</TD>
</TR>
</TABLE>
