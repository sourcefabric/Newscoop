<?php

/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @author Mugur Rus <mugur.rus@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */
use Newscoop\Service\Resource\ResourceId;
use Newscoop\Service\ITemplateSearchService;
use Newscoop\Service\IOutputService;
use Newscoop\Service\IOutputSettingIssueService;

/**
 * Class CampSystem
 */
abstract class CampSystem
{

    /**
     *
     */
    abstract protected function dispatch();

    /**
     *
     */
    abstract protected function render();

    /**
     * Reads a configuration setting.
     *
     * @param string $p_varName
     *
     * @return mixed
     *      The value of the configuration variable
     */
    protected function getSetting($p_varName)
    {
        $config = CampSite::GetConfigInstance();
        return $config->getSetting($p_varName);
    }// fn getSetting

    /**
     * Sets the context language object.
     *
     * @param integer $p_lngId
     *      The language identifier
     *
     * @return void
     */
    protected function setLanguage($p_lngId)
    {
        $context = CampTemplate::singleton()->context();
        if (is_object($context->language)
                && $context->language->number == $p_lngId) {
            return;
        }
        $context->language = new MetaLanguage($p_lngId);
    }// fn setLanguage

    /**
     * Sets the context publication object.
     *
     * @param integer $p_pubId
     *      The publication identifier
     *
     * @return void
     */
    protected function setPublication($p_pubId)
    {
        $context = CampTemplate::singleton()->context();
        if (is_object($context->publication)
                && $context->publication->identifier == $p_pubId) {
            return;
        }
        $context->publication = new MetaPublication($p_pubId);
    }// fn setPublication

    /**
     * Sets the context issue object.
     *
     * @param integer $p_pubId
     *      The publication identifier
     * @param integer $p_lngId
     *      The language identifier
     * @param integer $p_issNr
     *      The issue number
     *
     * @return void
     */
    protected function setIssue($p_pubId, $p_lngId, $p_issNr)
    {
        $context = CampTemplate::singleton()->context();
        if (is_object($context->issue)
                && $context->issue->number == $p_issNr) {
            return;
        }
        $context->issue = new MetaIssue($p_pubId, $p_lngId, $p_issNr);
    }// fn setIssue

    /**
     * Sets the context section object.
     *
     * @param integer $p_pubId
     *      The publication identifier
     * @param integer $p_issNr
     *      The issue number
     * @param integer $p_lngId
     *      The language identifier
     * @param integer $p_sctNr
     *      The section number
     *
     * @return void
     */
    protected function setSection($p_pubId, $p_issNr, $p_lngId, $p_sctNr)
    {
        $context = CampTemplate::singleton()->context();
        if (is_object($context->section)
                && $context->section->number == $p_sctNr) {
            return;
        }
        $context->section = new MetaSection($p_pubId, $p_issNr, $p_lngId, $p_sctNr);
    }// fn setSection

    /**
     * Sets the context article object.
     *
     * @param integer $p_lngId
     *      The language identifier
     * @param integer $p_artNr
     *      The article number
     *
     * @return void
     */
    protected function setArticle($p_lngId, $p_artNr)
    {
        $context = CampTemplate::singleton()->context();
        if (is_object($context->article)
                && $context->article->number == $p_artNr) {
            return;
        }
        $context->article = new MetaArticle($p_lngId, $p_artNr);
    }// fn setArticle


