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

todefnum('TOL_UserId');
todefnum('TOL_UserKey');
query("SELECT * FROM Users WHERE Id=$TOL_UserId AND KeyId=$TOL_UserKey", 'Usr');
$access = ($NUM_ROWS != 0);

if ($NUM_ROWS) {
    fetchRow($Usr);
    query ("SELECT * FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'XPerm');

	if ($NUM_ROWS) {
        fetchRow($XPerm);
    } else {
        $access = 0;						//added lately; a non-admin can enter the administration area; he exists but doesn't have ANY rights
    }

	$xpermrows = $NUM_ROWS;
} else {
	query ("SELECT * FROM UserPerm WHERE 1=0", 'XPerm');
}

?>
<HEAD>
	<META http-equiv="Content-Type" content="text/html; charset=UTF-8">

	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE><?php  putGS("Images"); ?></TITLE><?php

if ($access == 0) {
    ?><META HTTP-EQUIV="Refresh" CONTENT="0; URL=/priv/logout.php"><?php
}

query ("SELECT * FROM Images WHERE 1=0", 'q_img');

?></HEAD><?php  #

if ($access) {

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

    <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
    	<TR>
    		<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/priv/img/sign_big.gif" BORDER="0"></TD>
    		<TD>
    			<DIV STYLE="font-size: 12pt"><B><?php  putGS("Image Archive"); ?></B></DIV>
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

	<?php
    if ($aia != 0) {
    	// create the links ///////////////////////////////////////////////////
    	$Link   = cImgLink();

    	// regarding parameters from search form or link //////////////////////
    	todef('S');
    	todef('de');
    	todef('ph');
    	todef('da');
    	todef('use');

    	if ($S && ($de || $ph || $da || $use)) {
    		$color=0;

    		if ($de) {
    			$WhereAdd .= " AND i.Description LIKE '%$de%'";
    			$SearchC  .= "<tr ".trColor()."><td><b>".getGS('Description').":</b></td><td>$de</td></tr>";
    		}
    		if ($ph) {
    			$WhereAdd .= " AND i.Photographer LIKE '%$ph%'";
    			$SearchC  .= "<tr ".trColor()."><td><b>".getGS('Photographer').":</b></td><td>$ph</td></tr>";
    		}
    		if ($da) {
    			$WhereAdd .= " AND i.Date LIKE '%$da%'";
    			$SearchC  .= "<tr ".trColor()."><td><b>".getGS('Date').":</b></td><td>$da</td></tr>";
    		}
    		if ($use) {
    			if ($use) {
                    $not = "NOT";
                }
                $WhereAdd .= " AND a.IdImage IS $not NULL";
    			$SearchC  .= "<tr ".trColor()."><td><b>".getGS('In use').":</b></td><td>$pl</td></tr>";
    		}
    		$SearchReset = '<td><a href="'._DIR_.'index.php?v='.$v.'"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="'.getGS("Reset search conditions").'"></a></td><td><a href="'._DIR_.'index.php?v='.$v.'"><b>'.getGS('Reset search conditions').'</b></a></td>';
    		$SearchCHTML = '
    			<table><tr><td>
    				<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
    				  <tr BGCOLOR="#C0D0FF"><td colspan="2"><b>'.getGS('Search conditions').'</b></td></tr>
    				  '.$SearchC.'
    				</table>
    			</tr></td></table>';

    	}
	    ///////////////////////////////////////////////////////////////////////

    	todef('O');      // Order Id|Description|Photograper|Date|InUse
    	todef('D');      // Direction for ordering
        todef('v');      // View Tumbs|Galery|Flat

    	// build the links for ordering (search results) //////////////////////
    	$IdHref  = _DIR_.'?O=id'.$Link['S'].'&id=0';
    	$DeHref  = _DIR_.'?O=de'.$Link['S'].'&de=0';
    	$PhHref  = _DIR_.'?O=ph'.$Link['S'].'&ph=0';
    	$DaHref  = _DIR_.'?O=da'.$Link['S'].'&da=0';
    	$UseHref = _DIR_.'?O=use'.$Link['S'].'&use=0';
    	///////////////////////////////////////////////////////////////////////

    	if ($D == 'ASC') {
    		$OrderDir = "ASC";
    		$HrefDir  = "DESC";
    		$OrderSign = '<img src="/priv/img/icon/up.gif" border="0">';
    	} else {
    		$OrderDir  = "DESC";
    		$HrefDir   = "ASC";
    		$OrderSign = '<img src="/priv/img/icon/down.gif" border="0">';
    	}

    	switch ($O) {
    	case 'de':
    		$Order  .= 'ORDER BY i.Description '.$OrderDir;
    		$DeO     = $OrderSign;
    		$DeHref = _DIR_.'?O=de&D='.$HrefDir.$Link['S'].'&de=0';
    		break;

    	case 'ph':
    		$Order  .= 'ORDER BY i.Photographer '.$OrderDir;
    		$PhO     = $OrderSign;
    		$PhHref = _DIR_.'?O=ph&D='.$HrefDir.$Link['S'].'&ph=0';
    		break;

    	case 'da':
    		$Order  .= 'ORDER BY i.Date '.$OrderDir;
    		$DaO     = $OrderSign;
    		$DaHref = _DIR_.'?O=da&D='.$HrefDir.$Link['S'].'&da=0';
    		break;

    	case 'use':
    		$Order  .= 'ORDER BY inUse '.$OrderDir;
    		$UseO     = $OrderSign;
    		$UseHref = _DIR_.'?O=use&D='.$HrefDir.$Link['S'].'&use=0';
    		break;

    	case 'id':
    	default:
    		$Order  .= 'ORDER BY i.Id '.$OrderDir;
    		$IdO     = $OrderSign;
    		$IdHref = _DIR_.'?O=id&D='.$HrefDir.$Link['S'].'&id=0';
    		break;
    	}
    	///////////////////////////////////////////////////////////////////////

    	?>
        <table>
          <tr>
            <td><A HREF="<?php echo _DIR_; ?>add.php?v=<?php echo $v; ?>"><IMG SRC="/priv/img/tol.gif" BORDER="0" alt="<?php  putGS("Add new image"); ?>"></A></TD><TD><A HREF="<?php echo _DIR_; ?>add.php?v=<?php echo $v; ?>"><B><?php  putGS("Add new image"); ?></B></A></TD>
            <td><A HREF="<?php echo _DIR_; ?>searchform.php?<?php echo $Link['SO']; ?>"><IMG SRC="/priv/img/tol.gif" BORDER="0" alt="<?php  putGS("Search for images"); ?>"></A></TD><TD><A HREF="<?php echo _DIR_; ?>searchform.php?<?php echo $Link['SO']; ?>" ><B><?php  putGS("Search for images"); ?></B></A></TD>
            <?php echo $SearchReset; ?>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td><B><?php putGS('View', 'View'); ?>:</b></td>
            <td><A HREF="<?php echo _DIR_; ?>index.php?<?php echo $Link['SO']; ?>&v=t"><IMG SRC="/priv/img/tol.gif" BORDER="0" alt="<?php  putGS("Thumbnail"); ?>"></A></TD><TD><A HREF="index.php?<?php echo $Link['SO']; ?>&v=t"><B><?php  putGS("Thumbnail"); ?></B></A></TD>
            <td><A HREF="<?php echo _DIR_; ?>index.php?<?php echo $Link['SO']; ?>&v=g"><IMG SRC="/priv/img/tol.gif" BORDER="0" alt="<?php  putGS("Galery"); ?>"></A></TD><TD><A HREF="index.php?<?php echo $Link['SO']; ?>&v=g"><B><?php  putGS("Galery"); ?></B></A></TD>
            <td><A HREF="<?php echo _DIR_; ?>index.php?<?php echo $Link['SO']; ?>&v=f"><IMG SRC="/priv/img/tol.gif" BORDER="0" alt="<?php  putGS("Text only"); ?>"></A></TD><TD><A HREF="index.php?<?php echo $Link['SO']; ?>&v=f"><B><?php  putGS("Text only"); ?></B></A></TD>
          </tr>
        <?php  } ?>
        </table>
        <?php echo $SearchCHTML; ?>
        <P>
        <?php

    	$baseq = "SELECT  i.Id, i.Description, i.Photographer, i.Date, COUNT(a.IdImage) AS inUse
    			  FROM Images AS i
    			  LEFT JOIN ArticleImages AS a On i.Id=a.IdImage
    			  WHERE 1 $WhereAdd
    			  GROUP BY i.Id";
    	$fullq = $baseq." $Order LIMIT $ImgOffs, ".($lpp+1);
    	#echo $query;
    	query ($fullq, 'q_img');
    	if ($NUM_ROWS) {
    	$nr = $NUM_ROWS;
    	$i = $lpp;
    	$color = 0;

        todef('v');

        switch ($v) {
        case 'f':
              include('v_flat.inc.php');
        	break;
        case 'g':
        	include('v_galery.inc.php');
        	break;
        case 't':
        default:
        	include('v_tumb.inc.php');
            break;
        }

    } else {
        ?><BLOCKQUOTE><LI><?php  putGS('No images.'); ?></LI></BLOCKQUOTE>
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

