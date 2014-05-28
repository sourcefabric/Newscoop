<?php
/**
 * @package Campsite
 */

use Newscoop\Service\Resource\ResourceId;
use Newscoop\Service\IOutputSettingIssueService;

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/Issue.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/metaclasses/MetaDbObject.php');

/**
 * @package Campsite
 */
final class MetaIssue extends MetaDbObject {

	private static $m_baseProperties = array(
	'id'=>'id',
	'name'=>'Name',
    'number'=>'Number',
    'date'=>'PublicationDate',
    'publish_date'=>'PublicationDate',
    'url_name'=>'ShortName'
	);

	private static $m_defaultCustomProperties = array(
    'year'=>'getPublishYear',
    'mon'=>'getPublishMonth',
    'wday'=>'getPublishWeekDay',
    'mday'=>'getPublishMonthDay',
    'yday'=>'getPublishYearDay',
    'hour'=>'getPublishHour',
    'min'=>'getPublishMinute',
    'sec'=>'getPublishSecond',
    'mon_name'=>'getPublishMonthName',
    'wday_name'=>'getPublishWeekDayName',
    'template'=>'getTemplate',
    'publication'=>'getPublication',
    'language'=>'getLanguage',
    'is_current'=>'isCurrent',
    'is_published'=>'isPublished',
    'defined'=>'defined',
    'theme_path'=>'getThemePath'
	);


    public function __construct($p_publicationId = null, $p_languageId = null,
    $p_issueNumber = null)
    {
        $this->m_properties = self::$m_baseProperties;
        $this->m_customProperties = self::$m_defaultCustomProperties;

        $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
        $cacheKey = $cacheService->getCacheKey(array('issue', $p_publicationId, $p_languageId, $p_issueNumber), 'issue');
        if ($cacheService->contains($cacheKey)) {
             $this->m_dbObject = $cacheService->fetch($cacheKey);
        } else {
            $this->m_dbObject = new Issue($p_publicationId, $p_languageId, $p_issueNumber);
            $cacheService->save($cacheKey, $this->m_dbObject);
        }

        if (!$this->m_dbObject->exists()) {
            $this->m_dbObject = new Issue();
        }
    } // fn __construct


    protected function getThemePath()
    {
        $resourceId = new ResourceId(__CLASS__);
        $outSetIssueService = $resourceId->getService(IOutputSettingIssueService::NAME);
        $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
        $cacheKey = $cacheService->getCacheKey(array('outSets1', $this->m_dbObject->getIssueId()), 'issue');
        if ($cacheService->contains($cacheKey)) {
            return $cacheService->fetch($cacheKey);
        } else {
            $outSets = $outSetIssueService->findByIssue($this->m_dbObject->getIssueId());
            if (count($outSets) == 0) {
                return null;
            }
            $themePath = $outSets[0]->getThemePath()->getPath();
            $cacheService->save($cacheKey, $themePath);
            return $themePath;
        }
    }


    /**
     * Returns a list of MetaLanguage objects - list of languages in which
     * the issue was translated.
     *
     * @param boolean $p_excludeCurrent
     * @param array $p_order
     * @param boolean $p_allIssues
     * @return array of MetaLanguage
     */
    public function languages_list($p_excludeCurrent = true,
    array $p_order = array(), $p_allIssues = false) {
        $languages = $this->m_dbObject->getLanguages(false, $p_excludeCurrent,
        $p_order, $p_allIssues, !CampTemplate::singleton()->context()->preview);
        $metaLanguagesList = array();
        foreach ($languages as $language) {
            $metaLanguagesList[] = new MetaLanguage($language->getLanguageId());
        }
        return $metaLanguagesList;
    }


    protected function getPublishYear()
    {
        $publish_timestamp = strtotime($this->m_dbObject->getProperty('PublicationDate'));
        $publish_date_time = getdate($publish_timestamp);
        return $publish_date_time['year'];
    }


    protected function getPublishMonth()
    {
        $publish_timestamp = strtotime($this->m_dbObject->getProperty('PublicationDate'));
        $publish_date_time = getdate($publish_timestamp);
        return $publish_date_time['mon'];
    }


    protected function getPublishWeekDay()
    {
        $publish_timestamp = strtotime($this->m_dbObject->getProperty('PublicationDate'));
        $publish_date_time = getdate($publish_timestamp);
        return $publish_date_time['wday'];
    }


    protected function getPublishMonthDay()
    {
        $publish_timestamp = strtotime($this->m_dbObject->getProperty('PublicationDate'));
        $publish_date_time = getdate($publish_timestamp);
        return $publish_date_time['mday'];
    }


    protected function getPublishYearDay()
    {
        $publish_timestamp = strtotime($this->m_dbObject->getProperty('PublicationDate'));
        $publish_date_time = getdate($publish_timestamp);
        return $publish_date_time['yday'];
    }


    protected function getPublishHour()
    {
        $publish_timestamp = strtotime($this->m_dbObject->getProperty('PublicationDate'));
        $publish_date_time = getdate($publish_timestamp);
        return $publish_date_time['hours'];
    }


    protected function getPublishMinute()
    {
        $publish_timestamp = strtotime($this->m_dbObject->getProperty('PublicationDate'));
        $publish_date_time = getdate($publish_timestamp);
        return $publish_date_time['minutes'];
    }


    protected function getPublishSecond()
    {
        $publish_timestamp = strtotime($this->m_dbObject->getProperty('PublicationDate'));
        $publish_date_time = getdate($publish_timestamp);
        return $publish_date_time['seconds'];
    }


    protected function getPublishMonthName() {
        $dateTime = new MetaDatetime($this->m_dbObject->getProperty('PublicationDate'));
        return $dateTime->getMonthName();
    }


    protected function getPublishWeekDayName() {
        $dateTime = new MetaDatetime($this->m_dbObject->getProperty('PublicationDate'));
        return $dateTime->getWeekDayName();
    }


    protected function getTemplate()
    {
        return new MetaTemplate($this->m_dbObject->getIssueTemplateId());
    }


    protected function getPublication()
    {
        return new MetaPublication($this->m_dbObject->getPublicationId());
    }


    protected function getLanguage()
    {
        return new MetaLanguage($this->m_dbObject->getLanguageId());
    }


    protected function isCurrent() {
        $currentIssue = Issue::GetCurrentIssue($this->m_dbObject->getPublicationId(),
        $this->m_dbObject->getLanguageId());
        return !is_null($currentIssue)
        && $currentIssue->getIssueNumber() == $this->m_dbObject->getIssueNumber();
    }


    protected function isPublished() {
    	return $this->m_dbObject->isPublished();
    }

} // class MetaIssue

?>