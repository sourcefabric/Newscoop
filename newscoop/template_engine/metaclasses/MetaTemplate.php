<?php

/**
 * @package Campsite
 */
/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'] . '/classes/Template.php');
require_once($GLOBALS['g_campsiteDir'] . '/template_engine/metaclasses/MetaDbObject.php');

use Newscoop\Service\Resource\ResourceId;
use Newscoop\Service\ISyncResourceService;

/**
 * @package Campsite
 */
final class MetaTemplate extends MetaDbObject
{

    protected $_map = array();

    public function __construct($p_templateIdOrName = null)
    {

        $this->_map = array(
            "frontPage" => "issue",
            "errorPage" => "default",
            "sectionPage" => "section",
            "issuePage" => "issue",
            "articlePage" => "article"
        );
        $resourceId = new ResourceId('template_engine/metaclasses/MetaTemplate');
        /* @var $syncResourceService ISyncResourceService */
        $syncResourceService = $resourceId->getService(ISyncResourceService::NAME);

        try {
            $this->m_dbObject = $syncResourceService->findByPathOrId($p_templateIdOrName);
        } catch (Exception $e) {
        	$this->m_dbObject = null;
        }

        $this->m_properties = array();

        $this->m_customProperties['name'] = 'getValue';
        $this->m_customProperties['identifier'] = 'getId';
        $this->m_customProperties['type'] = 'getTemplateType';
        $this->m_customProperties['defined'] = 'defined';
        $this->m_customProperties['theme_dir'] = 'getThemeDir';
    }// fn __construct

    protected function getTemplateType()
    {
    	if (is_null($this->m_dbObject)) {
    		return null;
    	}
        if (isset($this->_map[$this->m_dbObject->getName()])) {

            return $this->_map[$this->m_dbObject->getName()];
        }
        return 'default';
    }

    public function getValue()
    {
    	if (is_null($this->m_dbObject)) {
    		return null;
    	}
    	return $this->m_dbObject->getPath();
    }

    public function IsValid($p_value)
    {
        return true;
    }

    public function getId()
    {
    	if (is_null($this->m_dbObject)) {
    		return null;
    	}
    	return $this->m_dbObject->getId();
    }

    public static function GetTypeName()
    {
        return 'template';
    }

	// we need this theme info for securing the smarty caching
	protected function getThemeDir() {
		$path = $this->getValue();
		if (empty($path)) {
			return '';
		}

		$dir_arr = explode(DIR_SEP, $path);
		if (2 < count($dir_arr)) {
			return  $dir_arr[0] . DIR_SEP . $dir_arr[1];
		}

		return $path;
	}
}
// class MetaTemplate

?>