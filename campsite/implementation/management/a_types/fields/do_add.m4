B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE
<?
    todef('cName');
    todef('cType');
    todef('AType');

?>dnl

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageArticleTypes*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Adding new field*>)
	<? if ($access == 0) { ?>
		X_AD(<*You do not have the right to add article type fields.*>)
	<? } ?>
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY
<? todef('cName'); ?>dnl

B_HEADER(<*Adding new field*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Fields*>, <*a_types/fields/?AType=<? print encURL($AType); ?>*>)
X_HBUTTON(<*Article Types*>, <*a_types/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

B_CURRENT
X_CURRENT(<*Article type*>, <*<B><? print encHTML($AType); ?></B>*>)
E_CURRENT

<? $created= 0; ?>dnl
<P>
B_MSGBOX(<*Adding new field*>)
	X_MSGBOX_TEXT(<*
<?
    $j=strlen($cName);
    $correct=1;
    if ($j==0)
	$correct=0;
    for ($i=0;$i<$j;$i++) {
	$c = ord ( strtolower ( substr ( $cName,$i,1 ) ) );
	if ($c<97 || $c>122)
	    $correct=0;
    }
    
    if ($correct) {
	query ("SHOW FIELDS FROM X$AType LIKE 'F$cName'", 'f');
	if ($NUM_ROWS) { ?>dnl
	<LI><? putGS('The field $1 already exists.','<B>'.encHTML($cName).'</B>'); ?></LI>
	<? $correct= 0;
        }
    } else { ?>dnl
	<LI><? putGS('The $1  must not be void and may only contain letters.','<B>'.getGS('Name').'</B>'); ?></LI>
    <? } 

    if ($correct) {
	if ($cType == 1) {
	    query ("ALTER TABLE X$AType ADD COLUMN F$cName VARCHAR(100) NOT NULL");
	    $created= 1;
	} elseif ($cType == 2) {
	    query ("ALTER TABLE X$AType ADD COLUMN F$cName DATE NOT NULL");
	    $created= 1;
	} elseif ($cType == 3) {
	    query ("ALTER TABLE X$AType ADD COLUMN F$cName MEDIUMBLOB NOT NULL");
	    $created= 1;
	} else { ?>dnl
	<LI><? putGS('Invalid field type.'); ?></LI>
	<? $correct= 0;
	}
    }

    if ($created) { ?>dnl
	<LI><? putGS('The field $1 has been created.','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
X_AUDIT(<*71*>, <*getGS('Article type field $1 created', decS($cName))*>)
<? } ?>dnl
	*>)
<? if ($created) { ?>dnl
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/a_types/fields/add.php?AType=<? print encURL($AType); ?>"><IMG SRC="X_ROOT/img/button/add_another.gif" BORDER="0" ALT="Add another field"></A>
		<A HREF="X_ROOT/a_types/fields/?AType=<? print encURL($AType); ?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
<? } else { ?>
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/a_types/fields/add.php?AType=<? print encURL($AType); ?>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
	E_MSGBOX_BUTTONS
<? } ?>dnl
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
