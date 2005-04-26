<?php
$in = $_SERVER[DOCUMENT_ROOT].'/images';
$out = $_SERVER[DOCUMENT_ROOT].'/images/thumbnails';

$d = dir($in);
while (false !== ($entry = $d->read())) {
	if (preg_match('/cms-image-/', $entry)) {
		$nr = preg_replace('/cms-image-/', '', $entry);
		system ("convert -sample 64x64 $in/cms-image-$nr $out/cms-thumb-$nr");
		$image_id = (int)$nr;
		$sql = "update Images set ThumbnailFileName = 'cms-thumb-$nr' where Id = $image_id";
		mysql_query($sql);
	}
}
$d->close();
?>
