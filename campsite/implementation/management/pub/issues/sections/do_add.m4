B_HTML
INCLUDE_PHP_LIB(<*../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageSection*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Adding new section*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add sections.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<? 
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Language');
?>dnl
B_HEADER(<*Adding new section*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Sections*>, <*pub/issues/sections/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>*>)
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<? p($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
    if ($NUM_ROWS) {
	query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
	if ($NUM_ROWS) {
	    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
	    fetchRow($q_iss);
	    fetchRow($q_pub);
	    fetchRow($q_lang);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><? pgetHVar($q_iss,'Number'); ?>. <? pgetHVar($q_iss,'Name'); ?> (<? pgetHVar($q_lang,'Name'); ?>)</B>*>)
E_CURRENT

<?
    todef('cName');
    todefnum('cNumber');
    todef('cSubs');

    $correct= 1;
    $created= 0;
?>dnl
<P>
B_MSGBOX(<*Adding new section*>)
	X_MSGBOX_TEXT(<*
<?
    if ($cName == "") {
	$correct= 0; ?>dnl
		<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
    <? }
    
    if ($cNumber == "") {
	$correct= 0;
	$cNumber= ($cNumber + 0); ?>dnl
		<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Number').'</B>'); ?></LI>
    <? }
    
    if ($correct) {
	
	query ("INSERT IGNORE INTO Sections SET Name='$cName', IdPublication=$Pub, NrIssue=$Issue, IdLanguage=$Language, Number=$cNumber");
	$created= ($AFFECTED_ROWS > 0);
    }
    
    if ($created) { ?>dnl
		<LI><? putGS('The section $1 has been successfuly added.','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
	<?	if ($cSubs != "") {
			$add_subs_res = add_subs_section($Pub, $cNumber);
			if ($add_subs_res == -1) { ?>
				<LI><? putGS('Error updating subscriptions.'); ?></LI>
		<?	} else { ?>
				<LI><? putGS('A total of $1 subscriptions were updated.','<B>'.encHTML(decS($add_subs_res)).'</B>'); ?></LI>
	<?		}
		}
	?>
X_AUDIT(<*21*>, <*getGS('Section $1 added to issue $2. $3 ($4) of $5',$cName,getHVar($q_iss,'Number'),getHVar($q_iss,'Name'),getHVar($q_lang,'Name'),getHVar($q_pub,'Name'))*>)
<? } else {
    
    if ($correct != 0) { ?>dnl
		<LI><? putGS('The section could not be added.'); ?></LI><LI><? putGS('Please check if another section with the same number does not already exist.'); ?></LI>
<? }
}
?>dnl
		*>)
	B_MSGBOX_BUTTONS
<? if ($correct && $created) { ?>dnl
		REDIRECT(<*Add another*>, <*Add another*>, <*X_ROOT/pub/issues/sections/add.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>*>)
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/pub/issues/sections/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>*>)
<? } else { ?>
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/pub/issues/sections/add.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>*>)
<? } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
