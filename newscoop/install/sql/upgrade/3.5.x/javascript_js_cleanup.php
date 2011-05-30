<?php

$dependencies = array(
    'files' => array(
        'admin.js',
        'base64.js',
        'campsite-audiosearch.js',
        'campsite-checkbox.js',
        'campsite.js',
        'crypt.js',
        'json2.js',
        'sha1.js'
    ),
    'directories' => array(
        'domTT',
        'editarea',
        'flowplayer',
        'fValidate',
        'geocoding',
        'jquery',
        'plupload',
        'pwd_meter',
        'scriptaculous',
        'syntaxhighlighter',
        'tinymce',
    ),
);

$docroot = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
define('CS_PATH_JAVASCRIPT', $docroot . DIRECTORY_SEPARATOR . 'javascript');
define('CS_PATH_JS', $docroot . DIRECTORY_SEPARATOR . 'js');

if ($handle = opendir(CS_PATH_JS)) {
    while (($item = readdir($handle)) !== FALSE) {
        if ($item != '.' && $item != '..') {
            $item = CS_PATH_JAVASCRIPT . DIRECTORY_SEPARATOR . $item;
            remove_duplicate($item);
        }
    }
    closedir($handle);
}

function remove_duplicate($item)
{
    if (is_file($item)) {
        @unlink($item);
    } elseif (is_dir($item)) {
        @camp_remove_dir($item);
    }
}

function camp_remove_dir($p_dirName)
{
    $p_dirName = str_replace('//', '/', $p_dirName);
    $dirBaseName = trim($p_dirName, '/');
    if ($p_dirName == "/" || $dirBaseName == ''
            || $dirBaseName == '.' || $dirBaseName == '..'
            || (strpos($dirBaseName, '/') === false && $p_dirName[0] == '/')) {
        return false;
    }
    if (empty($p_msg)) {
        $p_msg = "Unable to remove directory '$p_dirName'";
    }

    $removeDir = true;
    if (strrpos($p_dirName, '*') == (strlen($p_dirName) - 1)) {
        $p_dirName = substr($p_dirName, 0, strlen($p_dirName) - 1);
        $removeDir = false;
    }
    $p_dirName = rtrim($p_dirName, '/');

    $dirContent = scandir($p_dirName);
    if ($dirContent === false) {
        return false;
    }
    foreach ($dirContent as $file) {
        if (in_array($file, $p_skip)) {
                continue;
        }
        if ($file == '.' || $file == '..') {
            continue;
        }
        $filePath = "$p_dirName/$file";
        if (is_dir($filePath)) {
            camp_remove_dir($filePath);
            continue;
        }
        if (!unlink($filePath)) {
            return false;
        }
    }
    if ($removeDir) {
        rmdir($p_dirName);
    }
}

