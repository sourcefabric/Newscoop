<?php
// Since the XML_Serializer package is not yet stable,
// we must use our own package.  The package has a bug fix applied
// that is required for the Localizer XML files to work.
require_once($_SERVER['DOCUMENT_ROOT'].'/include/XML_Serializer/Serializer.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/include/XML_Serializer/Unserializer.php');

global $g_localizerConfig;

// The default language, which forms the keys for 
// all other languages.
$g_localizerConfig['DEFAULT_LANGUAGE'] = 'en';
//define('LOCALIZER_DEFAULT_LANG', 'en');

// Filename prefix for translation files.
//define('LOCALIZER_FILENAME_PREFIX', 'locals');
$g_localizerConfig['FILENAME_PREFIX'] = 'locals';

// Filename prefix for the global translation file -
// a file that is always loaded with the particular 
// locals file.
//define('LOCALIZER_FILENAME_PREFIX_GLOBAL', 'globals');
$g_localizerConfig['FILENAME_PREFIX_GLOBAL'] = 'globals';

// Set to a specific type if your code is using that type.
// Currently supported types are 'gs' and 'xml'.
// You can also set this to the empty string and the code
// will do its best to figure out the current type.
//define('LOCALIZER_DEFAULT_FILE_TYPE', 'gs');
$g_localizerConfig['DEFAULT_FILE_TYPE'] = 'gs';

// The top-level directory to the set of directories
// that need translation files.
//define('LOCALIZER_BASE_DIR', $_SERVER['DOCUMENT_ROOT'].'/admin-files');
$g_localizerConfig['BASE_DIR'] = $_SERVER['DOCUMENT_ROOT'].'/admin-files';

// Name of the XML file that contains the list of supported languages.
//define('LOCALIZER_LANGUAGE_METADATA_FILENAME', 'languages.xml');
$g_localizerConfig['LANGUAGE_METADATA_FILENAME'] = 'languages.xml';

// File encoding for XML files.
//define('LOCALIZER_ENCODING', 'UTF-8');
$g_localizerConfig['FILE_ENCODING'] = 'UTF-8';

// For the interface - the relative path (from DOCUMENT_ROOT)
// of the icons directory
global $ADMIN;
//define('LOCALIZER_ICONS_DIR', "/$ADMIN/img/icon");
$g_localizerConfig['ICONS_DIR'] = "/$ADMIN/img/icon";

// The size of the input fields for the admin interface.
//define('LOCALIZER_INPUT_SIZE', 70);
$g_localizerConfig['INPUT_SIZE'] = 70;

// List supported file types, in order of preference.
$g_localizerConfig['FILE_TYPES'] = array('xml', 'gs');
?>
