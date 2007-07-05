<?php

// redirect to the installation process if necessary
if (!file_exists(CS_PATH_DOCROOT.DIR_SEP.'configuration.php')) {
    header('Location: install/index.php');
}

// include some common classes
require_once(CS_PATH_DOCROOT.DIR_SEP.'classes'.DIR_SEP.'CampSite.php');
require_once(CS_PATH_DOCROOT.DIR_SEP.'classes'.DIR_SEP.'CampRequest.php');
require_once(CS_PATH_DOCROOT.DIR_SEP.'classes'.DIR_SEP.'CampURL.php');
require_once(CS_PATH_DOCROOT.DIR_SEP.'classes'.DIR_SEP.'CampVersion.php');

?>