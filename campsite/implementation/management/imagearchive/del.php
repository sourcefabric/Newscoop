<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<?php
	require_once("../lib_campsite.php");
	require_once ("../languages.php");
	require_once('include.inc.php');
	require_once("$DOCUMENT_ROOT/db_connect.php");

	$globalfile=selectLanguageFile('..','globals');
	$localfile=selectLanguageFile('.','locals');
	@include ($globalfile);
	@include ($localfile);
?>

<?php
	todefnum('TOL_UserId');
	todefnum('TOL_UserKey');
	query ("SELECT * FROM Users WHERE Id=$TOL_UserId AND KeyId=$TOL_UserKey", 'Usr');
	$access=($NUM_ROWS != 0);
	if ($NUM_ROWS) {
	fetchRow($Usr);
	query ("SELECT * FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'XPerm');
	 if ($NUM_ROWS){
		fetchRow($XPerm);
	 }
	 else $access = 0;						//added lately; a non-admin can enter the administration area; he exists but doesn't have ANY rights
	 $xpermrows= $NUM_ROWS;
	}
	else {
	query ("SELECT * FROM UserPerm WHERE 1=0", 'XPerm');
	}
?>


<?php 
todefnum('TOL_UserId');
todefnum('TOL_UserKey');
query ("SELECT * FROM Users WHERE Id=$TOL_UserId AND KeyId=$TOL_UserKey", 'Usr');
$access=($NUM_ROWS != 0);
if ($NUM_ROWS) {
	fetchRow($Usr);
	query ("SELECT * FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'XPerm');

	if ($NUM_ROWS) {
		fetchRow($XPerm);
	} else {
		$access = 0;						//added lately; a non-admin can enter the administration area; he exists but doesn't have ANY rights
	}
	$xpermrows= $NUM_ROWS;
} else {
	query ("SELECT * FROM UserPerm WHERE 1=0", 'XPerm');
}

if ($access) {
	query ("SELECT DeleteImage FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'Perm');
	if ($NUM_ROWS) {
		fetchRow($Perm);
		$access = (getVar($Perm,'DeleteImage') == "Y");
	} else $access = 0;
}

?>
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">

	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE><?php  putGS("Delete image"); ?></TITLE>
<?php  if ($access == 0) { ?>	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/priv/ad.php?ADReason=<?php  print encURL(getGS("You do not have the right to delete images" )); ?>">
<?php  } ?></HEAD>

<?php
if ($access) {
	?>
	<STYLE>
		BODY { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 10pt; }
		SMALL { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 8pt; }
		FORM { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 10pt; }
		TH { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 10pt; }
		TD { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 10pt; }
		BLOCKQUOTE { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 10pt; }
		UL { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 10pt; }
		LI { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 10pt; }
		A  { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 10pt; text-decoration: none; color: darkblue; }
		ADDRESS { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 8pt; }
	</STYLE>

	<BODY  BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">
	<?php
	todefnum('Id');

	$Link = cImgLink();
	?>
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
		<TR>
			<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/priv/img/sign_big.gif" BORDER="0"></TD>
			<TD>
				<DIV STYLE="font-size: 12pt"><B><?php  putGS("Delete image"); ?></B></DIV>
				<HR NOSHADE SIZE="1" COLOR="BLACK">
			</TD>
		</TR>
		<TR><TD ALIGN=RIGHT>
		  <TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
			<TR>
			  <TD><A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Image Archive"); ?>"></A></TD><TD><A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>" ><B><?php  putGS("Image Archive");  ?></B></A></TD>
			  <TD><A HREF="/priv/home.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Home"); ?>"></A></TD><TD><A HREF="/priv/home.php" ><B><?php  putGS("Home");  ?></B></A></TD>
			  <TD><A HREF="/priv/logout.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Logout"); ?>"></A></TD><TD><A HREF="/priv/logout.php" ><B><?php  putGS("Logout");  ?></B></A></TD>
			</TR>
		  </TABLE>
		</TD></TR>
	</TABLE>
	<?php
	query ("SELECT Id, Description FROM Images WHERE Id = $Id", 'q_img');
	if ($NUM_ROWS) {
		fetchRow($q_img);

		$query = "SELECT ai.NrArticle, a.Name, a.IdPublication, a.NrIssue, a.NrSection, a.Number, a.IdLanguage
				  FROM ArticleImages AS ai, Images AS i, Articles AS a
				  WHERE ai.IdImage = i.Id AND ai.NrArticle = a.Number AND i.Id = $Id
				  ORDER BY ai.NrArticle";
		query($query, 'q_art');

		if ($NUM_ROWS) {
			// image is in use //////////////////////////////////////////////////////////////////
			$inUse = '<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" bgcolor="white" width="100%">
						   <tr><td bgcolor="#C0D0FF" colspan="2"><b>'.getGS('Image is still attached to Article(s)').'</b></td></tr>';

			for($loop=0; $loop<$NUM_ROWS; $loop++) {
				fetchRow($q_art);
				$inUse .= '<tr '.trColor().'>
							 <td>'.getHVar($q_art, 'Name').'</td>
							 <td width="10%" align="center"><a href="/priv/pub/issues/sections/articles/edit.php?Pub='.getHVar($q_art, 'IdPublication').'&Issue='.getHVar($q_art, 'NrIssue').'&Section='.getHVar($q_art, 'NrSection').'&Article='.getHVar($q_art, 'Number').'&Language='.getHVar($q_art, 'IdLanguage').'&sLanguage='.getHVar($q_art, 'IdLanguage').'">'.getGS('Edit').'</a></td>
						   </tr>';
			}

			$inUse .= ' </table>';
		}

		if ($inUse) {
		?>
		<P>
		<CENTER>
		  <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" BGCOLOR="#C0D0FF" ALIGN="CENTER">
			<TR>
				<TD COLSPAN="2">
					<B> <?php  putGS("Delete image"); ?> </B>
					<HR NOSHADE SIZE="1" COLOR="BLACK">
				</TD>
			</TR>
			<TR>
				<TD COLSPAN="2"><BLOCKQUOTE><LI><?php  putGS('You cannot delete image $1','<B>'.getHVar($q_img,'Description').'</B>'); ?></LI></BLOCKQUOTE></TD>
			</TR>
			<TR>
				<TD COLSPAN="2">
				<DIV ALIGN="CENTER">
				<INPUT TYPE="button" NAME="Back" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>index.php?<?php echo $Link['SO']; ?>'">
				</DIV>
				</TD>
			</TR>
		  </TABLE>
		</CENTER>
		<P>
		<?php
		echo $inUse;

		} else {
		?>
		<P>
		<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" BGCOLOR="#C0D0FF" ALIGN="CENTER">
			<TR>
				<TD COLSPAN="2">
					<B> <?php  putGS("Delete image"); ?> </B>
					<HR NOSHADE SIZE="1" COLOR="BLACK">
				</TD>
			</TR>
			<TR>
				<TD COLSPAN="2"><BLOCKQUOTE><LI><?php  putGS('Are you sure you want to delete the image $1?','<B>'.getHVar($q_img,'Description').'</B>'); ?></LI></BLOCKQUOTE></TD>
			</TR>
			<TR>
				<TD COLSPAN="2">
				<DIV ALIGN="CENTER">
				<INPUT TYPE="button" NAME="Yes" VALUE="<?php  putGS('Yes'); ?>" ONCLICK="location.href='<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>do_del.php?Id=<?php echo $Id.$Link['SO']; ?>'">
				<INPUT TYPE="button" NAME="No" VALUE="<?php  putGS('No'); ?>" ONCLICK="location.href='<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>index.php?<?php echo $Link['SO']; ?>'">
				</DIV>
				</TD>
			</TR>
		</TABLE></CENTER>
		<P>
		<?php
		}
	} else { ?><BLOCKQUOTE>
		<LI><?php  putGS('No such image.'); ?></LI>
	</BLOCKQUOTE>
	<?php
	}
	?>
	<HR NOSHADE SIZE="1" COLOR="BLACK">
	<a STYLE='font-size:8pt;color:#000000' href='http://www.campware.org' target='campware'>CAMPSITE  2.1.5 &copy 1999-2004 MDLF, maintained and distributed under GNU GPL by CAMPWARE</a>
	</BODY>
<?php
}
?>
</HTML>
