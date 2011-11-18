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
    require_once __DIR__ . '/../../../../application.php';
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