    public static function GetInvalidURLTemplate($p_pubId, $p_issNr = NULL, $p_lngId = NULL, $p_isPublished = true)
    {
        global $g_ado_db;
        $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
        if (CampCache::IsEnabled()) {
            $paramString = $p_lngId . '_' . $p_pubId . '_' . $p_issNr;
            $cacheKey = __CLASS__ . '_IssueTemplate_' . $paramString;
            $issueTemplate = CampCache::singleton()->fetch($cacheKey);
            if ($issueTemplate !== false && !empty($issueTemplate)) {
                return $issueTemplate;
            }
        }
        $publication = null;
        if (is_null($p_lngId)) {
            $publication = new Publication($p_pubId);
            if (!$publication->exists()) {
                $template = null;
            }
            $p_lngId = $publication->getLanguageId();
        }
        if (is_null($p_issNr) && (!is_null($publication) || !is_null($p_pubId))) {
            if (is_null($publication)) {
                $publication = new Publication($p_pubId);
            }
            $lastIssue = self::GetLastIssue($publication, $p_lngId, $p_isPublished);
            if (is_null($lastIssue)) {
                $template = null;
            }
            $p_issNr = $lastIssue[0];
        }
        if (!is_null($p_issNr)) {
            $resourceId = new ResourceId('template_engine/classes/CampSystem');
            $outputService = $resourceId->getService(IOutputService::NAME);

            if (!\Zend_Registry::isRegistered('webOutput')) {
                $cacheKeyWebOutput = $cacheService->getCacheKey(array('OutputService', 'Web'), 'outputservice');
                if ($cacheService->contains($cacheKeyWebOutput)) {
                    \Zend_Registry::set('webOutput', $cacheService->fetch($cacheKeyWebOutput));
                } else {
                    $webOutput = $outputService->findByName('Web');
                    $cacheService->save($cacheKeyWebOutput, $webOutput);
                    \Zend_Registry::set('webOutput', $webOutput);
                }
            }

            $templateSearchService = $resourceId->getService(ITemplateSearchService::NAME);

            $issueObj = new Issue($p_pubId, $p_lngId, $p_issNr);
            $template = $templateSearchService->getErrorPage($issueObj->getIssueId(), Zend_Registry::get('webOutput'));
        }

        if (empty($template)) {
            $template = null;
        }
        if (CampCache::IsEnabled()) {
            CampCache::singleton()->store($cacheKey, $template);
        }
        return $template;
    }// fn GetInvalidURLTemplate


    /**
     * Get theme base path
     *
     * @param int $p_lngId
     * @param int $p_pubId
     * @param int $p_issNr
     */
    public static function GetThemePath($p_lngId, $p_pubId, $p_issNr)
    {
        $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');

        if (empty($p_lngId) || empty($p_issNr)) {
            $cacheKey = $cacheService->getCacheKey('legacy_publication'.$p_pubId, 'publication');
            if ($cacheService->contains($cacheKey)) {
                $publication = $cacheService->fetch($cacheKey);
            } else {
                $publication = new Publication($p_pubId);
                $cacheService->save($cacheKey, $publication);
            }

            $issue = self::GetLastIssue($publication, $p_lngId);
            if (is_null($issue)) {
                $issue = self::GetLastIssue($publication);
                if (is_null($issue)) {
                    return null;
                }
            }
            $p_issNr = array_shift($issue);
            $p_lngId = array_shift($issue);
        }

        $cacheKeyThemePath = $cacheService->getCacheKey(array('getThemePath', $p_lngId, $p_pubId, $p_issNr), 'issue');
        if ($cacheService->contains($cacheKeyThemePath)) {
            $themePath = $cacheService->fetch($cacheKeyThemePath);
        } else {
            $cacheKey = $cacheService->getCacheKey(array('issue', $p_pubId, $p_lngId, $p_issNr), 'issue');
            if ($cacheService->contains($cacheKey)) {
                $issueObj = $cacheService->fetch($cacheKey);
            } else {
                $issueObj = new Issue($p_pubId, $p_lngId, $p_issNr);
                $cacheService->save($cacheKey, $issueObj);
            }

            $resourceId = new ResourceId('template_engine/classes/CampSystem');
            $outputService = $resourceId->getService(IOutputService::NAME);

            if (!\Zend_Registry::isRegistered('webOutput')) {
                $cacheKeyWebOutput = $cacheService->getCacheKey(array('OutputService', 'Web'), 'outputservice');
                if ($cacheService->contains($cacheKeyWebOutput)) {
                    \Zend_Registry::set('webOutput', $cacheService->fetch($cacheKeyWebOutput));
                } else {
                    $webOutput = $outputService->findByName('Web');
                    $cacheService->save($cacheKeyWebOutput, $webOutput);
                    \Zend_Registry::set('webOutput', $webOutput);
                }
            }

            $cacheKeyOutSetIssues = $cacheService->getCacheKey(array('outSetIssues', $issueObj->getIssueId(), 'webOutput'));
            if ($cacheService->contains($cacheKeyOutSetIssues)) {
                $outSetIssues = $cacheService->fetch($cacheKeyOutSetIssues);
            } else {
                $outputSettingIssueService = $resourceId->getService(IOutputSettingIssueService::NAME);
                $outSetIssues = $outputSettingIssueService->findByIssueAndOutput($issueObj->getIssueId(), \Zend_Registry::get('webOutput'));
                $cacheService->save($cacheKeyOutSetIssues, $outSetIssues);
            }

            $outputSettingIssueService = $resourceId->getService(IOutputSettingIssueService::NAME);
            $outSetIssues = $outputSettingIssueService->findByIssueAndOutput($issueObj->getIssueId(), \Zend_Registry::get('webOutput'));
            if(!is_null($outSetIssues)) {
                $themePath = $outSetIssues->getThemePath()->getPath();
            } else {
                $themePath = null;
            }
            $cacheService->save($cacheKeyThemePath, $themePath);
        }

        return $themePath;
    }

