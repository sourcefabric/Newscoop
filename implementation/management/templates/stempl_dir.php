<TABLE BORDER="0" CELLSPACING="2" CELLPADDING="0" WIDTH="100%">
<TR BGCOLOR="WHITE"><TD WIDTH="30%" VALIGN="TOP">
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" WIDTH="100%">
<TR BGCOLOR="#C0D0FF">
<TD  ><B> <? putGS('Folders'); ?> </B></TD>
</TR>
<?
    $c="";
    
    $basedir=decURL("$DOCUMENT_ROOT$listbasedir");

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
            // filling the array
            $files[]=$file;
        }
        // if it's a directory but not the .. or .
        else if ($isdir&&$file!="."&&$file!=".."){
            // filling the array
            $dirs[]=$file;
        }
    }
    
if (isset($dirs)) {
	sort($dirs);
    for($fi=0;$fi<count($dirs);$fi++) {
	    $j=$dirs[$fi];

	    if ($c == "#D0D0D0" )
		$c="#D0D0B0";
	    else
		$c="#D0D0D0";
	    
	    print "<TR BGCOLOR='$c'><TD><TABLE BORDER='0' CELLSPACING='1' CELLPADDING='0'><TR><TD><IMG SRC='/priv/img/icon/dir.gif' BORDER='0'></TD><TD><A HREF='".encURL($j)."/?$params'>$j</A></TD></TR></TABLE></TD>";
	    
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
<TD  ><B> <? putGS('Files'); ?> </B></TD>
<TD WIDTH="1%" ><B> <? putGS('Select'); ?> </B></TD>
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
	    
	    print "<TR BGCOLOR='$c'><TD><TABLE BORDER='0' CELLSPACING='1' CELLPADDING='0'><TR><TD><IMG SRC='/priv/img/icon/generic.gif' BORDER='0'></TD><TD>$j</TD></TR></TABLE></TD>";
	    
	    print "<TD ALIGN='CENTER'><A HREF='/priv/pub/issues/set.php?$params&Path=".encURL($listbasedir).encURL($j)."'><IMG SRC='/priv/img/icon/image.gif' BORDER='0' ALT='".getGS('Set template')."'></A></TD></TR>";
    }
}
else{
echo '<TR><TD COLSPAN="2">'.getGS('No templates.').'</TD></TR>' ;
}

?>
</TABLE>
</TD></TR>
</TABLE>
