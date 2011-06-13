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


global $g_ado_db;


require 'Doctrine/Common/ClassLoader.php';
$classLoader = new \Doctrine\Common\ClassLoader('Newscoop', realpath(APPLICATION_PATH . '/../library'));
$classLoader->register(); // register on SPL autoload stack

$templatesPath = realpath(APPLICATION_PATH . '/../templates');
$themesPath = CS_PATH_SITE . '/themes/unassigned';
if (!is_dir($themesPath)) {
	mkdir($themesPath);
}


class ThemeUpgrade
{
    /**
     * @var string
     */
    private $templatesPath;

    /**
     * @var string
     */
    private $themesPath;

    /**
     * @var Newscoop\Entity\Resource
     */
    private $resourceId;

    /**
     * @var Newscoop\Service\IThemeManagementService
     */
    private $themeService;

    /**
     * @var Newscoop\Service\IPublicationService
     */
    private $publicationService;

    /**
     * @var Newscoop\Service\IIssueService
     */
    private $issueService;

    /**
     * @var Newscoop\Service\IOutputService
     */
    private $outputService = NULL;

    /**
     * @var Newscoop\Service\IOutputSettingIssueService
     */
    private $outputSettingIssueService = NULL;

    /**
     * @var Newscoop\Service\IOutputSettingSectionService
     */
    private $outputSettingSectionService = NULL;

    /**
     * @var Newscoop\Service\ISectionService
     */
    private $sectionService = NULL;

    /**
     * @var Newscoop\Service\ISyncResourceService
     */
    private $syncResourceService;

    /**
     * @var string
     */
    private $themeXMLFile = 'theme.xml';


    public function __construct($templatesPath, $themesPath)
    {
        $this->templatesPath = $templatesPath;
        $this->themesPath = $themesPath;
    }


    /**
     * Provides the controller resource id.
     *
     * @return Newscoop\Services\Resource\ResourceId
     * 		The controller resource id.
     */
    public function getResourceId()
    {
        if ($this->resourceId === NULL) {
            $this->resourceId = new Newscoop\Service\Resource\ResourceId(__CLASS__);
        }
        return $this->resourceId;
    }


    /**
     * Provides the theme service.
     *
     * @return Newscoop\Service\IThemeManagementService
     * 		The theme service to be used by this controller.
     */
    public function getThemeService()
    {
        if ($this->themeService === NULL) {
            $this->themeService = $this->getResourceId()->getService(IThemeManagementService::NAME_1);
        }
        return $this->themeService;
    }


    /**
     * Provides the publication service.
     *
     * @return Newscoop\Service\IPublicationService
     * 		The publication service
     */
    public function getPublicationService()
    {
        if ($this->publicationService === NULL) {
            $this->publicationService = $this->getResourceId()->getService(IPublicationService::NAME);
        }
        return $this->publicationService;
    }


    /**
     * Provides the issue service.
     *
     * @return Newscoop\Service\IIssueService
     * 		The issue service
     */
    public function getIssueService()
    {
        if ($this->issueService === NULL) {
            $this->issueService = $this->getResourceId()->getService(IIssueService::NAME);
        }
        return $this->issueService;
    }

    /**
     * Provides the ouput service.
     *
     * @return Newscoop\Service\IOutputService
     * 		The service service to be used by this controller.
     */
    public function getOutputService()
    {
        if ($this->outputService === NULL) {
            $this->outputService = $this->getResourceId()->getService(IOutputService::NAME);
        }
        return $this->outputService;
    }

    /**
     * Provides the Output setting issue service.
     *
     * @return Newscoop\Service\IOutputSettingIssueService
     * 		The output setting issue service to be used by this controller.
     */
    public function getOutputSettingIssueService()
    {
        if ($this->outputSettingIssueService === NULL) {
            $this->outputSettingIssueService = $this->getResourceId()->getService(IOutputSettingIssueService::NAME);
        }
        return $this->outputSettingIssueService;
    }

