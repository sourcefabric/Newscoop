INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub/issues*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageIssue*>)

B_HEAD
    X_TITLE(<*Changing issue template*>)
<?php  if ($access == 0) { ?>dnl
    X_AD(<*You do not have the right to change issue templates.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY
<?php
    todefnum('What');
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Language');
    todef('Path'); 

    query ("SELECT Name FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
    if ($NUM_ROWS) {
    query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
    if ($NUM_ROWS) {
?>dnl
B_HEADER(<*Changing issue template*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<?php  pencURL($Pub); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Publications*>, <*pub/*>)
E_HEADER_BUTTONS
E_HEADER

<?php
    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_languages');
    $nr=$NUM_ROWS;
    fetchRow($q_pub);
    fetchRow($q_iss);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<?php  pgetHVar($q_pub,'Name'); ?>*>)
X_CURRENT(<*Issue*>, <*<?php  pgetHVar($q_iss,'Name'); ?> (<?php
    for($loop=0;$loop<$nr;$loop++) {
    fetchRow($q_languages);
    pgetHVar($q_languages,'Name');
    }
    ?>)*>)
E_CURRENT

<P>
<?php
    $AFFECTED_ROWS= 0;
    if ($What == 1) { ?>dnl
B_MSGBOX(<*Changing issue template for front page*>)
<?php
    query ("UPDATE Issues SET FrontPage='$Path' WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language");
} else { ?>dnl
B_MSGBOX(<*Changing issue template for single article*>)
<?php
    query ("UPDATE Issues SET SingleArticle='$Path' WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language");
    }
    ?>dnl
    X_MSGBOX_TEXT(<*
<?php  if ($AFFECTED_ROWS > 0) { ?>dnl
    <LI><?php  putGS('The template has been successfully changed.'); ?></LI>
<?php  if ($What == 1) { ?>dnl
X_AUDIT(<*13*>, <*getGS('Issue template for publication $1 changed to $2',getVar($q_pub,'Name'),$Path)*>)
<?php  } else { ?>dnl
X_AUDIT(<*36*>, <*getGS('Issue template for single articles from $1 changed to $2',getVar($q_pub,'Name'),$Path)*>)
<?php  }
} else { ?>dnl
    <LI><?php  putGS('The template could not be changed.'); ?></LI>
<?php  } ?>dnl
    *>)
    B_DIALOG_BUTTONS
        REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/pub/issues/?Pub=<?php  pencURL($Pub); ?>*>)
    E_DIALOG_BUTTONS
E_MSGBOX
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
    <LI><?php  putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
    <LI><?php  putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
