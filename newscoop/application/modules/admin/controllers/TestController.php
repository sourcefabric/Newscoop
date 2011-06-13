<?php

use Newscoop\Service\IThemeManagementService;
use Newscoop\Service\IOutputService;
use Newscoop\Service\ILanguageService;
use Newscoop\Service\ISyncResourceService;
use Newscoop\Service\IPublicationService;
use Newscoop\Service\IThemeService;
use Newscoop\Service\IOutputSettingIssueService;
use Newscoop\Service\IOutputSettingSectionService;
use Newscoop\Service\IIssueService;
use Newscoop\Service\ISectionService;
use Newscoop\Service\ITemplateSearchService;
use Newscoop\Entity\Publication;
use Newscoop\Entity\Theme;
use Newscoop\Entity\Resource;
use Newscoop\Entity\OutputSettings;
use Newscoop\Entity\Output\OutputSettingsIssue;
use Newscoop\Entity\Output\OutputSettingsSection;
use Newscoop\Service\Resource\ResourceId;
use Newscoop\Service\Model\SearchTheme;
use Newscoop\Service\Model\SearchPublication;
use Newscoop\Service\Model\SearchLanguage;

class Admin_TestController extends Zend_Controller_Action
{

    /** @var Newscoop\Services\Resource\ResourceId */
    private $resourceId = NULL;
    /** @var Newscoop\Service\IThemeService */
    private $themeService = NULL;
    /** @var Newscoop\Service\IThemeManagementService */
    private $themeManagementService = NULL;
    /** @var Newscoop\Service\IPublicationService */
    private $publicationService = NULL;
    /** @var Newscoop\Service\ILanguageService */
    private $languageService = NULL;
    /** @var Newscoop\Service\IIssueService */
    private $issueService = NULL;
    /** @var Newscoop\Service\ISectionService */
    private $sectionService = NULL;
    /** @var Newscoop\Service\IOutputService */
    private $outputService = NULL;
    /** @var Newscoop\Service\IOutputSettingSectionService */
    private $outputSettingSectionService = NULL;
    /** @var Newscoop\Service\IOutputSettingIssueService */
    private $outputSettingIssueService = NULL;
    /** @var Newscoop\Service\ITemplateSearchService */
    private $templateSearchService = NULL;
    /** @var Newscoop\Service\ISyncResourceService */
    private $syncResourceService = NULL;

    public function init()
    {

    }

    /* --------------------------------------------------------------- */