    /**
     *
     */
    public static function GetTemplate($p_lngId, $p_pubId, $p_issNr, $p_sctNr,
            $p_artNr, $p_isPublished = true)
    {
        $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');

        global $g_ado_db;
        if ($p_lngId <= 0) {
            $cacheKey = $cacheService->getCacheKey('legacy_publication'.$p_pubId, 'publication');
            if ($cacheService->contains($cacheKey)) {
                $publication = $cacheService->fetch($cacheKey);
            } else {
                $publication = new Publication($p_pubId);
                $cacheService->save($cacheKey, $publication);
            }
            
            if (!$publication->exists()) {
                return null;
            }
            $p_lngId = $publication->getLanguageId();
        }
        if ($p_artNr > 0) {
            if ($p_issNr <= 0 || $p_sctNr <= 0) {
                $article = new Article($p_lngId, $p_artNr);
                if (!$article->exists()
                        || ($p_isPublished && !$article->isPublished())) {
                    return self::GetInvalidURLTemplate($p_pubId, $p_issNr, $p_lngId, $p_isPublished);
                }
                $p_issNr = $article->getIssueNumber();
                $p_sctNr = $article->getSectionNumber();
            }
            return self::GetArticleTemplate($p_lngId, $p_pubId, $p_issNr, $p_sctNr);
        }
        if ($p_sctNr > 0) {
            if ($p_issNr <= 0) {
                $sql = 'SELECT MAX(i.Number) AS Number '
                        . 'FROM Sections as s, Issues as i '
                        . 'WHERE s.IdPublication = i.IdPublication'
                        . ' AND s.IdLanguage = i.IdLanguage'
                        . ' AND s.IdPublication = ' . (int)$p_pubId
                        . ' AND s.IdLanguage = ' . (int)$p_lngId;
                if ($p_isPublished == true) {
                    $sql .= " AND i.Published = 'Y'";
                }
                $data = $g_ado_db->GetOne($sql);
                if (empty($data)) {
                    return null;
                }
                $p_issNr = $data;
            }
            return self::GetSectionTemplate($p_lngId, $p_pubId, $p_issNr,
                    $p_sctNr);
        }
        if ($p_issNr <= 0) {
            $sql = 'SELECT MAX(Number) AS Number FROM Issues '
                    . 'WHERE IdPublication = ' . $p_pubId
                    . ' AND IdLanguage = ' . $p_lngId;
            if ($p_isPublished == true) {
                $sql .= " AND Published = 'Y'";
            }
            $data = $g_ado_db->GetOne($sql);
            if (empty($data)) {
                return null;
            }
            $p_issNr = $data;
        }
        return self::GetIssueTemplate($p_lngId, $p_pubId, $p_issNr);
    }// fn GetTemplate

    public static function GetLastIssue(Publication $publication, $p_langId = null, $p_isPublished = true)
    {
        global $g_ado_db;

        if (!$publication->exists()) {
            return null;
        }
        if (empty($p_langId)) {
            $p_langId = $publication->getDefaultLanguageId();
        }
        $sql = 'SELECT MAX(Number) AS Number FROM Issues '
        . 'WHERE IdPublication = ' . (int)$publication->getPublicationId()
        . ' AND IdLanguage = ' . (int)$p_langId;
        if ($p_isPublished == true) {
            $sql .= " AND Published = 'Y'";
        }
        $issueNo = $g_ado_db->GetOne($sql);
        if (empty($issueNo)) {
            return null;
        }
        return array($issueNo, $p_langId);
    }

