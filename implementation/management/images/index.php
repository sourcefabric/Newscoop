<?php  include ("../lib_campsite.php");
	$globalfile=selectLanguageFile('..','globals');
	$localfile=selectLanguageFile('.','locals');
    @include ($globalfile);
	@include ($localfile);
	include ("../languages.php");   ?>
<?php  require_once("$DOCUMENT_ROOT/db_connect.php"); ?>


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

<HEAD>
	<META http-equiv="Content-Type" content="text/html; charset=UTF-8">

	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE><?php  putGS("Images"); ?></TITLE>
<?php  if ($access == 0) { ?>	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/priv/logout.php">
<?php  }
	query ("SELECT * FROM Images WHERE 1=0", 'q_img');
?></HEAD>

<?php  if ($access) {

   if (getVar($XPerm,'AddImage') == "Y")
	$aia=1;
	else
	$aia=0;


   if (getVar($XPerm,'ChangeImage') == "Y")
	$cia=1;
	else
	$cia=0;


   if (getVar($XPerm,'DeleteImage') == "Y")
	$dia=1;
	else
	$dia=0;

?><STYLE>
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

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
	<TR>
		<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/priv/img/sign_big.gif" BORDER="0"></TD>
		<TD>
			<DIV STYLE="font-size: 12pt"><B><?php  putGS("Images"); ?></B></DIV>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR><TD ALIGN=RIGHT>
	  <TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
		<TR>
			<TD><A HREF="/priv/home.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Home"); ?>"></A></TD><TD><A HREF="/priv/home.php" ><B><?php  putGS("Home");  ?></B></A></TD>
			<TD><A HREF="/priv/logout.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Logout"); ?>"></A></TD><TD><A HREF="/priv/logout.php" ><B><?php  putGS("Logout");  ?></B></A></TD>
		</TR>
	</TABLE>
  </TD></TR>
</TABLE>

<table>
<?php  if ($aia != 0) {


	// create the links ///////////////////////////////////////////////////
	require_once('image_functions.inc.php');
	$Link   = cImgLink();

	// regarding parameters from search form or link //////////////////////
	todef('S');
	todef('de');
	todef('ph');
	todef('pl');
	todef('da');

	if ($S && (!empty($de) || !empty($ph) || !empty($pl) || !empty($da))) {
		$color=0;

		if (!empty($de)) {
			$WhereAdd .= " AND i.Description LIKE '%$de%'";
			$SearchC  .= "<tr ".trColor()."><td><b>".getGS('Description').":</b></td><td>$de</td></tr>";
		}
		if (!empty($ph)) {
			$WhereAdd .= " AND i.Photographer LIKE '%$ph%'";
			$SearchC  .= "<tr ".trColor()."><td><b>".getGS('Photographer').":</b></td><td>$ph</td></tr>";
		}
		if (!empty($pl)) {
			$WhereAdd .= " AND i.Place LIKE '%$pl%'";
			$SearchC  .= "<tr ".trColor()."><td><b>".getGS('Place').":</b></td><td>$pl</td></tr>";
		}
		if (!empty($da)) {
			$WhereAdd .= " AND i.Date LIKE '%$da%'";
			$SearchC  .= "<tr ".trColor()."><td><b>".getGS('Date').":</b></td><td>$da</td></tr>";
		}

		$SearchReset = '<td><a href="/priv/images/"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="'.getGS("Reset search conditions").'"></a></td><td><a href="/priv/images/"><b>'.getGS('Reset search conditions').'</b></a></td>';
		$SearchCHTML = '
			<table><tr><td>
				<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
				  <tr BGCOLOR="#C0D0FF"><td colspan="2"><b>'.getGS('Search conditions').'</b></td></tr>
				  '.$SearchC.'
				</table>
			</tr></td></table>';

	}
	///////////////////////////////////////////////////////////////////////

	// build the order statement //////////////////////////////////////////
	todef('O');
	todef('D');

	if ($D == 'DESC') {
		$OrderDir = "DESC";
		$HrefDir  = "ASC";
	} else {
		$OrderDir = "ASC";
		$HrefDir  = "DESC";
	}

	switch ($O) {
	case 'de':
		$Order  .= 'ORDER BY i.Description '.$OrderDir;
		break;

	case 'ph':
		$Order  .= 'ORDER BY i.Photographer '.$OrderDir;
		break;

	case 'pl':
		$Order  .= 'ORDER BY i.Place '.$OrderDir;
		break;

	case 'da':
		$Order  .= 'ORDER BY i.Date '.$OrderDir;
		break;

	case 'id':
	default:
		$Order  .= 'ORDER BY i.Id '.$OrderDir;
		break;
	}
	///////////////////////////////////////////////////////////////////////

	// build the links for ordering (search results) //////////////////////
	$IdHref = '?O=id&D='.$HrefDir.$Link['S'];
	$DeHref = '?O=de&D='.$HrefDir.$Link['S'];
	$PhHref = '?O=ph&D='.$HrefDir.$Link['S'];
	$PlHref = '?O=pl&D='.$HrefDir.$Link['S'];
	$DaHref = '?O=da&D='.$HrefDir.$Link['S'];
	///////////////////////////////////////////////////////////////////////

	?>
<tr>
  <td><A HREF="add.php"><IMG SRC="/priv/img/tol.gif" BORDER="0" alt="<?php  putGS("Add new image"); ?>"></A></TD><TD><A HREF="add.php"><B><?php  putGS("Add new image"); ?></B></A></TD>
  <td><A HREF="searchform.php?<?php echo $Link['SO']; ?>"><IMG SRC="/priv/img/tol.gif" BORDER="0" alt="<?php  putGS("Search for images"); ?>"></A></TD><TD><A HREF="searchform.php?<?php echo $Link['SO']; ?>" ><B><?php  putGS("Search for images"); ?></B></A></TD>
  <?php echo $SearchReset; ?>
</tr>
<?php  } ?>
</table>
<?php echo $SearchCHTML; ?>
<P><?php

	query ("SELECT * FROM Images AS i WHERE 1 $WhereAdd $Order LIMIT $ImgOffs, ".($lpp+1), 'q_img');
	if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i=$lpp;
	$color= 0;
	?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" WIDTH="100%">
	<TR BGCOLOR="#C0D0FF">
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><a href="<?php echo $IdHref; ?>"><?php  putGS("Nr"); ?></a></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><a href="<?php echo $DeHref; ?>"><?php  putGS("Click to view image"); ?></a></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><a href="<?php echo $PhHref; ?>"><?php  putGS("Photographer"); ?></a></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><a href="<?php echo $PlHref; ?>"><?php  putGS("Place"); ?></a></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><a href="<?php echo $DaHref; ?>"><?php  putGS("Date<BR><SMALL>(yyyy-mm-dd)</SMALL>"); ?></a></B></TD>
	<?php  if ($cia != 0) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Info"); ?></B></TD>
	<?php  }
	    
	    if ($dia != 0) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Delete"); ?></B></TD>
	<?php  } ?>
	</TR>
