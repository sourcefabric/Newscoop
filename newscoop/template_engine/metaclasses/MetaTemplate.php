<?php

/**
 * @package Campsite
 */
/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'] . '/template_engine/metaclasses/MetaDbObject.php');

use Newscoop\Service\Resource\ResourceId;
use Newscoop\Service\ISyncResourceService;
use Newscoop\Entity\Resource;

/**
 * @package Campsite
 */
final class MetaTemplate extends MetaDbObject
{
    private $_name = false;

    protected $_map = array();

    public function __construct($p_templateIdOrName = null, $p_themePath = null)
    {
        $this->m_properties = array();

        $this->m_customProperties['name'] = 'getValue';
        $this->m_customProperties['identifier'] = 'getId';
        $this->m_customProperties['type'] = 'getTemplateType';
        $this->m_customProperties['defined'] = 'defined';
        $this->m_customProperties['theme_dir'] = 'getThemeDir';

        $this->_map = array(
            "frontPage" => "issue",
            "errorPage" => "default",
            "sectionPage" => "section",
            "issuePage" => "issue",
            "articlePage" => "article"
        );

        if ((is_string($p_templateIdOrName) || is_int($p_templateIdOrName))
        && !empty($p_templateIdOrName)) {
            $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
            $cacheKey = $cacheService->getCacheKey(array('MetaTemplate', $p_templateIdOrName, $p_themePath), 'template');
            if ($cacheService->contains($cacheKey)) {
                $this->m_dbObject = $cacheService->fetch($cacheKey);
            } else {
                $filePath = is_numeric($p_templateIdOrName) ? $p_templateIdOrName : $p_themePath.$p_templateIdOrName;

                $resourceId = new ResourceId('template_engine/metaclasses/MetaTemplate');
                /* @var $syncResourceService ISyncResourceService */
                $syncResourceService = $resourceId->getService(ISyncResourceService::NAME);

                $this->m_dbObject = $syncResourceService->findByPathOrId($filePath);
                $cacheService->save($cacheKey, $this->m_dbObject);
            }

            if (is_null($this->m_dbObject)) {
                $pathRsc = new Resource();
                $pathRsc->setName('buildPage');
                $filePath = CS_PATH_TEMPLATES.DIR_SEP.$p_themePath.$p_templateIdOrName;
                if (!is_numeric($p_templateIdOrName) && !is_null($p_themePath)
                && file_exists($filePath)) {
                    $pathRsc->setPath($p_themePath.$p_templateIdOrName);
                    $this->m_dbObject = $syncResourceService->getSynchronized($pathRsc);
                } else {
                    $this->m_dbObject = $pathRsc;
                }
            }
        }
    }// fn __construct

    /**
     * Try to get it from the template id (templates table)
     * @param int $tplId
     * @author Mihai Balaceanu
     */
    protected function getByTemplateIdOrName($tplId)
    {
        $doctrine = Zend_Registry::get('container')->getService('doctrine');
        if( is_numeric($tplId) ) {
            $tpl = $doctrine->getEntityManager()->getRepository('Newscoop\Entity\Template')->find($tplId);
        } else {
            $tpl = $doctrine->getEntityManager()->getRepository('Newscoop\Entity\Template')->findOneBy(array('key' => $tplId));
        }
        /* @var $tpl \Newscoop\Entity\Template */
        $this->m_dbObject = $tpl;
    }

    protected function getTemplateType()
    {
        if (isset($this->_map[$this->m_dbObject->getName()])) {
            return $this->_map[$this->m_dbObject->getName()];
        }
        return 'default';
    }

    protected function getValue()
    {
        if ($this->_name !== false) {
            return $this->_name;
        }
        $parts = explode('/', $this->m_dbObject->getPath());
        if (count($parts) < 3) {
            $this->_name = null;
            return null;
        }
        array_shift($parts);
        array_shift($parts);
        $this->_name = implode('/', $parts);
        return $this->_name;
    }

    protected function getId()
    {
    	return $this->m_dbObject->getId();
    }

    public static function GetTypeName()
    {
        return 'template';
    }

	// we need this theme info for securing the smarty caching
	protected function getThemeDir()
	{
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
