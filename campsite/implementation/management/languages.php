<?

function registerLanguage($name,$code,$charset){

	global $languages;
	$languages["$code"]=array("name"=>$name,"charset"=>$charset);
}
registerLanguage('English','en','ISO_8859-1');
registerLanguage('Serbo-Croatian','sh','ISO_8859-2');
registerLanguage('Chinese','zh','UTF-8');
registerLanguage('Croatian','hr','ISO_8859-2');
registerLanguage('Czech','cz','ISO_8859-2');
registerLanguage('German','de','ISO_8859-1');
registerLanguage('Portuguese','pt','ISO_8859-1');
registerLanguage('Romanian','ro','ISO_8859-2');
registerLanguage('Russian','ru','ISO_8859-5');
registerLanguage('Serbian (Cyrillic)','sr','ISO_8859-5');
registerLanguage('Spanish','es','ISO_8859-1');

?>
