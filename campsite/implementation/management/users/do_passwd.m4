B_HTML
INCLUDE_PHP_LIB(<*$ADMIN_DIR/users*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUsers*>)

B_HEAD
    X_EXPIRES
    X_TITLE(<*Changing user password*>)
<?php  if ($access == 0) { ?>dnl
    X_AD(<*You do not have the right to change user passwords.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Changing user password*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Users*>, <*users/*>)
E_HEADER_BUTTONS
E_HEADER

<?php
    todefnum('User');
    query ("SELECT UName FROM Users WHERE Id=$User", 'users');
    if ($NUM_ROWS) {
    fetchRow($users);
    ?>dnl

B_CURRENT
X_CURRENT(<*User account*>, <*<?php  pgetHVar($users,'UName'); ?>*>)
E_CURRENT

<?php  $ok= 1; ?>dnl
<P>
B_MSGBOX(<*Changing user password*>)
    X_MSGBOX_TEXT(<*
<?php
    todef ('cPass1');
    todef ('cPass2');
    if ($cPass1!=$cPass2 || strlen(decS($cPass1))<6) { ?>dnl
    <LI><?php  putGS('The password must be at least 6 characters long and both passwords should match.'); ?></LI>
<?php
    $ok= 0;
    }

    if ($ok) {
    query ("UPDATE Users SET Password=password('$cPass1') WHERE Id=$User");
    if ($AFFECTED_ROWS <= 0) { ?>dnl
    <LI><?php  putGS('The password could not be changed.'); ?></LI>
X_AUDIT(<*54*>, <*getGS('Password changed for $1',getHVar($users,'UName'))*>)
<?php
    $ok= 0;
    } else { ?>dnl
    <LI><?php  putGS('The password has been changed.'); ?></LI>
<?php  }
    }
     ?>dnl
    *>)
    B_MSGBOX_BUTTONS
<?php  if ($ok) { ?>dnl
        REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/users/*>)
<?php  } else { ?>dnl
        REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/users/*>)
<?php  } ?>dnl
    E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<?php  } else { ?>dnl
<BLOCKQUOTE>
    <LI><?php  putGS('No such user account.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
