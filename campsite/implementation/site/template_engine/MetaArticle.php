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

require_once($g_documentRoot.'/classes/Article.php');
require_once($g_documentRoot.'/classes/ArticleAttachment.php');
require_once($g_documentRoot.'/classes/Template.php');
require_once($g_documentRoot.'/classes/Language.php');
require_once($g_documentRoot.'/template_engine/MetaDbObject.php');


/**
 * @package Campsite
 */
final class MetaArticle extends MetaDbObject {
	private $m_articleData = null;

	private $m_state = null;


	private function InitProperties()
	{
		if (!is_null($this->m_properties)) {
			return;
		}
		$this->m_properties['number'] = 'Number';
		$this->m_properties['type_name'] = 'Type';
		$this->m_properties['name'] = 'Name';
		$this->m_properties['publish_date'] = 'PublishDate';
		$this->m_properties['creation_date'] = 'UploadDate';
		$this->m_properties['keywords'] = 'Keywords';
		$this->m_properties['url_name'] = 'ShortName';
		$this->m_properties['comments_enabled'] = 'comments_enabled';
		$this->m_properties['comments_locked'] = 'comments_locked';
		$this->m_properties['last_update'] = 'time_updated';
	}


    public function __construct($p_languageId, $p_articleId)
    {
        $articleObj =& new Article($p_languageId, $p_articleId);

        if (!is_object($articleObj) || !$articleObj->exists()) {
            return false;
        }
        $this->m_dbObject =& $articleObj;
        $this->InitProperties();

        $this->m_articleData =& new ArticleData($articleObj->getType(),
                                                $articleObj->getArticleNumber(),
                                                $articleObj->getLanguageId());

        foreach ($this->m_articleData->m_columnNames as $property) {
        	if ($property[0] != 'F') {
        		continue;
        	}
        	$property = substr($property, 1);
        	$this->m_customProperties[strtolower($property)] = array($property);
        }
        $this->m_customProperties['year'] = 'getCreationYear';
        $this->m_customProperties['mon'] = 'getCreationMonth';
        $this->m_customProperties['wday'] = 'getCreationWeekDay';
        $this->m_customProperties['mday'] = 'getCreationMonthDay';
        $this->m_customProperties['yday'] = 'getCreationYearDay';
        $this->m_customProperties['hour'] = 'getCreationHour';
        $this->m_customProperties['min'] = 'getCreationMinute';
        $this->m_customProperties['sec'] = 'getCreationSecond';
        $this->m_customProperties['on_front_page'] = 'getOnFrontPage';
        $this->m_customProperties['on_section_page'] = 'getOnSectionPage';
        $this->m_customProperties['is_published'] = 'getIsPublished';
        $this->m_customProperties['is_public'] = 'getIsPublic';
        $this->m_customProperties['is_indexed'] = 'getIsIndexed';
        $this->m_customProperties['publication'] = 'getPublication';
        $this->m_customProperties['issue'] = 'getIssue';
        $this->m_customProperties['section'] = 'getSection';
        $this->m_customProperties['language'] = 'getLanguage';
        $this->m_customProperties['owner'] = 'getOwner';
        $this->m_customProperties['template'] = 'getTemplate';
        $this->m_customProperties['defined'] = 'defined';
        $this->m_customProperties['has_attachments'] = 'hasAttachments';
    } // fn __construct


    final public function __get($p_property)
    {
    	global $tpl;

    	if ($this->m_state == 'type_name_error') {
    		$this->m_state = null;
    		return null;
    	}

    	if ($p_property == 'type' && $this->m_state == null) {
    		$this->m_state = 'type';
    		return $this;
    	}

    	if ($this->m_state == 'type') {
    		if ($this->m_dbObject->getType() != $p_property) {
    			$this->m_state = 'type_name_error';
    		} else {
	    		$this->m_state = null;
    		}
    		return $this;
    	}

    	try {
	    	return $this->m_dbObject->getProperty($this->translateProperty($p_property));
    	} catch (InvalidPropertyException $e) {
    		try {
    			return $this->getCustomProperty($p_property);
    		} catch (InvalidPropertyException $e) {
        		$this->trigger_invalid_property_error($p_property);
    			return null;
    		}
    	}
    } // fn __get


    protected function getCustomProperty($p_property)
    {
    	if (isset($this->m_customProperties[strtolower($p_property)])
    			&& is_array($this->m_customProperties[strtolower($p_property)])) {
    		try {
    			$property = $this->m_customProperties[strtolower($p_property)];
	    		return $this->m_articleData->getProperty('F'.$property[0]);
    		} catch (InvalidPropertyException $e) {
    			// do nothing; will throw another exception with original property field name
    		}
    		throw new InvalidPropertyException(get_class($this->m_dbObject), $p_property);
    	}
    	return parent::getCustomProperty($p_property);
    }


    public function getCreationYear()
    {
    	$creation_timestamp = strtotime($this->m_dbObject->getProperty('UploadDate'));
    	$creation_date_time = getdate($creation_timestamp);
    	return $creation_date_time['year'];
    }


    public function getCreationMonth()
    {
    	$creation_timestamp = strtotime($this->m_dbObject->getProperty('UploadDate'));
    	$creation_date_time = getdate($creation_timestamp);
    	return $creation_date_time['mon'];
    }


    public function getCreationWeekDay()
    {
    	$creation_timestamp = strtotime($this->m_dbObject->getProperty('UploadDate'));
    	$creation_date_time = getdate($creation_timestamp);
    	return $creation_date_time['wday'];
    }


    public function getCreationMonthDay()
    {
    	$creation_timestamp = strtotime($this->m_dbObject->getProperty('UploadDate'));
    	$creation_date_time = getdate($creation_timestamp);
    	return $creation_date_time['mday'];
    }


    public function getCreationYearDay()
    {
    	$creation_timestamp = strtotime($this->m_dbObject->getProperty('UploadDate'));
    	$creation_date_time = getdate($creation_timestamp);
    	return $creation_date_time['yday'];
    }


    public function getCreationHour()
    {
    	$creation_timestamp = strtotime($this->m_dbObject->getProperty('UploadDate'));
    	$creation_date_time = getdate($creation_timestamp);
    	return $creation_date_time['hours'];
    }


    public function getCreationMinute()
    {
    	$creation_timestamp = strtotime($this->m_dbObject->getProperty('UploadDate'));
    	$creation_date_time = getdate($creation_timestamp);
    	return $creation_date_time['minutes'];
    }


    public function getCreationSecond()
    {
    	$creation_timestamp = strtotime($this->m_dbObject->getProperty('UploadDate'));
    	$creation_date_time = getdate($creation_timestamp);
    	return $creation_date_time['seconds'];
    }


    public function getOnFrontPage()
    {
    	return $this->m_dbObject->getProperty('OnFrontPage') == 'Y';
    }


    public function getOnSectionPage()
    {
    	return $this->m_dbObject->getProperty('OnSection') == 'Y';
    }


    public function getIsPublished()
    {
    	return $this->m_dbObject->getProperty('Published') == 'Y';
    }


    public function getIsPublic()
    {
    	return $this->m_dbObject->getProperty('Public') == 'Y';
    }


    public function getIsIndexed()
    {
    	return $this->m_dbObject->getProperty('IsIndexed') == 'Y';
    }


    public function getPublication()
    {
    	return new MetaPublication($this->m_dbObject->getProperty('IdPublication'));
    }


    public function getIssue()
    {
    	return new MetaIssue($this->m_dbObject->getProperty('IdPublication'),
    						 $this->m_dbObject->getProperty('IdLanguage'),
    						 $this->m_dbObject->getProperty('NrIssue'));
    }


    public function getSection()
    {
    	return new MetaSection($this->m_dbObject->getProperty('IdPublication'),
    						   $this->m_dbObject->getProperty('NrIssue'),
    						   $this->m_dbObject->getProperty('IdLanguage'),
    						   $this->m_dbObject->getProperty('NrSection'));
    }


    public function getLanguage()
    {
    	return new MetaLanguage($this->m_dbObject->getProperty('IdLanguage'));
    }


    public function getOwner()
    {
    	return new MetaUser($this->m_dbObject->getProperty('IdUser'));
    }


    public function getTemplate()
    {
    	$articleSection =& new Section($this->m_dbObject->getProperty('IdPublication'),
    								   $this->m_dbObject->getProperty('NrIssue'),
    								   $this->m_dbObject->getProperty('IdLanguage'),
    								   $this->m_dbObject->getProperty('NrSection'));
    	if (!is_null($articleSection->getArticleTemplateId())) {
    		return new Template($articleSection->getArticleTemplateId());
    	}
    	$articleIssue =& new Issue($this->m_dbObject->getProperty('IdPublication'),
    							   $this->m_dbObject->getProperty('IdLanguage'),
    							   $this->m_dbObject->getProperty('NrIssue'));
   		return new Template($articleIssue->getArticleTemplateId());
    }


    public function hasAttachments()
    {
    	$attachments = ArticleAttachment::GetAttachmentsByArticleNumber($this->m_dbObject->getProperty('Number'));
    	return (int)(sizeof($attachments) > 0);
    }


    public function translated_to($p_language)
    {
    	if (is_string($p_language)) {
    		$languages = Language::GetLanguages(null, $p_language);
    		if (sizeof($languages) == 0) {
    			return (int)false;
    		}
    		$language = $languages[0];
    	} else {
    		$language = $p_language;
    	}
    	$article =& new Article($language->getLanguageId(), $this->m_dbObject->getArticleNumber());
    	return (int)$article->exists();
    }

} // class MetaArticle

?>