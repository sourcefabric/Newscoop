INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManagePub*>)

B_HEAD
    X_TITLE(<*Changing default subscription time*>)
<?php  if ($access == 0) { ?>dnl
    X_AD(<*You do not have the right to change publication information.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php
    todefnum('Pub');
    todefnum('cPub');
    todef('cCountryCode');
    todefnum('Language', 1);
    todefnum('cPaidTime');
    todefnum('cTrialTime');
?>dnl
B_HEADER(<*Changing default subscription time*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Subscriptions*>, <*pub/deftime.php?Pub=<?php  pencURL($Pub); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Publications*>, <*pub/*>)
E_HEADER_BUTTONS
E_HEADER

<?php
    $created= 0;

    query ("SELECT * FROM Publications WHERE Id=$cPub", 'q_pub');

    if ($NUM_ROWS) {
    query ("SELECT * FROM Countries WHERE Code='$cCountryCode'", 'q_ctr');

    if ($NUM_ROWS) {
        fetchRow($q_pub);
        fetchRow($q_ctr);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<?php  pgetHVar($q_pub,'Name'); ?>*>)
X_CURRENT(<*Country*>, <*<?php  pgetHVar($q_ctr,'Name'); ?>*>)
E_CURRENT

<P>
B_MSGBOX(<*Changing default subscription time*>)
    X_MSGBOX_TEXT(<*
<?php
    query ("UPDATE SubsDefTime SET TrialTime='$cTrialTime', PaidTime='$cPaidTime' WHERE CountryCode='$cCountryCode' AND IdPublication=$cPub");
    $created= ($AFFECTED_ROWS > 0);

    if ($created) { ?>dnl
        <LI><?php  putGS('The default subscription time for $1 has been successfuly updated.','<B>'.getHVar($q_pub,'Name').':'.getHVar($q_ctr,'Name').'</B>'); ?></LI>
X_AUDIT(<*6*>, <*getGS('Default subscription time for $1 changed',getVar($q_pub,'Name').':'.$cCountryCode)*>)
<?php  } else { ?>dnl
        <LI><?php  putGS('The default subscription time could not be updated.'); ?></LI>
<?php  } ?>dnl
        *>)
    B_MSGBOX_BUTTONS
<?php  if ($created) { ?>dnl
        REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/pub/deftime.php?Pub=<?php  pencURL($Pub); ?>&Language=<?php  pencURL($Language); ?>*>)
<?php  } else { ?>
        REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/pub/deftime.php?Pub=<?php  pencURL($Pub); ?>&Language=<?php  pencURL($Language); ?>*>)
<?php  } ?>dnl
    E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
    <LI><?php  putGS('No such country.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
    <LI><?php  putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
