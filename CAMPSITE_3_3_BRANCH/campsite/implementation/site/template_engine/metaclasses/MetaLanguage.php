<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/Language.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/metaclasses/MetaDbObject.php');

/**
 * @package Campsite
 */
final class MetaLanguage extends MetaDbObject {

    public function __construct($p_languageId = null)
    {
		$this->m_dbObject = new Language($p_languageId);
        if (!$this->m_dbObject->exists()) {
            $this->m_dbObject = new Language();
        }

        $this->m_properties['name'] = 'OrigName';
        $this->m_properties['number'] = 'Id';
        $this->m_properties['english_name'] = 'Name';
        $this->m_properties['code'] = 'Code';

		$this->m_customProperties['defined'] = 'defined';
    } // fn __construct

} // class MetaLanguage

?>