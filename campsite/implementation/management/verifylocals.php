<?php 

include("./lib_campsite.php");

function verify($s){
    print "<B>$s</b><BR>";
    include($s);
    print '<HR>';
}

verify("./globals.en.php");
verify("./locals.en.php");
verify("a_types/locals.en.php");				
verify("a_types/fields/locals.en.php");			
verify("infotype/locals.en.php");	
verify("country/locals.en.php");
verify("glossary/locals.en.php");				
verify("glossary/keyword/locals.en.php");		
verify("languages/locals.en.php");		
verify("logs/locals.en.php");				
verify("popup/locals.en.php");	
verify("pub/locals.en.php");				
verify("pub/issues/locals.en.php");				
verify("pub/issues/sections/locals.en.php");		
verify("pub/issues/sections/articles/locals.en.php");	
verify("pub/issues/sections/articles/images/locals.en.php");
verify("templates/locals.en.php");				
verify("users/locals.en.php");			
verify("users/subscriptions/locals.en.php");		
verify("users/subscriptions/sections/locals.en.php");		
verify("u_types/locals.en.php");			
verify("topics/locals.en.php");			


?>