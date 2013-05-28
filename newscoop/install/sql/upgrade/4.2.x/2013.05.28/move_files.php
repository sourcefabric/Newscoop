<?php

//move all files from /files to /public/files

$newscoop_dir = __DIR__ . '/../../../../..';
rmove($newscoop_dir . '/files', $newscoop_dir . '/public/files');
rmdir($newscoop_dir . '/files');


/**
 * Recursively move files from one directory to another
 *
 * @param String $src - Source of files being moved
 * @param String $dest - Destination of files being moved
 */
function rmove($src, $dest)
{
	// If source is not a directory stop processing
	if(!is_dir($src)) return false;
	 
	// If the destination directory does not exist create it
	if(!is_dir($dest)) {
		if(!mkdir($dest)) {
			// If the destination directory could not be created stop processing
			return false;
		}
	}
 
	// Open the source directory to read in files
	$i = new DirectoryIterator($src);
	foreach($i as $f) {
		if($f->isFile()) {
			rename($f->getRealPath(), "$dest/" . $f->getFilename());
		} else if(!$f->isDot() && $f->isDir()) {
			rmove($f->getRealPath(), "$dest/$f");
			unlink($f->getRealPath());
		}
	}
	unlink($src);
}