    /**
     * Provides the Output  setting service.
     *
     * @return Newscoop\Service\IOutputSettingSectionService
     * 		The output setting section service to be used by this controller.
     */
    public function getOutputSettingSectionService()
    {
        if ($this->outputSettingSectionService === NULL) {
            $this->outputSettingSectionService = $this->getResourceId()->getService(IOutputSettingSectionService::NAME);
        }
        return $this->outputSettingSectionService;
    }

    /**
     * Provides the Section service.
     *
     * @return Newscoop\Service\ISectionService
     * 		The section service to be used by this controller.
     */
    public function getSectionService()
    {
        if ($this->sectionService === NULL) {
            $this->sectionService = $this->getResourceId()->getService(ISectionService::NAME);
        }
        return $this->sectionService;
    }

    /**
     * Provides the sync resources service.
     *
     * @return Newscoop\Service\ISyncResourceService
     * 		The sync resource service to be used by this controller.
     */
    public function getSyncResourceService()
    {
        if ($this->syncResourceService === NULL) {
            $this->syncResourceService = $this->getResourceId()->getService(ISyncResourceService::NAME);
        }
        return $this->syncResourceService;
    }


    /**
     * Returns an array of themes (theme path)
     *
     * @return array
     */
    public function themesList()
    {
        $hasOneTheme = count(glob($this->templatesPath . "/*.tpl")) > 0;
        if ($hasOneTheme) {
            return array(''=>'Default');
        }
        $themes = array();
        foreach (glob($this->templatesPath . "/*") as $filePath) {
            $fileName = basename($filePath);
            if (!is_dir($filePath) || $fileName == 'system_templates'
            || count(glob($filePath . "/*.tpl")) == 0) {
                continue;
            }

            $themes[$fileName] = $this->createName($fileName);
        }
        return $themes;
    }


    /**
     * Creates a name from the theme path
     *
     * @param string $themePath
     *
     * @return string
     * 		Returns the theme name
     */
    public function createName($themePath)
    {
        $parts = preg_split('/[\s_\-.,]+/', $themePath);
        $name = implode(' ', array_map('ucfirst', $parts));
        return $name;
    }


    /**
     * Moves the existing templates into the new themes structure
     *
     * @return bool
     * 		True on success, false otherwise
     */
    public function createThemes()
    {
        foreach ($this->themesList() as $themePath=>$themeName) {
            $this->createTheme($themePath);
        }
        $themes = $this->getThemeService()->getUnassignedThemes();
        foreach ($themes as $theme) {
            $theme->setName($this->createName(basename($theme->getPath())));
            $this->getThemeService()->updateTheme($theme);
            $dstPath = $this->themesPath . (empty($themePath) ? '/default' : '/' . $themePath);
            $this->fixPathsInFolder($dstPath);
            $this->assignTheme($theme);
        }
    }


    /**
     * Assign the theme to the publications that use it.
     *
     * @param $theme
     *
     * @return bool
     */
    public function assignTheme(Newscoop\Entity\Theme $theme)
    {
        global $g_ado_db;
        $themePath = basename($theme->getPath());
        if (empty($themePath)) {
            $likeStr = '';
        } else {
            $likeStr = $g_ado_db->Escape($themePath) . '/';
        }

        $sql = "SELECT DISTINCT iss.IdPublication
FROM Issues AS iss
WHERE IssueTplId IN (SELECT Id FROM Templates WHERE Name LIKE '$likeStr%')
    AND SectionTplId IN (SELECT Id FROM Templates WHERE Name LIKE '$likeStr%')
    AND ArticleTplId IN (SELECT Id FROM Templates WHERE Name LIKE '$likeStr%')";
        $publicationIds = $g_ado_db->GetAll($sql);
        foreach ($publicationIds as $publicationId) {
            $publicationId = $publicationId['IdPublication'];
            $this->setThemeDefaultOutSettings($theme, $publicationId);
            $publicationTheme = $this->assignThemeToPublication($theme, $publicationId);
            if (is_null($publicationTheme)) {
                continue;
            }
            $this->setIssuesTheme($publicationId, $theme, $publicationTheme);
            $this->setSectionOutSettings($publicationId, $theme, $publicationTheme);
        }
    }