    /**
     * Provides the controller resource id.
     *
     * @return Newscoop\Services\Resource\ResourceId
     * 		The controller resource id.
     */
    public function getResourceId()
    {
        if ($this->resourceId === NULL) {
            $this->resourceId = new ResourceId(__CLASS__);
        }
        return $this->resourceId;
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
     * Provides the language service.
     *
     * @return Newscoop\Service\ILanguageService
     * 		The language service to be used by this controller.
     */
    public function getLanguageService()
    {
        if ($this->languageService === NULL) {
            $this->languageService = $this->getResourceId()->getService(ILanguageService::NAME);
        }
        return $this->languageService;
    }

    /**
     * Provides the publications service.
     *
     * @return Newscoop\Service\IPublicationService
     * 		The publication service to be used by this controller.
     */
    public function getPublicationService()
    {
        if ($this->publicationService === NULL) {
            $this->publicationService = $this->getResourceId()->getService(IPublicationService::NAME);
        }
        return $this->publicationService;
    }

    /**
     * Provides the theme service.
     *
     * @return Newscoop\Service\IThemeService
     * 		The theme service to be used by this controller.
     */
    public function getThemeService()
    {
        if ($this->themeService === NULL) {
            $this->themeService = $this->getResourceId()->getService(IThemeService::NAME);
        }
        return $this->themeService;
    }

    /**
     * Provides the theme management service.
     *
     * @return Newscoop\Service\IThemeManagementService
     * 		The theme management service to be used by this controller.
     */
    public function getThemeManagementService()
    {
        if ($this->themeManagementService === NULL) {
            $this->themeManagementService = $this->getResourceId()->getService(IThemeManagementService::NAME_1);
        }
        return $this->themeManagementService;
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
     * Provides the Issue service.
     *
     * @return Newscoop\Service\IIssueService
     * 		The issue service to be used by this controller.
     */
    public function getIssueService()
    {
        if ($this->issueService === NULL) {
            $this->issueService = $this->getResourceId()->getService(IIssueService::NAME);
        }
        return $this->issueService;
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
     * Provides the Template search service.
     *
     * @return Newscoop\Service\ITemplateSearchService
     * 		The section service to be used by this controller.
     */
    public function getTemplateSearchService()
    {
        if ($this->templateSearchService === NULL) {
            $this->templateSearchService = $this->getResourceId()->getService(ITemplateSearchService::NAME);
        }
        return $this->templateSearchService;
    }

    /**
     * Provides the sync resource service.
     *
     * @return Newscoop\Service\ISyncResourceService
     * 		The sync resource service to be used.
     */
    protected function getSyncResourceService()
    {
        if ($this->syncResourceService === NULL) {
            $this->syncResourceService = $this->getResourceId()->getService(ISyncResourceService::NAME);
        }
        return $this->syncResourceService;
    }

    /* --------------------------------------------------------------- */

    public function indexAction()
    {
        $this->test8();
        die;
    }

    protected function test1()
    {
        $search = new SearchPublication();
        $search->NAME->orderAscending();

        try {
            $outputs = $this->getOutputService()->getEntities();
            $text = '---><br/>'; //.$this->getLanguageService()->getCount();

            foreach ($outputs as $out) {
                /* @var $lang Language */
                $text = $text . $out->getName() . '<br/>';
            }

            $this->view->text = $text;
        } catch (\Exception $e) {
            $this->view->text = $e->getMessage();
        }
    }

    protected function test2()
    {
        $search = new SearchTheme();
        $search->NAME->orderAscending();


        try {
            $themes = $this->getThemeService()->getEntities();
            $text = '---><br/>';

            foreach ($themes as $theme) {
                /* @var $theme Theme */
                $text = $text . $theme->getName() . '  -  ' . $theme->getId() . '  -  ' . $theme->getPath() . '<br/>';
                $imgs = $this->getThemeService()->getPresentationImages($theme);
                foreach ($imgs as $img) {
                    /* @var $img Resource */
                    $text = $text . '              ' . $img->getName() . '  -  ' . $img->getId() . '  -  ' . $img->getPath() . '<br/>';
                }
            }

            $this->view->text = $text;
        } catch (\Exception $e) {
            $this->view->text = $e->getMessage();
        }
    }

    protected function test3()
    {
        $search = new SearchTheme();
        $search->NAME->orderAscending();


        try {
            $themes = $this->getThemeManagementService()->getUnassignedThemes();
            $text = '---><br/>';

            foreach ($themes as $theme) {
                /* @var $theme Theme */
                $text = $text . $theme->getName() . '  -  ' . $theme->getId() . '  -  ' . $theme->getPath() . '<br/>';
                $imgs = $this->getThemeService()->getPresentationImages($theme);
                foreach ($imgs as $img) {
                    /* @var $img Resource */
                    $text = $text . '              ' . $img->getName() . '  -  ' . $img->getId() . '  -  ' . $img->getPath() . '<br/>';
                }
            }

            $this->view->text = $text;
        } catch (\Exception $e) {
            $this->view->text = $e->getMessage();
        }
    }

    protected function test4()
    {
        $search = new SearchTheme();
        $search->NAME->orderAscending();


        try {
            $themes = $this->getThemeManagementService()->getThemes($this->getPublicationService()->findById(2));
            $text = '---><br/>';

            foreach ($themes as $theme) {
                /* @var $theme Theme */
                $text = $text . $theme->getName() . '  -  ' . $theme->getId() . '  -  ' . $theme->getPath() . '<br/>';
                $tpls = $this->getThemeManagementService()->getTemplates($theme);
                foreach ($tpls as $tpl) {
                    /* @var $img Resource */
                    $text = $text . '              ' . $tpl->getName() . '  -  ' . $tpl->getId() . '  -  path:' . $tpl->getPath() . '<br/>';
                }
                $this->getThemeManagementService()->getPresentationImages($theme);
            }

            $this->view->text = $text;
        } catch (\Exception $e) {
            $this->view->text = $e->getMessage();
        }
    }

    protected function test5()
    {
        $search = new SearchTheme();
        $search->NAME->orderAscending();


        try {
            $themes = $this->getThemeManagementService()->getThemes($this->getPublicationService()->findById(2));
            $text = '---><br/>';

            foreach ($themes as $theme) {
                /* @var $theme Theme */
                $text = $text . $theme->getName() . '  -  ' . $theme->getId() . '  -  ' . $theme->getPath() . '<br/>';
                $outputs = $this->getThemeManagementService()->getOutputSettings($theme);
                foreach ($outputs as $out) {
                    /* @var $out OutputSettings */
                    $text = $text . $out->getOutput()->getName() . '------------------<br/>';
                    $text = $text . $out->getFrontPage()->getPath() . '<br/>';
                    $text = $text . $out->getSectionPage()->getPath() . '<br/>';
                    $text = $text . $out->getArticlePage()->getPath() . '<br/>';
                    $text = $text . $out->getErrorPage()->getPath() . '<br/>';
                }
            }

            $this->view->text = $text;
        } catch (\Exception $e) {
            $this->view->text = $e->getMessage();
        }
    }

    protected function test6()
    {
        try {
            $theme1 = $this->getThemeManagementService()->getById(1356059962);
            $pub = $this->getPublicationService()->findById(2);

            $this->getThemeManagementService()->assignTheme($theme1, $pub);
            $text = '---><br/>';

            $this->view->text = $text;
        } catch (\Exception $e) {
            $this->view->text = 'errror<br/>' . $e . '</br>' . $e->getMessage();
        }
    }

    protected function test7()
    {
        try {
            $theme = $this->getThemeManagementService()->getById(1721544697);
            $outss = $this->getThemeManagementService()->getOutputSettings($theme);

            $outs = $outss[0];

            $this->getThemeManagementService()->assignOutputSetting($outs,
            $theme);
            $text = '---><br/>';

            $this->view->text = $text;
        } catch (\Exception $e) {
            $this->view->text = 'errror<br/>' . $e . '</br>' . $e->getMessage();
        }
    }

    protected function test8()
    {
        try {
            echo $this->getThemeManagementService()->installTheme('C:\wamp\www\newscoop\themes\exports\The_Journal.zip');
        } catch (\Exception $e) {
            var_dump($e);
        }
    }

    /**
     * Insert output setting issue Test service
     */
    public function test8Action()
    {
        $this->getHelper('viewRenderer')->setNoRender();
        try {
            $output = $this->getOutputService()->findByName('Web');

            /* @var $issue \Newscoop\Entity\Issue */
            $issue = $this->getIssueService()->getById(1);


            $frontRsc = new Resource();
            $frontRsc->setName('register.tpl');
            $frontRsc->setPath('publication_2/theme_1/register.tpl');
            $frontRsc = $this->getSyncResourceService()->getSynchronized($frontRsc);

            $outputSettingsIssue = new OutputSettingsIssue;

            /* @var $theme \Newscoop\Entity\Theme */
            $theme = $this->getThemeManagementService()->getById(1721544697);

            $themeRsc = new Resource();
            $themeRsc->setName('theme-path');
            $themeRsc->setPath($theme->getPath());
            $themeRsc = $this->getSyncResourceService()->getSynchronized($themeRsc);

            $outputSettingsIssue->setThemePath($themeRsc)
            ->setIssue($issue)
            ->setOutput($output)
            ->setFrontPage($frontRsc);

            $this->getOutputSettingIssueService()->insert($outputSettingsIssue);
        } catch (\Exception $e) {
            echo 'errror<br/>' . $e . '</br>' . $e->getMessage();
        }
    }

    /**
     * Update output setting issue by issue test service
     */
    public function test9Action()
    {
        $this->getHelper('viewRenderer')->setNoRender();
        try {
            $output = $this->getOutputService()->findByName('Web');

            /* @var $issue \Newscoop\Entity\Issue */
            $issue = $this->getIssueService()->findById(1);


            $frontRsc = new Resource();
            $frontRsc->setName('register.tpl');
            $frontRsc->setPath('publication_2/theme_1/register.tpl');
            $frontRsc = $this->getSyncResourceService()->getSynchronized($frontRsc);

            $outputSettingsIssue = $this->getOutputSettingIssueService()->findById(2);
            if (is_null($outputSettingsIssue)) {
                echo 'errror<br/404></br>Not found';
                return;
            }
            /* @var $theme \Newscoop\Entity\Theme */
            $theme = $this->getThemeManagementService()->getById(1721544697);

            $themeRsc = new Resource();
            $themeRsc->setName('theme-path');
            $themeRsc->setPath($theme->getPath());
            $themeRsc = $this->getSyncResourceService()->getSynchronized($themeRsc);

            $outputSettingsIssue->setThemePath($themeRsc)
            ->setIssue($issue)
            ->setOutput($output)
            ->setFrontPage($frontRsc);

            $this->getOutputSettingIssueService()->update($outputSettingsIssue);
        } catch (\Exception $e) {
            echo 'errror<br/>' . $e . '</br>' . $e->getMessage();
        }
    }

    /**
     * List output setting issue by issue test service
     */
    public function test10Action()
    {
        $this->getHelper('viewRenderer')->setNoRender();
        try {
            $output = $this->getOutputService()->findByName('Web');
            /* @var $issue \Newscoop\Entity\Issue */
            $issue = $this->getIssueService()->findById(1);
            $results = $this->getOutputSettingIssueService()->findbyIssue($issue);
            if (count($results)) {
                foreach ($results as $outputSettingIssue) {
                    /* @var $outputSettingIssue OutputSettingsIssue */
                    echo $outputSettingIssue->getOutput()->getName(), '---';
                    echo "<br/>";
                }
            }
        } catch (\Exception $e) {
            echo 'errror<br/>' . $e . '</br>' . $e->getMessage();
        }
    }

    /**
     * Delete output setting issue by issue test service
     */
    public function test11Action()
    {
        $this->getHelper('viewRenderer')->setNoRender();
        try {
            $outputSetting = $this->getOutputSettingIssueService()->findById(1);
            $this->getOutputSettingIssueService()->delete($outputSetting);
        } catch (\Exception $e) {
            echo 'errror<br/>' . $e . '</br>' . $e->getMessage();
        }
    }

    /**
     * Insert output setting section Test service
     */
    public function test12Action()
    {
        $this->getHelper('viewRenderer')->setNoRender();
        try {
            $output = $this->getOutputService()->findByName('Web');

            /* @var $section \Newscoop\Entity\Section */
            $section = $this->getSectionService()->findById(1);

            $frontRsc = new Resource();
            $frontRsc->setName('register.tpl');
            $frontRsc->setPath('publication_2/theme_1/register.tpl');
            $frontRsc = $this->getSyncResourceService()->getSynchronized($frontRsc);

            $outputSettingsSection = new OutputSettingsSection;

            /* @var $theme \Newscoop\Entity\Theme */
            $theme = $this->getThemeManagementService()->getById(1721544697);

            $themeRsc = new Resource();
            $themeRsc->setName('theme-path');
            $themeRsc->setPath($theme->getPath());
            $themeRsc = $this->getSyncResourceService()->getSynchronized($themeRsc);

            $outputSettingsSection
            ->setSection($section)
            ->setOutput($output)
            ->setFrontPage($frontRsc);

            $this->getOutputSettingSectionService()->insert($outputSettingsSection);
        } catch (\Exception $e) {
            echo 'errror<br/>' . $e . '</br>' . $e->getMessage();
        }
    }

    /**
     * Update output setting section by section test service
     */
    public function test13Action()
    {
        $this->getHelper('viewRenderer')->setNoRender();
        try {
            $output = $this->getOutputService()->findByName('Web');

            /* @var $section \Newscoop\Entity\Section */
            $section = $this->getSectionService()->findById(3);

            $frontRsc = new Resource();
            $frontRsc->setName('register.tpl');
            $frontRsc->setPath('publication_2/theme_1/register.tpl');
            $frontRsc = $this->getSyncResourceService()->getSynchronized($frontRsc);

            $outputSettingsSection = $this->getOutputSettingSectionService()->getById(1);

            /* @var $theme \Newscoop\Entity\Theme */
            $theme = $this->getThemeManagementService()->getById(1721544697);

            $themeRsc = new Resource();
            $themeRsc->setName('theme-path');
            $themeRsc->setPath($theme->getPath());
            $themeRsc = $this->getSyncResourceService()->getSynchronized($themeRsc);

            $outputSettingsSection
            ->setSection($section)
            ->setOutput($output)
            ->setFrontPage($frontRsc);

            $this->getOutputSettingSectionService()->insert($outputSettingsSection);
        } catch (\Exception $e) {
            echo 'errror<br/>' . $e . '</br>' . $e->getMessage();
        }
    }

    /**
     * List output setting issue by section test service
     */
    public function test14Action()
    {
        $this->getHelper('viewRenderer')->setNoRender();
        try {
            $output = $this->getOutputService()->findByName('Web');
            /* @var $issue \Newscoop\Entity\Issue */
            $section = $this->getSectionService()->findById(1);
            $results = $this->getOutputSettingSectionService()->findbySection($section);
            if (count($results)) {
                foreach ($results as $result) {
                    /* @var $result OutputSettingsSection */
                    echo $result->getOutput()->getName(), '---';
                    echo "<br/>";
                }
            }
        } catch (\Exception $e) {
            echo 'errror<br/>' . $e . '</br>' . $e->getMessage();
        }
    }

    /**
     * Delete output setting section by issue test service
     */
    public function test15Action()
    {
        $this->getHelper('viewRenderer')->setNoRender();
        try {
            $outputSetting = $this->getOutputSettingSectionService()->findById(1);
            $this->getOutputSettingSectionService()->delete($outputSetting);
        } catch (\Exception $e) {
            echo 'errror<br/>' . $e . '</br>' . $e->getMessage();
        }
    }

    /**
     * Search by an issue test service
     */
    public function test16Action()
    {
        $this->getHelper('viewRenderer')->setNoRender();
        try {
            $output = $this->getOutputService()->findByName('Web');

            /* @var $issue \Newscoop\Entity\Issue */
            $issue = $this->getIssueService()->findById(1);
            /* @var $section \Newscoop\Entity\Section */
            $section = $this->getSectionService()->findById(10);

            $return = $this->getTemplateSearchService()->getArticlePage($section, $output);
            var_dump($return);
        } catch (\Exception $e) {
            echo 'errror<br/>' . $e . '</br>' . $e->getMessage();
        }
    }

}

?>