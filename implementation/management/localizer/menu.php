<?php
require_once($_SERVER[DOCUMENT_ROOT].'/db_connect.php');

function writeFile($newfile,$fen){
    global $for,$en;
    $s="<?php \n";
    while( list($key,$value) = each($en) ){
 if (isset($for["$key"])){
     $newval=$for["$key"];
 }
 else{
     $newval='';
 }
 $s.="regGS(\"$key\",\"$newval\");\n";
    }
    $s.="\n?>";
    print "Writing to $fen\n";
    $fh=fopen($newfile,'w');
    fwrite($fh,$s);
    fclose($fh);

}


function regGS($key,$val){
    global $setit,$for,$en;
    if ($setit==1){
 $en["$key"]=$val;
    }
    else{
 $for["$key"]=$val;
    }
}

function createArrays($fen,$dirname,$newlang){
    global $setit,$for,$en;
    unset($for);
    unset($en);
    $setit=1;
    include("$dirname/$fen"."en.php");
    $setit=2;
    $foreignfile="$dirname/$fen$newlang.php";
    include("$foreignfile");
    writeFile($foreignfile,$fen);

}

function verifyFile($fen,$dirname,$newlang){
    $check="$dirname/$fen$newlang.php";
    $fh=@fopen($check,'r');
    if ($fh!=null){
 fclose($fh);
    }
    else{
 print "Creating empty $fen\n";
 $fh=fopen($check,'w');
 fwrite($fh,"<?php \n?>");
 fclose($fh);
    }
    createArrays($fen,$dirname,$newlang);
}



function parseFolder($dirname, $depth){
    global $createnew,$newlang,$langarray;
    $handle=opendir($dirname);
    $space=3;
    while (($file = readdir($handle))!==false) {
        $fullname=$dirname."/".$file;
        $filetype=filetype($fullname);
        $isdir=false;
        $isfile=false;
        // avoiding the links
        if ($filetype=="dir") $isdir=true;
        else if ($filetype!="link") $isfile=true;
        // if it's a file
        if ($isfile){
            // filling the array
            $files[]=$file;
        }
        // if it's a directory but not the .. or .
        else if ($isdir&&$file!="."&&$file!=".."){
            // filling the array
            $dirs[]=$file;
        }
    }
    //$isfileforlang=false;
    if (isset($files)){
        for($i=0;$i<count($files);$i++){
     $filen=$files[$i];


     if ( ( (strpos($filen,'locals')===0) ||
            (strpos($filen,'globals')===0) ) &&
     (substr($filen,strlen($filen)-6)=='en.php')){
  $filesinen[]=$filen;
     }



     if ( ( (strpos($filen,'locals')===0) ||
            (strpos($filen,'globals')===0) )
        &&
        (substr($filen,strlen($filen)-4)==='.php')
        &&
        (substr($filen,strlen($filen)-6)!='en.php' || substr($dirname,3,7)=='modules')  //enable editing english files for modules
        ){

        if ( (strpos($filen,'globals')===0) ){
      $langarray[]=substr($filen,8,2);
  }


        print str_repeat(' ',$depth*$space)."<a href='display.php?file=$filen&dir=$dirname' target=panel>$filen</a>\n";
        usleep (0); //usleep(0) seems to be senseless, but needet for a strange behavior of PHP in combination with IE
     }
        }//for

 if (isset($filesinen)&&($createnew)){
     foreach($filesinen as $fen){
  verifyFile(substr($fen,0,strlen($fen)-6),$dirname,$newlang);
  //print substr($fen,0,strlen($fen)-6);
     }
 }
    }
    if (isset($dirs)){
 for($i=0;$i<count($dirs);$i++){
     print str_repeat(' ',$depth*$space).strtoupper($dirs[$i])."\n";
     parseFolder("$dirname/".$dirs[$i],$depth+1);
        }
    }
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
<?php

if (isset($_REQUEST[newlang])){
    $newlang = trim($_REQUEST[newlang]);
}

if ( (isset($newlang)) && ($newlang!='') && (strlen($newlang)>1) &&($newlang!='en') ){
    $createnew=true;
    print "creating files for: $newlang";
    $langarray[]=$newlang;
}
else{
    $createnew=false;
}
$langarray[]='en';
$langfile='';

$langfile.="<?php \n\n";
$langfile.='function registerLanguage($name,$code,$charset){'."\n\n";
$langfile.="\t".'global $languages;'."\n";
$langfile.="\t".'$languages["$code"]=array("name"=>$name,"charset"=>$charset);'."\n";
$langfile.='}'."\n";


print '<PRE>';
parseFolder('..', 0);
print '</PRE>';

?>
<form action=# method=post>

<SELECT NAME=newlang>
<?php

    $Languages=mysql_query ("SELECT Id, Name, OrigName, CodePage, Code FROM Languages ORDER BY Name");
    $NUM_ROWS=mysql_num_rows($Languages);
    if ($NUM_ROWS) {
 $nr= $NUM_ROWS;
        for($loop=0;$loop<$nr;$loop++) {
     $arr=mysql_fetch_array($Languages,MYSQL_ASSOC);
     if ($arr['Code']!='en'){
  print '<OPTION VALUE="'.$arr['Code'].'">'.$arr['OrigName'].'('.$arr['Code'].")\n";
     }
     if (in_array($arr['Code'],$langarray)){
  $langfile.='registerLanguage(\''. $arr['Name']."','".$arr['Code']."','".$arr['CodePage']."');\n";
     }
 }
    }

$langfile.="\n".'?>';

?>
</select>
 <input type=submit value='create language files'>
</form>

<?php
if ($createnew){
    $langf=fopen('../languages.php','w');
    fwrite($langf,$langfile);
    fclose($langf);
}
?>


<?php
if ($createnew){ ?>
    <SCRIPT>
 document.location.href='menu.php';
    </SCRIPT>
<?php
//exit();

}
?>
</body>
</html>
