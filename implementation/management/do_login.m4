B_HTML
INCLUDE_PHP_LIB(<*.*>)
B_DATABASE

<?php
    $ok=0;
    todef ('UserName');
    todef ('UserPassword');
    todef ('selectlanguage');
    query ( "SELECT Id FROM Users WHERE UName='$UserName' AND Password=PASSWORD('$UserPassword') AND Reader='N'", 'q' );
    if ($NUM_ROWS) {
 fetchRow ($q);
 query ( "UPDATE Users SET KeyId=RAND()*1000000000+RAND()*1000000+RAND()*1000 WHERE Id=".getVar($q,'Id'));
 $ok=$AFFECTED_ROWS;
 if ($ok) {
     query ( "SELECT Id, KeyId FROM Users WHERE Id=".getVar($q,'Id'), 'usrs');
 }
    }
?>

B_HEAD
 X_EXPIRES
<?php  if ($ok==0) { ?>dnl
 X_TITLE(<*Login failed*>)
<?php  } else {
    fetchRow($usrs);?>dnl
 X_TITLE(<*Login*>)
 X_COOKIE(<*TOL_UserId=<?php  print getVar ($usrs,'Id'); ?>*>)
 X_COOKIE(<*TOL_UserKey=<?php  print getVar ($usrs,'KeyId'); ?>*>)
 <?php
 if (function_exists ("incModFile"))
   incModFile ();

     if (!isset($selectlanguage))<*
  $selectlanguage='en';
     *>
 ?>
 X_COOKIE(<*TOL_Language=<?php  p($selectlanguage); ?>*>)
 X_REFRESH(<*0; URL=X_ROOT/index.php*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($ok==0) { ?>dnl
B_STYLE
E_STYLE

B_BODY
B_HEADER(<*Login failed*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Login*>, <*login.php*>)
E_HEADER_BUTTONS
E_HEADER

<BLOCKQUOTE>
 <LI><?php  putGS('Login failed'); ?></LI>
 <LI><?php  putGS('Pease make sure that you typed the correct user name and password.'); ?></LI>
 <LI><?php  putGS('If your problem persists please contact the site administrator $1','<A HREF="mailto:'.encURL($SERVER_ADMIN).'">'.encHTML($SERVER_ADMIN) );?></A></LI>
</BLOCKQUOTE>

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

