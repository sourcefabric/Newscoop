INCLUDE_PHP_LIB(<*./priv*>)
B_DATABASE<**>
<?
    todefnum('IdPublication');
    query("SELECT * FROM Publications WHERE Id=$IdPublication", 'Publication');
    if ($NUM_ROWS != 0) {
	fetchRow($Publication);
	todefnum('TOL_UserId');
	todefnum('TOL_UserKey');
	query("SELECT * FROM Users WHERE Id=$TOL_UserId AND KeyId=$TOL_UserKey", 'User');
	if ($NUM_ROWS != 0){
	    fetchRow($User);
	?>dnl


<HTML>
<HEAD>
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE>Welcome to <? pgetHVar($Publication,'Name'); ?></TITLE>
<?
    query("INSERT IGNORE INTO Subscriptions SET IdUser=".getSVar($User,'Id').", IdPublication=".getSVar($Publication,'Id').", Active='Y'");
    if ($AFFECTED_ROWS > 0){ ?>dnl
	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=sections.php?IdPublication=<? pgetUVar($Publication,'Id'); ?>">
<?  } ?>dnl

</HEAD>

<? if($AFFECTED_ROWS <= 0){ ?>dnl
<BODY BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">
<H1><? pgetHVar($Publication,'Name'); ?></H1>

<BLOCKQUOTE>
	<P>You could not be subscribed to this publication.
	Try again and if the problem persists, contact the site administrator.
</BLOCKQUOTE>

</BODY>
<? } ?>dnl

</HTML>

<? } else  { ?>dnl
	<P>No publication found matching this site.
<? } ?>dnl
E_DATABASE<**>
