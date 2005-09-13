<?php
function isTpl($s)
{
	$dotpos=strrpos($s,".");
	$ext=substr($s,$dotpos+1);
	return ($ext == 'tpl' || $ext=='TPL' || $ext == 'php' || $ext == 'htm'
		|| $ext == 'html' || $ext == 'php3' || $ext == 'php4' || $ext == 'txt'
		|| $ext == 'css');
}
?>

<TABLE BORDER="0" CELLSPACING="2" CELLPADDING="0" WIDTH="100%">
<TR BGCOLOR="WHITE"><TD WIDTH="30%" VALIGN="TOP">
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" WIDTH="100%" class="table_list">
<TR class="table_list_header">
<TD VALIGN="TOP"><B><?php  putGS('Folders'); ?> </B></TD>
<?php 
if ($User->hasPermission("DeleteTempl")) {
	echo '<TD WIDTH="1%" ><B> '.getGS('Delete').' </B></TD>';
}
?>
</TR>
<?php 
	// once entered here, in the TemplateManagement, because the parameter What is 0 by default, even if the value set before is still set
	// its meaning is lost, so we can use the variable to switch between file and folders; let's say 0 is for folders and 1 for files
	// we only need it when deleting items

	$basedir=$_SERVER['DOCUMENT_ROOT']."/look/".decURL($listbasedir);

	$handle=opendir($basedir);
	while (($file = readdir($handle))!=false) {
	$full="$basedir/$file";
        $filetype=filetype($full);
        $isdir=false;
        $isfile=false;
        // avoiding the links
        if ($filetype=="dir") $isdir=true;
        else if ($filetype!="link") $isfile=true;
        // if it's a file
        if ($isfile){
            // filling the array with filenames
            $files[]=$file;
        }
        // if it's a directory but not  '..' or '.'
        else if ($isdir && $file != "." && $file != ".."){
            // filling the array with directory names
            $dirs[]=$file;
        }
    }
    
if (isset($dirs)) {
	sort($dirs);
	$color = 0;
	for($fi=0;$fi<count($dirs);$fi++) {
		$j= $dirs[$fi];

		$tr_class = "";
		if ($color) {
			$color=0;
			$tr_class = "class=\"list_row_even\"";
		} else {
			$color = 1;
			$tr_class = "class=\"list_row_odd\"";
		}
		print "<TR $tr_class><TD valign=\"center\"><IMG SRC='".$Campsite["ADMIN_IMAGE_BASE_URL"]."/folder.png' BORDER='0'>&nbsp;<A HREF='?Path=".encURL("$listbasedir/$j")."'>$j</A></TD>";

		if ($User->hasPermission("DeleteTempl"))
			print '<TD ALIGN="CENTER"><A HREF="/'.$ADMIN.'/templates/del.php?What=0&Path='.encURL($listbasedir).'&Name='.encURL($j).'"><IMG SRC="'.$Campsite["ADMIN_IMAGE_BASE_URL"].'/delete.png" BORDER="0" ALT="'.getGS('Delete folder').'" TITLE="'.getGS('Delete folder').'"></A></TD></TR>';
		else
			echo '</TR>';
    }
} else {
	echo '<TR><TD COLSPAN="2">'.getGS('No folders.').'</TD></TR>' ;
}
?>
</TABLE>
</TD><TD WIDTH="60%" VALIGN="TOP">
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" WIDTH="100%" class="table_list">
<TR class="table_list_header">
<TD  VALIGN="TOP"><B> <?php  putGS('Files'); ?> </B></TD>
<?php 
if ($User->hasPermission("ManageTempl")) {
	echo '<TD  VALIGN="TOP" WIDTH="1%" ><B> '.getGS('Duplicate').' </B></TD>';
}
if($User->hasPermission("DeleteTempl")) {
	echo '<TD  VALIGN="TOP" WIDTH="1%" ><B> '.getGS('Delete').' </B></TD>';
}
?>
</TR>
<?php 
if (isset($files)) {
	sort($files);
	$color = 0;
	for($fi=0;$fi<count($files);$fi++) {
		$j=$files[$fi];

		$tr_class = "";
		if ($color) {
			$color=0;
			$tr_class = "class=\"list_row_even\"";
		} else {
			$color = 1;
			$tr_class = "class=\"list_row_odd\"";
		}
		if (isTpl($j)) {
			print "<TR $tr_class><TD valign=\"center\"><IMG SRC='".$Campsite["ADMIN_IMAGE_BASE_URL"]."/generic.gif' BORDER='0'>&nbsp;<A HREF='/$ADMIN/templates/edit_template.php?Path=" .encURL($listbasedir)."&Name=".encURL($j)."'>$j</A></TD>";
		} else {
			print "<TR $tr_class><TD><IMG SRC='".$Campsite["ADMIN_IMAGE_BASE_URL"]."/generic.gif' BORDER='0'> $j</TD>";
		}
		if ($User->hasPermission("ManageTempl")){
			print '<TD ALIGN="CENTER"><A HREF="/'.$ADMIN.'/templates/dup.php?Path='.encURL($listbasedir).'&Name='.encURL($j).'"><IMG SRC="'.$Campsite["ADMIN_IMAGE_BASE_URL"].'/duplicate.png" BORDER="0" ALT="'.getGS('Duplicate file').'" TITLE="'.getGS('Duplicate file').'"></A></TD>';
		}
		if ($User->hasPermission("DeleteTempl")){
			print '<TD ALIGN="CENTER"><A HREF="/'.$ADMIN.'/templates/del.php?What=1&Path='.encURL($listbasedir).'&Name='.encURL($j).'"><IMG SRC="'.$Campsite["ADMIN_IMAGE_BASE_URL"].'/delete.png" BORDER="0" ALT="'.getGS('Delete file').'" TITLE="'.getGS('Delete file').'"></A></TD></TR>';
		} 
	}
}
else{
	echo '<TR><TD COLSPAN="2">'.getGS('No templates.').'</TD></TR>' ;
}

?>
</TABLE>
</TD></TR>
</TABLE>