    public static function GetIssueTemplate($p_lngId, $p_pubId, $p_issNr)
    {
        global $g_ado_db;

        $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
        $cacheKey = $cacheService->getCacheKey(array('GetIssueTemplate', $p_lngId, $p_pubId, $p_issNr), 'issue');
        if ($cacheService->contains($cacheKey)) {
            return $cacheService->fetch($cacheKey);
        } else {
            $resourceId = new ResourceId('template_engine/classes/CampSystem');
            $outputService = $resourceId->getService(IOutputService::NAME);

            if (!\Zend_Registry::isRegistered('webOutput')) {
                $cacheKeyWebOutput = $cacheService->getCacheKey(array('OutputService', 'Web'), 'outputservice');
                if ($cacheService->contains($cacheKeyWebOutput)) {
                    \Zend_Registry::set('webOutput', $cacheService->fetch($cacheKeyWebOutput));
                } else {
                    $webOutput = $outputService->findByName('Web');
                    $cacheService->save($cacheKeyWebOutput, $webOutput);
                    \Zend_Registry::set('webOutput', $webOutput);
                }
            }

            /* @var $templateSearchService ITemplateSearchService */
            $templateSearchService = $resourceId->getService(ITemplateSearchService::NAME);

            $issueObj = new Issue($p_pubId, $p_lngId, $p_issNr);
            $data = $templateSearchService->getFrontPage($issueObj->getIssueId(),
                \Zend_Registry::get('webOutput'));

            if (empty($data)) {
                $data = self::GetInvalidURLTemplate($p_pubId, $p_issNr, $p_lngId);
            }
            $cacheService->save($cacheKey, $data);
            return $data;
        }
    }// fn GetIssueTemplate

    public static function GetSectionTemplate($p_lngId, $p_pubId, $p_issNr, $p_sctNr)
    {
        global $g_ado_db;
        $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
        $cacheKey = $cacheService->getCacheKey(array('GetSectionTemplate', $p_lngId, $p_pubId, $p_issNr, $p_sctNr), 'section');
        if ($cacheService->contains($cacheKey)) {
            return $cacheService->fetch($cacheKey);
        } else {
            $resourceId = new ResourceId('template_engine/classes/CampSystem');
            $resourceId = new ResourceId('template_engine/classes/CampSystem');
            $outputService = $resourceId->getService(IOutputService::NAME);

            if (!\Zend_Registry::isRegistered('webOutput')) {
                $cacheKeyWebOutput = $cacheService->getCacheKey(array('OutputService', 'Web'), 'outputservice');
                if ($cacheService->contains($cacheKeyWebOutput)) {
                    \Zend_Registry::set('webOutput', $cacheService->fetch($cacheKeyWebOutput));
                } else {
                    $webOutput = $outputService->findByName('Web');
                    $cacheService->save($cacheKeyWebOutput, $webOutput);
                    \Zend_Registry::set('webOutput', $webOutput);
                }
            }

            $templateSearchService = $resourceId->getService(ITemplateSearchService::NAME);

            $sectionObj = new Section($p_pubId, $p_issNr, $p_lngId, $p_sctNr);
            $data = $templateSearchService->getSectionPage($sectionObj->getSectionId(),
                \Zend_Registry::get('webOutput'));
            if (empty($data)) {
                $data = self::GetInvalidURLTemplate($p_pubId, $p_issNr, $p_lngId);
            }
            $cacheService->save($cacheKey, $data);
            return $data;
        }
    }// fn GetSectionTemplate

    public static function GetArticleTemplate($p_lngId, $p_pubId, $p_issNr, $p_sctNr)
    {
        global $g_ado_db;
        $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
        $cacheKey = $cacheService->getCacheKey(array('GetArticleTemplate', $p_lngId, $p_pubId, $p_issNr, $p_sctNr), 'article');
        if ($cacheService->contains($cacheKey)) {
            return $cacheService->fetch($cacheKey);
        } else {
            $resourceId = new ResourceId('template_engine/classes/CampSystem');
            $outputService = $resourceId->getService(IOutputService::NAME);

            if (!\Zend_Registry::isRegistered('webOutput')) {
                $cacheKeyWebOutput = $cacheService->getCacheKey(array('OutputService', 'Web'), 'outputservice');
                if ($cacheService->contains($cacheKeyWebOutput)) {
                    \Zend_Registry::set('webOutput', $cacheService->fetch($cacheKeyWebOutput));
                } else {
                    $webOutput = $outputService->findByName('Web');
                    $cacheService->save($cacheKeyWebOutput, $webOutput);
                    \Zend_Registry::set('webOutput', $webOutput);
                }
            }

            $templateSearchService = $resourceId->getService(ITemplateSearchService::NAME);

            $sectionObj = new Section($p_pubId, $p_issNr, $p_lngId, $p_sctNr);
            $data = $templateSearchService->getArticlePage($sectionObj->getSectionId(),
                \Zend_Registry::get('webOutput'));

            if (empty($data)) {
                $data = self::GetInvalidURLTemplate($p_pubId, $p_issNr, $p_lngId);
            }
            $cacheService->save($cacheKey, $data);
            return $data;
        }// fn GetArticleTemplate
    }
}// class CampSystem
?>