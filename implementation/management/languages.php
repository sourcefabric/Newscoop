<?

function registerLanguage($name,$code,$charset){

	global $languages;
	$languages["$code"]=array("name"=>$name,"charset"=>$charset);
}

registerLanguage('English','en','ISO-8859-1');
registerLanguage('Russian','ru','ISO-8859-5');
registerLanguage('German','de','ISO-8859-1');
registerLanguage('Czech','cz','ISO-8859-2');
registerLanguage('Romanian','ro','ISO-8859-2');
registerLanguage('Croatian','hr','ISO-8859-2');

?>
