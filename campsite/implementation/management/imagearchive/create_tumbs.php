<?php
$in = $_SERVER[DOCUMENT_ROOT].'images';
$out = $_SERVER[DOCUMENT_ROOT].'images/tumbnails';

$d = dir($in);
while (false !== ($entry = $d->read())) {
if (preg_match('/cms-image-/', $entry)) {
       $nr = preg_replace('/cms-image-/', '', $entry);
       system ("convert -sample 64x64 $in/cms-image-$nr $out/cms-tumb-$nr");
   }
}
$d->close();
?>