    /**
     * Sets the theme for all issues of the given publication
     *
     * @param $publicationId
     *
     * @param $theme
     *
     * @return bool
     */
    public function setIssuesTheme($publicationId, Newscoop\Entity\Theme $theme, Newscoop\Entity\Theme $publicationTheme)
    {
        global $g_ado_db;

        $sql = $this->buildIssuesQuery($publicationId, $theme->getPath());
        $issuesList = $g_ado_db->GetAll($sql);

        foreach ($issuesList as $issueData) {
            $issue = $this->getIssueService()->findById($issueData['id']);
            if (is_null($issue)) {
                continue;
            }
            $outSetIssues = $this->getOutputSettingIssueService()->findByIssue($issueData['id']);
            $newOutputSetting = false;
            if (count($outSetIssues) > 0) {
                $outSetIssue = $outSetIssues[0];
            } else {
                $outSetIssue = new OutputSettingsIssue();
                $outSetIssue->setOutput($this->getOutputService()->findByName('Web'));
                $outSetIssue->setIssue($issue);
                $newOutputSetting = true;
            }
            $outTh = $this->getThemeService()->getOutputSettings($publicationTheme);
            if (count($outTh) == 0) {
                return false;
            }
            $outTh = array_shift($outTh);

            $outSetIssue->setThemePath($this->getSyncResourceService()->getThemePath($publicationTheme->getPath()));

            if (!empty($issueData['issue_template'])) {
                $rscPath = $publicationTheme->getPath().basename($issueData['issue_template']);
                if($rscPath != $outTh->getFrontPage()->getPath()){
                    $outSetIssue->setFrontPage($this->getSyncResourceService()->getResource('frontPage', $rscPath));
                }else {
                    $outSetIssue->setFrontPage(null);
                }
            } else {
                $outSetIssue->setFrontPage(null);
            }

            if (!empty($issueData['section_template'])) {
                $rscPath = $publicationTheme->getPath().basename($issueData['section_template']);
                if($rscPath != $outTh->getSectionPage()->getPath()){
                    $outSetIssue->setSectionPage($this->getSyncResourceService()->getResource('sectionPage',$rscPath));
                }else {
                    $outSetIssue->setSectionPage(null);
                }
            } else {
                $outSetIssue->setSectionPage(null);
            }

            if (!empty($issueData['article_template'])) {
                $rscPath = $publicationTheme->getPath().basename($issueData['article_template']);
                if($rscPath != $outTh->getArticlePage()->getPath()){
                    $outSetIssue->setArticlePage($this->getSyncResourceService()->getResource('articlePage', $rscPath));
                }else {
                    $outSetIssue->setSectionPage(null);
                }
            } else {
                $outSetIssue->setArticlePage(null);
            }

            if ($newOutputSetting) {
                $this->getOutputSettingIssueService()->insert($outSetIssue);
            } else {
                $this->getOutputSettingIssueService()->update($outSetIssue);
            }
        }
    }


