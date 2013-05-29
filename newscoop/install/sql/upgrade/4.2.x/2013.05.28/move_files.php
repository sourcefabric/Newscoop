<?php

//move all files from /files to /public/files
$newscoop_dir = __DIR__ . '/../../../../..';

$files = array(
	'/application/configs/application.ini-dist',
	'/library/Newscoop/Services/EventDispatcherService.php',
	'/library/Newscoop/DoctrineEventDispatcherProxy.php',
	'/library/Newscoop/Entity/Repository/User/SubscriberRepository.php',
	'/library/Resource/Doctrine.php',
	'/application/modules/admin/controllers/TestController.php',
	'/application/controllers/ArticleofthedayController.php',
	'/library/Newscoop/Entity/User/Subscriber.php',
	'/library/Newscoop/Entity/Entity.php',
	'/.disable_upgrade',
	'/README.txt',
	'/.travis.yml',
	'/UPGRADE.txt'
);

$dirs = array(
	'/library/Newscoop/Entity/Proxy',
	'/library/fabpot-dependency-injection-07ff9ba',
	'/library/fabpot-event-dispatcher-782a5ef',
	'/library/smarty3',
	'/docs',
	'/files',
	'/videos',
);

rmove($newscoop_dir . '/files', $newscoop_dir . '/public/files');
rmove($newscoop_dir . '/videos', $newscoop_dir . '/public/videos');

$fail = false;
$required_commands = array();

foreach($files as $file) {
	if (file_exists($newscoop_dir . $file)) {
		if(unlink(realpath($newscoop_dir . $file)) !== true) {
			echo 'Please remove file "'.realpath($newscoop_dir . $file).'"'."\n";
			$required_commands[] = 'sudo rm '.realpath($newscoop_dir . $file);
			$fail = true;
		};
	}
}

foreach($dirs as $dir) {
	if (file_exists($newscoop_dir . $dir)) {
		if(rrmdir(realpath($newscoop_dir . $dir)) !== true) {
			echo 'Please remove directory rm -R "'.realpath($newscoop_dir . $dir).'"'."\n";
			$required_commands[] = 'sudo rm -R '.realpath($newscoop_dir . $dir);
			$fail = true;
		};
	}
}

if ($fail) {
	echo 'Some files or directories needs your attention in order to continue. Please remove them manualy: ';
	echo 'In linux it will be: <pre>';
	foreach($required_commands as $command) {
		echo $command;
	}
	echo '</pre>';
	die;
}

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
			@unlink($f->getRealPath());
		}
	}
	@unlink($src);
}

// When the directory is not empty:
function rrmdir($dir) {
   	if (is_dir($dir)) {
     	$objects = scandir($dir);
    	foreach ($objects as $object) {
       		if ($object != "." && $object != "..") {
         		if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
       		}
     	}
    	reset($objects);
    	
    	return rmdir($dir);
   }
}