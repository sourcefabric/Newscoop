<?php

use Symfony\Component\Console\Input,
Doctrine\DBAL\Types,
Newscoop\Storage,
Newscoop\Entity\Resource,
Newscoop\Entity\Output\OutputSettingsIssue,
Newscoop\Entity\Output\OutputSettingsSection,
Newscoop\Service\IThemeManagementService,
Newscoop\Service\IOutputService,
Newscoop\Service\ISyncResourceService,
Newscoop\Service\IPublicationService,
Newscoop\Service\IIssueService,
Newscoop\Service\ISectionService,
Newscoop\Service\IOutputSettingIssueService,
Newscoop\Service\IOutputSettingSectionService;

if (!defined('APPLICATION_PATH')) {
    // Define path to application directory
    defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../../../application'));

    // Define application environment
    defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

    // Ensure library/ is on include_path
    set_include_path(implode(PATH_SEPARATOR, array(
        realpath(APPLICATION_PATH . '/../library'),
        get_include_path(),
        '/usr/share/php/libzend-framework-php',
    )));

    /** Zend_Application */
    require_once 'Zend/Application.php';

    // Create application, bootstrap, and run
    $application = new Zend_Application(
        APPLICATION_ENV,
        APPLICATION_PATH . '/configs/application.ini'
    );

    $application->bootstrap();
}

global $g_ado_db;


require 'Doctrine/Common/ClassLoader.php';
$classLoader = new \Doctrine\Common\ClassLoader('Newscoop', realpath(APPLICATION_PATH . '/../library'));
$classLoader->register(); // register on SPL autoload stack

$templatesPath = realpath(APPLICATION_PATH . '/../templates');
$themesPath = CS_PATH_SITE . '/themes/unassigned';
if (!is_dir($themesPath)) {
	mkdir($themesPath);
}

require_once realpath(dirname(__FILE__)."/ThemeUpgrade.php");

$themeUpgrade = new ThemeUpgrade($templatesPath, $themesPath);
$themeUpgrade->createThemes();
