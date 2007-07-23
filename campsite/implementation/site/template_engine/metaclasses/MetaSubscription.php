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

require_once($g_documentRoot.'/classes/Subscription.php');
require_once($g_documentRoot.'/classes/Language.php');
require_once($g_documentRoot.'/template_engine/metaclasses/MetaDbObject.php');
require_once($g_documentRoot.'/template_engine/classes/CampTemplate.php');

/**
 * @package Campsite
 */
final class MetaSubscription extends MetaDbObject {

	private function InitProperties()
	{
		if (!is_null($this->m_properties)) {
			return;
		}
		$this->m_properties['currency'] = 'Currency';
	}


    public function __construct($p_subscriptionId = null)
    {
        $this->m_dbObject =& new Subscription($p_subscriptionId);

		$this->InitProperties();
		$this->m_customProperties['type'] = 'getType';
		$this->m_customProperties['expiration_date'] = 'getExpirationDate';
		$this->m_customProperties['active'] = 'isActive';
        $this->m_customProperties['defined'] = 'defined';
    } // fn __construct


    public function getType()
    {
    	$type = $this->m_dbObject->getType();
    	return $type == 'T' ? 'trial' : 'paid';
    }


    public function getExpirationDate()
    {
    	return '';
    }


    public function isActive()
    {
    	return $this->m_dbObject->isActive();
    }

} // class MetaTopic

?>