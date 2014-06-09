<?php

//move all files from /files to /public/files
$newscoop_dir = __DIR__ . '/../../../../..';

$files = array(
    '/include/smarty/Config_File.class.php',
    '/include/smarty/Smarty.class.php',
    '/include/smarty/Smarty_Compiler.class.php',
    '/include/smarty/debug.tpl',
);

$dirs = array(
    '/include/smarty/internals',
    '/include/smarty/plugins',
);

rmove2($newscoop_dir . '/files', $newscoop_dir . '/public/files');
rmove2($newscoop_dir . '/videos', $newscoop_dir . '/public/videos');

$fail = false;
$required_commands = array();

foreach ($files as $file) {
    if (file_exists($newscoop_dir . $file)) {
        if (unlink(realpath($newscoop_dir . $file)) !== true) {
            echo 'Please remove file "'.realpath($newscoop_dir . $file).'"'."\n";
            $required_commands[] = 'sudo rm '.realpath($newscoop_dir . $file);
            $fail = true;
        };
    }
}

foreach ($dirs as $dir) {
    if (file_exists($newscoop_dir . $dir)) {
        if (rrmdir2(realpath($newscoop_dir . $dir)) !== true) {
            echo 'Please remove directory rm -R "'.realpath($newscoop_dir . $dir).'"'."\n";
            $required_commands[] = 'sudo rm -R '.realpath($newscoop_dir . $dir);
            $fail = true;
        };
    }
}

if ($fail) {
    echo 'Some files or directories needs your attention in order to continue. Please remove them manualy: ';
    echo 'In linux it will be: <pre>';
    foreach ($required_commands as $command) {
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
function rmove2($src, $dest)
{
    // If source is not a directory stop processing
    if(!is_dir($src)) return false;

    // If the destination directory does not exist create it
    if (!is_dir($dest)) {
        if (!mkdir($dest)) {
            // If the destination directory could not be created stop processing
            return false;
        }
    }

    // Open the source directory to read in files
    $i = new DirectoryIterator($src);
    foreach ($i as $f) {
        if ($f->isFile()) {
            rename($f->getRealPath(), "$dest/" . $f->getFilename());
        } elseif (!$f->isDot() && $f->isDir()) {
            rmove2($f->getRealPath(), "$dest/$f");
            if (is_file($f->getRealPath())) {
                @unlink($f->getRealPath());
            }
        }
    }

    if (!is_dir($src)) {
        @unlink($src);
    }
}

// When the directory is not empty:
function rrmdir2($dir)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir."/".$object) == "dir") rrmdir2($dir."/".$object); else unlink($dir."/".$object);
            }
        }
        reset($objects);

        return rmdir($dir);
   }
}
