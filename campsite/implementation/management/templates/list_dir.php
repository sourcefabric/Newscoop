<? function isTpl($s){
	$dotpos=strrpos($s,".");
	$ext=substr($s,$dotpos+1);
        return (($ext=='tpl') || ($ext=='TPL'));
}  ?>

<TABLE BORDER="0" CELLSPACING="2" CELLPADDING="0" WIDTH="100%">
<TR BGCOLOR="WHITE"><TD WIDTH="30%" VALIGN="TOP">
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" WIDTH="100%">
<TR BGCOLOR="#C0D0FF">
<TD VALIGN="TOP"><B> <? putGS('Folders'); ?> </B></TD>
<?
    if ( $dta != "0" ) {
	echo '<TD WIDTH="1%" ><B> '.getGS('Delete').' </B></TD>';
    }
?>
</TR>
<?
    $c="";
	// once entered here, in the TemplateManagement, because the parameter What is 0 by default, even if the value set before is still set
	// its meaning is lost, so we can use the variable to switch between file and folders; let's say 0 is for folders and 1 for files
	// we only need it when deleting items
	
    $basedir="$DOCUMENT_ROOT".decURL($listbasedir);

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
    for($fi=0;$fi<count($dirs);$fi++) {
	    $j=$dirs[$fi];

	    if ($c == "#D0D0D0" )		//alternate the color lines in the table
		$c="#D0D0B0";
	    else
		$c="#D0D0D0";
	    
	    print "<TR BGCOLOR='$c'><TD><TABLE BORDER='0' CELLSPACING='1' CELLPADDING='0'><TR><TD><IMG SRC='/priv/img/icon/dir.gif' BORDER='0'></TD><TD><A HREF='".encURL($j)."'>$j</A></TD></TR></TABLE></TD>";

		if ($dta != 0)
			print "<TD ALIGN='CENTER'><A HREF='/priv/templates/del.php?What=0&Path=".encURL($listbasedir)."&Name=".encURL($j)."'><IMG SRC='/priv/img/icon/x.gif' BORDER='0' ALT='".getGS('Delete folder')."'></A></TD></TR>";
		else echo '</TR>';
    }
}
else{
echo '<TR><TD COLSPAN="2">'.getGS('No folders.').'</TD></TR>' ;
}
?>
</TABLE>
</TD><TD WIDTH="60%" VALIGN="TOP">
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" WIDTH="100%">
<TR BGCOLOR="#C0D0FF">
<TD  VALIGN="TOP"><B> <? putGS('Files'); ?> </B></TD>
<?
    if($dta!= "0") {
    	echo '<TD  VALIGN="TOP" WIDTH="1%" ><B> '.getGS('Duplicate').' </B></TD>';
	echo '<TD  VALIGN="TOP" WIDTH="1%" ><B> '.getGS('Delete').' </B></TD>';
	}
?>
</TR>
<?
    $c="";

if (isset($files)) {
	sort($files);
	for($fi=0;$fi<count($files);$fi++) {
		$j=$files[$fi];

		if ($c == "#D0D0D0" )
			$c="#D0D0B0";
		else
			$c="#D0D0D0";
	    
		if(isTpl($j) && $dta) {
			print "<TR BGCOLOR='$c'><TD><TABLE BORDER='0' CELLSPACING='1' CELLPADDING='0'><TR><TD><IMG SRC='/priv/img/icon/generic.gif' BORDER='0'></TD><TD><A HREF='/priv/templates/edit_template.php?Path=".encURL($listbasedir)."&Name=".encURL($j)."'>$j</A></TD></TR></TABLE></TD>";				
		}
		else{
			print "<TR BGCOLOR='$c'><TD><TABLE BORDER='0' CELLSPACING='1' CELLPADDING='0'><TR><TD><IMG SRC='/priv/img/icon/generic.gif' BORDER='0'></TD><TD>$j</TD></TR></TABLE></TD>";
		}
		if ($dta != 0){
			print "<TD ALIGN='CENTER'><A HREF='/priv/templates/dup.php?Path=".encURL($listbasedir)."&Name=".encURL($j)."'><IMG SRC='/priv/img/icon/dup.gif' BORDER='0' ALT='".getGS('Duplicate file')."'></A></TD>";
			print "<TD ALIGN='CENTER'><A HREF='/priv/templates/del.php?What=1&Path=".encURL($listbasedir)."&Name=".encURL($j)."'><IMG SRC='/priv/img/icon/x.gif' BORDER='0' ALT='".getGS('Delete file')."'></A></TD></TR>";
		}		
		else echo '<TD ALIGN="CENTER"></td></TR>';
	    }
}
else{
	echo '<TR><TD COLSPAN="2">'.getGS('No templates.').'</TD></TR>' ;
}

?>
</TABLE>
</TD></TR>
</TABLE>