    /**
     * Set the section output settings for the given publication
     *
     * @param $publicationId
     *
     * @param $theme
     *
     * @param $publicationTheme
     */
    public function setSectionOutSettings($publicationId, Newscoop\Entity\Theme $theme, Newscoop\Entity\Theme $publicationTheme)
    {
        global $g_ado_db;

        $themePath = basename($theme->getPath());
        if (empty($themePath)) {
            $likeStr = '';
        } else {
            $likeStr = $g_ado_db->Escape($themePath) . '/';
        }

        $sql = "SELECT tpl_s.Name AS section_template,
    tpl_a.Name AS article_template,
    sec.NrIssue, sec.Number, sec.IdLanguage, sec.id
FROM Sections AS sec
    LEFT JOIN Templates AS tpl_s ON sec.SectionTplId = tpl_s.Id
    LEFT JOIN Templates AS tpl_a ON sec.ArticleTplId = tpl_a.Id
WHERE sec.IdPublication = $publicationId
	AND (SectionTplId > 0 OR ArticleTplId > 0)
    AND (SectionTplId IN (SELECT Id FROM Templates WHERE Name LIKE '$likeStr%')
    	OR ArticleTplId IN (SELECT Id FROM Templates WHERE Name LIKE '$likeStr%'))
ORDER BY Number DESC";
        $sectionsList = $g_ado_db->GetAll($sql);

        foreach ($sectionsList as $sectionsData) {
            $section = $this->getSectionService()->findById($sectionsData['id']);
            if (is_null($section)) {
                continue;
            }
            $outSetSections = $this->getOutputSettingSectionService()->findBySection($sectionsData['id']);
            $newOutputSetting = false;
            if (count($outSetSections) > 0) {
                $outSetSection = $outSetSections[0];
            } else {
                $outSetSection = new OutputSettingsSection();
                $outSetSection->setOutput($this->getOutputService()->findByName('Web'));
                $outSetSection->setSection($section);
                $newOutputSetting = true;
            }
            if (!empty($sectionsData['section_template'])) {
                $rscPath = $publicationTheme->getPath().basename($sectionsData['section_template']);
                $outSetSection->setSectionPage($this->getSyncResourceService()->getResource('sectionPage',$rscPath));
            } else {
                $outSetSection->setSectionPage(null);
            }

            if (!empty($sectionsData['article_template'])) {
                $rscPath = $publicationTheme->getPath().basename($sectionsData['article_template']);
                $outSetSection->setArticlePage($this->getSyncResourceService()->getResource('articlePage', $rscPath));
            } else {
                $outSetSection->setArticlePage(null);
            }
            if ($newOutputSetting) {
                $this->getOutputSettingSectionService()->insert($outSetSection);
            } else {
                $this->getOutputSettingSectionService()->update($outSetSection);
            }
        }
    }


    /**
     * Set the theme default output settings
     *
     * @param Newscoop\Entity\Theme $theme
     *
     * @param int $publicationId
     *
     * @return bool
     */
    public function setThemeDefaultOutSettings(Newscoop\Entity\Theme $theme, $publicationId)
    {
        global $g_ado_db;

        $sql = $this->buildDefaultIssueQuery($publicationId, $theme->getPath());
        $outSettings = $g_ado_db->GetAll($sql);
        if (count($outSettings) == 0) {
            return false;
        }
        $outSettings = array_shift($outSettings);

        $outputSettings = $this->getThemeService()->getOutputSettings($theme);
        if (count($outputSettings) == 0) {
            return false;
        }
        $outputSettings = array_shift($outputSettings);
        $prefix = dirname($theme->getPath()) . DIR_SEP;
        $outputSettings->setFrontPage($this->getNonDbResource('frontPage', $prefix.$outSettings['issue_template']));
        $outputSettings->setSectionPage($this->getNonDbResource('sectionPage', $prefix.$outSettings['section_template']));
        $outputSettings->setArticlePage($this->getNonDbResource('articlePage', $prefix.$outSettings['article_template']));
        $outputSettings->setErrorPage($this->getNonDbResource('errorPage', $prefix.$outSettings['error_template']));
        return $this->getThemeService()->assignOutputSetting($outputSettings, $theme);
    }

    public function getNonDbResource($name, $path){
        $rsc = new Resource();
        $rsc->setName($name);
        $rsc->setPath($path);
        return $rsc;
    }


