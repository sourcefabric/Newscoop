B_HTML
INCLUDE_PHP_LIB(<*.*>)
B_HEAD
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
	X_EXPIRES
	X_TITLE(<*Logout*>)
	X_COOKIE(<*TOL_UserId=0*>)
	X_COOKIE(<*TOL_UserKey=0*>)
E_HEAD
<BODY OnLoad="parent.location='X_ROOT/login.php'">
</BODY>
E_HTML
