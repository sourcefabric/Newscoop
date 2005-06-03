B_HTML
INCLUDE_PHP_LIB(<*..*>)dnl
B_DATABASE

<?php
    todefnum('Issue');
    todefnum('Pub');
    todefnum('What');
    todefnum('Language');
    todef('REQUEST_URI', $_SERVER['REQUEST_URI']);
?>dnl
CHECK_BASIC_ACCESS
<?php  if ($What != 0) { ?>dnl
CHECK_ACCESS(<*ManageTempl*>)dnl
<?php  } ?>dnl

B_HEAD
 X_EXPIRES
 <?php
     if ($What) { ?>
  X_TITLE(<*Select template*>)
     <?php  } else { ?>
  X_TITLE(<*Templates management*>)
 <?php  } ?>
<?php
    if ($access == 0) {
 if ($What) { ?>dnl
 X_AD(<*You do not have the right to change default templates.*>)
<?php  } else { ?>dnl
 X_LOGOUT
<?php  }
    }
?>dnl
E_HEAD

<?php

$dotpos=strrpos($REQUEST_URI,"?");
$dotpos = $dotpos ? $dotpos: strlen($REQUEST_URI);
$myurl=substr ($REQUEST_URI,0,$dotpos);
$myurl1=substr ($REQUEST_URI,$dotpos+1);

foreach (split("/", $myurl) as $index=>$dir) {
	if ($dir == "..") {
		$myurl = "/look/";
		break;
	}
}

if (strncmp($myurl, "/look/", 6) != 0) {
    $access = FALSE;
?>
X_XAD(<*You do no have access to the $1 directory!*>, <**>, <*$myurl*>)
<?php
}
if ($access) {

SET_ACCESS(<*mta*>, <*ManageTempl*>)
SET_ACCESS(<*dta*>, <*DeleteTempl*>)
?>dnl
B_STYLE
E_STYLE

B_BODY

<?php
    if ($What) { ?>
 B_HEADER(<*Select template*>)
    <?php  } else { ?>
 B_HEADER(<*Templates*>)
    <?php  } ?>
B_HEADER_BUTTONS
<?php  if ($What) { ?>dnl
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<?php  pencURL($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
<?php  } ?>dnl
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?php
    $NUM_ROWS=0;
    if ($What)
 query ("SELECT Name, FrontPage, SingleArticle FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
    if ($NUM_ROWS != 0 || $What == 0) {
 if ($What)
     query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
 if (($NUM_ROWS != 0) || ($What == 0)) {
   ?>dnl
B_CURRENT
<?php  if ($What) { ?>dnl
X_CURRENT(<*Publication*>, <*<B><?php  fetchRow($q_pub); pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><?php  pencURL($Issue); ?>. <?php  fetchRow($q_iss); pgetHVar($q_iss,'Name'); ?> (<?php
    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_language');
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) {
 fetchRowNum($q_language);
 pencHTML( getNumVar($q_language,0));
    }
?>)</B>*>)
<?php  } ?>dnl


X_CURRENT(<*Path*>, <*<B><?php  pencHTML(decURL($myurl)); ?></B>*>)
E_CURRENT
<P>
<TABLE BORDER="0" CELLSPACING="2" CELLPADDING="0">
<TR>


<?php
    if ($myurl != "LOOK_PATH/") {
 if ($What) { ?>dnl
<TD>X_NEW_BUTTON(<*Go up*>, <*../?What=<?php  pencURL($What); ?>&Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pencURL($Issue); ?>&Language=<?php  pencURL($Language); ?>*>)</TD>
<?php  } else { ?>dnl
<TD>X_NEW_BUTTON(<*Go up*>, <*..*>)</TD>
<?php  }
}

 if ($What == 0) {
  if ($mta != 0) { ?>
   <TD>X_NEW_BUTTON(<*Create new folder*>, <*X_ROOT/templates/new_dir.php?Path=<?php  pencURL($myurl); ?>*>)</TD>
   <TD>X_NEW_BUTTON(<*Upload template*>, <*X_ROOT/templates/upload_templ.php?Path=<?php  pencURL($myurl); ?>*>)</TD>
   <TD>X_NEW_BUTTON(<*Create new template*>, <*X_ROOT/templates/new_template.php?Path=<?php  pencURL($myurl); ?>*>)</TD>
  <?php  }
 } else {?>dnl
<TD>
<?php  if ($What == 1) { ?>dnl
 <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
 <TR>
  <TD><IMG SRC="X_ROOT/img/tol.gif" BORDER="0"></TD>
  <TD><?php  putGS('Select the template for displaying the front page.'); ?></TD>
 </TR>
 </TABLE>
<?php  } else { ?>dnl
 <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
 <TR>
  <TD><IMG SRC="X_ROOT/img/tol.gif" BORDER="0"></TD>
  <TD><?php  putGS('Select the template for displaying a single article.'); ?></TD>
 </TR>
 </TABLE>
<?php  } ?>dnl
 </TD>
<?php  } ?>dnl
</TABLE>
<P>
<?php
	// 'What' at this level selects the usage of templates:
	// 0 - you are in the templates management module (create, delete, edit, upload, duplicate etc)
	// 1, 2 - select a template for viewing with it the font page (1) and an independent article (2)

	$listbasedir=substr ($myurl, 6);
	if ($What) {
		$params=$myurl1;
		include ('./stempl_dir.php');
	} else {
		include ('./list_dir.php');
	}

} else {
?>dnl
<BLOCKQUOTE>
 <LI><?php  putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<?php  }
} else { ?>dnl
<BLOCKQUOTE>
 <LI><?php  putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML


