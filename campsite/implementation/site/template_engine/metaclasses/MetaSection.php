<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
// is not defined in these cases.
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/classes/Section.php');
require_once($g_documentRoot.'/template_engine/metaclasses/MetaDbObject.php');

/**
 * @package Campsite
 */
final class MetaSection extends MetaDbObject {

	private function InitProperties()
	{
		if (!is_null($this->m_properties)) {
			return;
		}
		$this->m_properties['name'] = 'Name';
		$this->m_properties['number'] = 'Number';
		$this->m_properties['description'] = 'Description';
        $this->m_properties['url_name'] = 'ShortName';
	}


    public function __construct($p_publicationId = null, $p_issueNumber = null,
                                $p_languageId = null, $p_sectionNumber = null)
    {
		$this->m_dbObject = new Section($p_publicationId, $p_issueNumber,
										$p_languageId, $p_sectionNumber);
        if (!$this->m_dbObject->exists()) {
            $this->m_dbObject = new Section();
        }

		$this->InitProperties();
		$this->m_customProperties['template'] = 'getTemplate';
        $this->m_customProperties['publication'] = 'getPublication';
        $this->m_customProperties['issue'] = 'getIssue';
        $this->m_customProperties['language'] = 'getLanguage';
        $this->m_customProperties['defined'] = 'defined';
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