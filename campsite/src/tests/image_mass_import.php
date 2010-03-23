<?PHP
require_once('../classes/Image.php');

$dirs = array();

// put the full path to your directory of images here.
$top_dirname = "";
$dirs[] = $top_dirname;
$topdir = dir($top_dirname);
while (false !== ($entry = $topdir->read())) {
	$filename = $top_dirname.$entry;
	if ($entry != "." && $entry != ".." && is_dir($filename)) {
		$dirs[] = $filename;
	}
}
sort($dirs);
foreach ($dirs as $dirname) {
	echo "<b>Directory $dirname</b><br>";
	$dir = dir($dirname);
	while (false !== ($entry = $dir->read())) {
		//echo $entry."<br>";
		$filename = $dirname.'/'.$entry;
		$extension = explode('.', $entry);
		$extension = array_pop($extension);
		//echo $extension."<br>";
		if (@is_file($filename) && (in_array($extension, array('jpg', 'jpeg', 'gif')))) {
	   		echo $entry."<br>";
	   		flush();
	   		$file = array("name" => $entry, "type" => "image/$extension", "tmp_name" => $filename);
			$image = Image::OnImageUpload($file, null, null, null, true);
			if (!is_object($image)) {
				echo "ERROR: could not import image<br>";
			}
		}
	}
	$dir->close();
}
exit;
?>
