B_HTML
INCLUDE_PHP_LIB(<*.*>)
B_DATABASE

<?
    $ok=0;
    todef ('UserName');
    todef ('UserPassword');
    query ( "SELECT Id FROM Users WHERE UName='$UserName' AND Password=PASSWORD('$UserPassword') AND Reader='N'", 'q' );
    if ($NUM_ROWS) {
	fetchRow ($q);
	query ( "UPDATE Users SET KeyId=RAND()*1000000000+RAND()*1000000+RAND()*1000 WHERE Id=".getVar($q,'Id'));
	$ok=$AFFECTED_ROWS;
	if ($ok) {
	    query ( "SELECT Id, KeyId FROM Users WHERE Id=".getVar($q,'Id'), 'usrs');
	}
    }
?>

B_HEAD
	X_EXPIRES
<? if ($ok==0) { ?>dnl
	X_TITLE(<*Login failed*>)
<? } else { 
    fetchRow($usrs);?>dnl
	X_TITLE(<*Login*>)
	X_COOKIE(<*TOL_UserId=<? print getVar ($usrs,'Id'); ?>*>)
	X_COOKIE(<*TOL_UserKey=<? print getVar ($usrs,'KeyId'); ?>*>)
	<?
	    if (!isset($selectlanguage))<*
		$selectlanguage='en';
	    *>
	?>
	X_COOKIE(<*TOL_Language=<? p($selectlanguage); ?>*>)
	X_REFRESH(<*0; URL=X_ROOT/*>)
<? } ?>dnl
E_HEAD

<? if ($ok==0) { ?>dnl
B_STYLE
E_STYLE

B_BODY
B_HEADER(<*Login failed*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Login*>, <*login.php*>)
E_HEADER_BUTTONS
E_HEADER

<BLOCKQUOTE>
	<LI><? putGS('Login failed'); ?></LI>
	<LI><? putGS('Pease make sure that you typed the correct user name and password.'); ?></LI>
	<LI><? putGS('If your problem persists please contact the site administrator $1','<A HREF="mailto:'.encURL($SERVER_ADMIN).'">'.encHTML($SERVER_ADMIN) );?></A></LI>
</BLOCKQUOTE>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

