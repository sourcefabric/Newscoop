<?php

function upgrade_35x_javascript_js_cleanup() {

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
            upgrade_35x_remove_duplicate($item);
        }
    }
    closedir($handle);
}

} // fn upgrade_35x_javascript_js_cleanup

function upgrade_35x_remove_duplicate($item)
{
    if (is_file($item)) {
        @unlink($item);
    } elseif (is_dir($item)) {
        @upgrade_35x_camp_remove_dir($item);
    }
}

function upgrade_35x_camp_remove_dir($p_dirName)
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
            upgrade_35x_camp_remove_dir($filePath);
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

// the (function) names defined here shall not clash with those defined at other places
// since the new upgrade system works by calling 'require' on the respective php files
upgrade_35x_javascript_js_cleanup();

