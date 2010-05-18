<?php
/**
 * Attachment Manager configuration file.
 * @author $Author: holman $
 */
$GLOBALS['g_campsiteDir'] = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
require_once($GLOBALS['g_campsiteDir'].'/conf/liveuser_configuration.php');

// Only logged in admin users allowed
if (!$LiveUser->isLoggedIn()) {
    header("Location: /$ADMIN/login.php");
    exit(0);
} else {
    $userId = $LiveUser->getProperty('auth_user_id');
    $userTmp = new User($userId);
    if (!$userTmp->exists() || !$userTmp->isAdmin()) {
        header("Location: /$ADMIN/login.php");
        exit(0);
    }
    unset($userTmp);
}

require_once($GLOBALS['g_campsiteDir'].'/conf/configuration.php');

global $Campsite;

/*
 File system path to the directory you want to manage the images
 for multiple user systems, set it dynamically.

 NOTE: This directory requires write access by PHP. That is,
       PHP must be able to create files in this directory.
	   Able to create directories is nice, but not necessary.
*/
$AMConfig['base_dir'] = $Campsite['FILE_DIRECTORY'];

/*
 The URL to the above path, the web browser needs to be able to see it.
 It can be protected via .htaccess on apache or directory permissions on IIS,
 check you web server documentation for futher information on directory protection
 If this directory needs to be publicly accessiable, remove scripting capabilities
 for this directory (i.e. disable PHP, Perl, CGI). We only want to store assets
 in this directory and its subdirectories.
*/
$AMConfig['base_url'] = $Campsite['FILE_BASE_URL'];

/*
  The prefix for thumbnail files, something like .thumb will do. The
  thumbnails files will be named as "prefix_imagefile.ext", that is,
  prefix + orginal filename.
*/
$AMConfig['num_dirs_level_1'] = '1000';

/*
  Thumbnail can also be stored in a directory, this directory
  will be created by PHP. If PHP is in safe mode, this parameter
  is ignored, you can not create directories.

  If you do not want to store thumbnails in a directory, set this
  to false or empty string '';
*/
$AMConfig['num_dirs_level_2'] = '1000';

/*
  Possible values: true, false

 TRUE -  Allow the user to create new sub-directories in the
         $IMConfig['base_dir'].

 FALSE - No directory creation.

 NOTE: If $IMConfig['safe_mode'] = true, this parameter
       is ignored, you can not create directories
*/
$AMConfig['tmp_dir'] = '/tmp/';

/*
  Possible values: true, false

 TRUE -  Show the directory dropdown at the top of the
 		 "Insert Image" window.

 FALSE - Do not show the directory dropdown control at the top
 		 of the "Insert Image" window.
*/
$AMConfig['show_dirs'] = false;

/*
  Possible values: true, false

  TRUE - Allow the user to upload files.

  FALSE - No uploading allowed.
*/
$AMConfig['allow_upload'] = false;

/*
 Possible values: true, false

 TRUE - If set to true, uploaded files will be validated based on the
        function getImageSize, if we can get the image dimensions then
        I guess this should be a valid image. Otherwise the file will be rejected.

 FALSE - All uploaded files will be processed.

 NOTE: If uploading is not allowed, this parameter is ignored.
*/
$AMConfig['validate_files'] = false;

/*
 The default thumbnail if the thumbnails can not be created, either
 due to error or bad image file.
*/
$AMConfig['default_thumbnail'] = 'img/default.gif';

/*
 Valid media formats
*/
$AMConfig['media_formats'] = 'swf,flv,mov,qt,mpg,mp3,mp4,mpeg,avi,wmv,wm,asf,asx,wmx,wvx,rm,ra,ram';
?>