    /**
     * Assign the theme to the given publication if the publication issues used templates from this theme.
     *
     * @param Newscoop\Entity\Theme $theme
     *
     * @param int $publicationId
     *
     * @return Newscoop\Entity\Theme
     */
    public function assignThemeToPublication(Newscoop\Entity\Theme $theme, $publicationId)
    {
        $publication = $this->getPublicationService()->getById($publicationId);
        return $this->getThemeService()->assignTheme($theme, $publication);
    }


    /**
     * Builds the SQL query that returns the last issue having all the templates set for a certain publication/theme
     *
     * @param int $publicationId
     *
     * @param string $themePath
     *
     * @return string
     */
    public function buildDefaultIssueQuery($publicationId, $themePath)
    {
        global $g_ado_db;

        $themePath = basename($themePath);
        if (empty($themePath)) {
            $likeStr = '';
        } else {
            $likeStr = $g_ado_db->Escape($themePath) . '/';
        }

        $sql = "SELECT tpl_i.Name AS issue_template,
    tpl_s.Name AS section_template,
    tpl_a.Name AS article_template,
    tpl_e.Name AS error_template,
    iss.Number, iss.IdLanguage, iss.id
FROM Issues AS iss
    LEFT JOIN Templates AS tpl_i ON iss.IssueTplId = tpl_i.Id
    LEFT JOIN Templates AS tpl_s ON iss.SectionTplId = tpl_s.Id
    LEFT JOIN Templates AS tpl_a ON iss.ArticleTplId = tpl_a.Id,
    Publications AS pub
    LEFT JOIN Templates AS tpl_e ON pub.url_error_tpl_id = tpl_e.Id
WHERE iss.IdPublication = $publicationId
    AND IssueTplId IN (SELECT Id FROM Templates WHERE Name LIKE '$likeStr%')
    AND SectionTplId IN (SELECT Id FROM Templates WHERE Name LIKE '$likeStr%')
    AND ArticleTplId IN (SELECT Id FROM Templates WHERE Name LIKE '$likeStr%')
    AND pub.Id = $publicationId
    AND pub.url_error_tpl_id IN (SELECT Id FROM Templates WHERE Name LIKE '$likeStr%')
ORDER BY Number DESC
LIMIT 0, 1";
        return $sql;
    }


    /**
     * Builds the SQL query that returns the list of issues for a certain publication/theme
     *
     * @param int $publicationId
     *
     * @param string $themePath
     *
     * @return string
     */
    public function buildIssuesQuery($publicationId, $themePath)
    {
        global $g_ado_db;

        $themePath = basename($themePath);
        if (empty($themePath)) {
            $likeStr = '';
        } else {
            $likeStr = $g_ado_db->Escape($themePath) . '/';
        }

        $sql = "SELECT tpl_i.Name AS issue_template,
    tpl_s.Name AS section_template,
    tpl_a.Name AS article_template,
    tpl_e.Name AS error_template,
    iss.Number, iss.IdLanguage, iss.id
FROM Issues AS iss
    LEFT JOIN Templates AS tpl_i ON iss.IssueTplId = tpl_i.Id
    LEFT JOIN Templates AS tpl_s ON iss.SectionTplId = tpl_s.Id
    LEFT JOIN Templates AS tpl_a ON iss.ArticleTplId = tpl_a.Id,
    Publications AS pub
    LEFT JOIN Templates AS tpl_e ON pub.url_error_tpl_id = tpl_e.Id
WHERE iss.IdPublication = $publicationId
	AND (IssueTplId > 0 OR SectionTplId > 0 OR ArticleTplId > 0)
    AND (IssueTplId IN (SELECT Id FROM Templates WHERE Name LIKE '$likeStr%')
    	OR SectionTplId IN (SELECT Id FROM Templates WHERE Name LIKE '$likeStr%')
    	OR ArticleTplId IN (SELECT Id FROM Templates WHERE Name LIKE '$likeStr%'))
    AND pub.Id = $publicationId
    AND pub.url_error_tpl_id IN (SELECT Id FROM Templates WHERE Name LIKE '$likeStr%')
ORDER BY Number DESC";
        return $sql;
    }

