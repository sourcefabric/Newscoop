<?

	$sb='';
	for ($i=0;$i<$pieces;$i++){
		$var1="base$i";
		$var2="translation$i";
		$var1=strtr(stripslashes($$var1),"\"","'");
		$var2=strtr(stripslashes($$var2),"\"","'");
//		$var2 = utf8_encode($$var2);
		$sb.="regGS(\"$var1\",\"$var2\");\n";
	}

	//copy($destfile,$destfile.'.bak');
	$fh=fopen($destfile,'w');
	fputs($fh,"<?\n\n$sb\n\n?>");
	fclose($fh);
					//print $sb;
	print "Done<br>\n";

?>
