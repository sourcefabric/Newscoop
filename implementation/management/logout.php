<?php
// Delete the cookies
setcookie("TOL_UserId", "", time() - 3600);
setcookie("TOL_UserKey", "", time() - 3600);

## added by sebastian
if (function_exists ("incModFile")) {
	incModFile ();
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<script>
	<!--
	/*
	A slightly modified version of "Break-out-of-frames script"
	By JavaScript Kit (http://javascriptkit.com)
	*/
	
	if (window != top.fmain && window != top) {
		if (top.fmenu)
			top.fmain.location.href=location.href
		else
			top.location.href=location.href
	}
	// -->
	</script>
	<META HTTP-EQUIV="Expires" CONTENT="now">
</HEAD>
<BODY OnLoad="parent.location='/priv/login.php'">
</BODY>
</HTML>