    /**
     * Moves a group of templates from their directory to the new theme structure, creating the theme
     *
     * @param string $themeSrcDir
     * @return bool
     * 		True if the move was performed succesfully, false otherwise
     */
    public function createTheme($themeSrcDir)
    {
        $srcPath = $this->templatesPath . (empty($themeSrcDir) ? '' : '/' . $themeSrcDir);
        $dstPath = $this->themesPath . (empty($themeSrcDir) ? '/default' : '/' . $themeSrcDir);

        mkdir($dstPath, 0777, true);
        copy(dirname(__FILE__) . '/' . $this->themeXMLFile, "$dstPath/$this->themeXMLFile");
        return $this->copyPath($srcPath, $dstPath);
    }


    /**
     * Copies the given file or directory to the destination path.
     *
     * @param string $srcPath
     *
     * @param string $dstPath
     *
     * @return bool
     */
    private function copyPath($srcPath, $dstPath)
    {
        if (is_dir($srcPath)) {
            if (!is_dir($dstPath)) {
                mkdir($dstPath);
            }
            $files = array_merge(glob($srcPath . "/*"), glob($srcPath . "/.*"));
            foreach ($files as $filePath) {
                if (basename($filePath) == '.' || basename($filePath) == '..') {
                    continue;
                }
                if (!$this->copyPath($filePath, $dstPath . '/' . basename($filePath))) {
                    return false;
                }
            }
            return true;
        } elseif (is_file($srcPath)) {
            return copy($srcPath, $dstPath);
        }
        return false;
    }

    function fixPathsInFolder($folderPath, $folder = null){
        if($folder == null){
            $folder = basename($folderPath);
        }
        if ($handle = opendir($folderPath)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    $path = realpath($folderPath.'/'.$file);
                    if(is_dir($path)){
                        $this->fixPathsInFolder($path, $folder);
                    } else {
                        if(pathinfo($path, PATHINFO_EXTENSION) == 'tpl'){
                            $this->fixPathsInFile($path, $folder);
                        }
                    }
                }
            }
            closedir($handle);
        }
    }

    /**
     * Fixes the tpl path in the provided file path.
     * @param str $filePath
     * 		The file path.
     * @param str $folder
     * 		The folder name that previouslly contained the template.
     */
    function fixPathsInFile($filePath, $folder){
        $content = file_get_contents($filePath);

        $replacer = function($match){
            return "\"{{ url static_file='".$match['path']."' }}\"";
        };

        //Replace all of the gimme tag + relative path
        $pattern = '([\"\']+[\s]*[http\:\/\/\]?{\{[\s]*\$gimme\-\>publication\-\>site[\s]*\}\}\/templates/'.$folder.'\/(?<path>[^\"]+)[\"\']+)';
        $content = preg_replace_callback($pattern, $replacer, $content);

        //Replace all of the relative path
        $pattern = '([\"\']+\/templates/'.$folder.'\/(?<path>[^\"]+)[\"\']+)';
        $content = preg_replace_callback($pattern, $replacer, $content);

        //Replace all relatives to theme folder (this kind of risky but i just might doe everithing forgoted)
        $content = preg_replace('('.$folder.'\/)', '', $content);

        $fh = fopen($filePath, 'w') or die("can't open file");
        fwrite($fh, $content);
        fclose($fh);
    }
}


$themeUpgrade = new ThemeUpgrade($templatesPath, $themesPath);
//$themeUpgrade->fixPathsInFile('c:/wamp/www/newscoop/themes/unassigned/classic/index.tpl', 'classic');
//$themeUpgrade->fixPathsInFolder('c:/wamp/www/newscoop/themes/unassigned/set_thejournal');
$themeUpgrade->createThemes();