<?php 
	for($loop=0; $loop<$nr; $loop++) {
	fetchRow($q_img);
	if ($i) { ?>	<TR <?php  if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php  } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php  } ?>>
		<TD ALIGN="RIGHT">
			<?php  pgetHVar($q_img,'Id'); ?>
		</TD>
		<TD >
			<A HREF="/priv/images/view.php?Id=<?php  pgetUVar($q_img,'Id'); echo $Link['SO']?>"><?php  pgetHVar($q_img,'Description'); ?></A>
		</TD>
		<TD >
			<?php
			// photographer search link //
			$PhLink = '?S=1&ph='.getUVar($q_img,'Photographer');
			echo "<a href='$PhLink'>".orE(getHVar($q_img,'Photographer'))."</a>";
			?>&nbsp;
		</TD>
		<TD >
			<?php
			// place search link //
			$PlLink = '?S=1&pl='.getUVar($q_img,'Place');
			echo "<a href='$PlLink'>".orE(getHVar($q_img,'Place'))."</a>";
			?>&nbsp;
		</TD>
		<TD >
			<?php
			// date search link //
			$DaLink = '?S=1&da='.getUVar($q_img,'Date');
			echo "<a href='$DaLink'>".orE(getHVar($q_img,'Date'))."</a>";
			?>&nbsp;
		</TD>
	<?php  if ($cia != 0) { ?>
		<TD ALIGN="CENTER">
			<A HREF="/priv/images/edit.php?Id=<?php pgetVar($q_img, 'Id'); echo $Link['SO']; ?>"><?php  putGS("Change");?></A>
		</TD>
	<?php  }
	    if ($dia != 0) { ?>
		<TD ALIGN="CENTER">
			<A HREF="/priv/images/del.php?Id=<?php pgetVar($q_img, 'Id'); echo $Link['SO']; ?>"><IMG SRC="/priv/img/icon/x.gif" BORDER="0" ALT="<?php  putGS('Delete image $1',getHVar($q_img,'Description')); ?>"></A>
		</TD>
	<?php  } ?>
	</TR>
<?php 
	$i--;
    }
}

?>	<TR><TD COLSPAN="2" NOWRAP>
<?php  if ($ImgOffs <= 0) { ?>		&lt;&lt; <?php  putGS('Previous'); ?>
<?php  } else { ?>		<B><A HREF="index.php?<?php echo $Link['P']; ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
<?php  } ?><?php  if ($nr < $lpp+1) { ?>		 | <?php  putGS('Next'); ?> &gt;&gt;
<?php  } else { ?>		 | <B><A HREF="index.php?<?php echo $Link['N']; ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
<?php  } ?>	</TD></TR>
</TABLE>
<?php  } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('No images.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>

<HR NOSHADE SIZE="1" COLOR="BLACK">
<a STYLE='font-size:8pt;color:#000000' href='http://www.campware.org' target='campware'>CAMPSITE  2.1.5 &copy 1999-2004 MDLF, maintained and distributed under GNU GPL by CAMPWARE</a>
</BODY>
<?php  } ?>

</HTML>

