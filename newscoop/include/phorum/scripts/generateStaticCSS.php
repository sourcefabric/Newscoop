<?php
/* generateStaticCSS.php
 * this script is meant to be run from the command-line
 * it extracts the CSS-data from the templates and will write it to a static file
 * keep in mind that you'll have to run it by hand if your color-settings change!!!
 *
 * for using that script you have either to use the chdir call or put it in the phorum-dir
 * keep in mind that others could use this script to overwrite files on your webserver 
 * therefore there is the first exit(); in there, you have to remove it to use the script too
 * 
 * to have Phorum use the static CSS, you'll have to edit header.tpl, instead the include of the css
 * you have to use a link href ... 
 */

 // that's here for security measures, remove it if you want to use the script!!!
 exit();
 
 //chdir("../");
 
include './common.php';

if($argc < 2) {
    echo "needs 2 parameters, first as forum-id, second as filename (including path) of the css to be generated.\n";
    exit();       
}

// the second arg is the filename
$filepath=$argv[2];

if(is_dir($filename)) {
    echo "the second argument has to be a filename and no directory!\n";
    exit();       
}

echo "Generating static CSS-file for Forum ".$PHORUM['forum_id']."\n";

ob_start();
include phorum_get_template('css');
$css_str=ob_get_contents();
ob_end_clean();

echo "writing CSS-file to ".$filepath.".\n";

$fp=fopen($filepath,"w");
fputs($fp,$css_str);
fclose($fp);

?>
