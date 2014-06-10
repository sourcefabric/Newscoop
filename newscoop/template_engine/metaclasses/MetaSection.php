<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/Section.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/metaclasses/MetaDbObject.php');

/**
 * @package Campsite
 */
final class MetaSection extends MetaDbObject {
	private static $m_baseProperties = array(
	'name'=>'Name',
    'number'=>'Number',
    'description'=>'Description',
    'url_name'=>'ShortName',
    'identifier' => 'id'
	);

	private static $m_defaultCustomProperties = array(
	'template'=>'getTemplate',
    'publication'=>'getPublication',
    'issue'=>'getIssue',
    'language'=>'getLanguage',
    'defined'=>'defined'
	);


    public function __construct($p_publicationId = null, $p_issueNumber = null, $p_languageId = null, $p_sectionNumber = null)
    {
        $this->m_properties = self::$m_baseProperties;
        $this->m_customProperties = self::$m_defaultCustomProperties;

        $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
        $cacheKey = $cacheService->getCacheKey(array('section', $p_publicationId, $p_issueNumber, $p_languageId, $p_sectionNumber), 'section');
        if ($cacheService->contains($cacheKey)) {
             $this->m_dbObject = $cacheService->fetch($cacheKey);
        } else {
            $this->m_dbObject = new Section($p_publicationId, $p_issueNumber, $p_languageId, $p_sectionNumber);

            if ($p_publicationId && $p_issueNumber && $p_languageId && $p_sectionNumber) {
                 $cacheService->save($cacheKey, $this->m_dbObject);
            }
        }

        if (!$this->m_dbObject->exists() && !is_null($p_sectionNumber)) {
            $this->m_dbObject = new Section();
        }

        $this->m_skipFilter = array('description');
    } // fn __construct


    protected function getTemplate()
    {
    	if ($this->m_dbObject->getSectionTemplateId() > 0) {
   			return new MetaTemplate($this->m_dbObject->getSectionTemplateId());
    	}
    	$sectionIssue = new Issue($this->m_dbObject->getProperty('IdPublication'),
    							  $this->m_dbObject->getProperty('IdLanguage'),
    							  $this->m_dbObject->getProperty('NrIssue'));
   		return new MetaTemplate($sectionIssue->getSectionTemplateId());
    }


    protected function getPublication()
    {
        return new MetaPublication($this->m_dbObject->getPublicationId());
    }


    protected function getLanguage()
    {
        return new MetaLanguage($this->m_dbObject->getLanguageId());
    }


    protected function getIssue()
    {
        return new MetaIssue($this->m_dbObject->getPublicationId(),
                             $this->m_dbObject->getLanguageId(),
                             $this->m_dbObject->getIssueNumber());
    }

} // class MetaSection

?>