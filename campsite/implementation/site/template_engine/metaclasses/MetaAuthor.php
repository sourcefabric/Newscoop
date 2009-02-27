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

require_once($g_documentRoot.'/classes/Author.php');
require_once($g_documentRoot.'/classes/Language.php');
require_once($g_documentRoot.'/template_engine/metaclasses/MetaDbObject.php');

/**
 * @package Campsite
 */
final class MetaAuthor extends MetaDbObject {

	private function InitProperties()
	{
		if (!is_null($this->m_properties)) {
			return;
		}
		$this->m_properties['identifier'] = 'Id';
        $this->m_properties['first_name'] = 'first_name';
        $this->m_properties['last_name'] = 'last_name';
        $this->m_properties['email'] = 'email';
	}


    public function __construct($p_idOrName = null)
    {
        $this->m_dbObject = new Author($p_idOrName);
        if (!$this->m_dbObject->exists()) {
            $this->m_dbObject = new Author();
        }
        
		$this->InitProperties();
		$this->m_customProperties['name'] = 'getName';
        $this->m_customProperties['defined'] = 'defined';
    } // fn __construct


    protected function getName($p_format = '%_FIRST_NAME %_LAST_NAME')
    {
    	return $this->m_dbObject->getName($p_format);
    }


    public static function GetTypeName()
    {
        return 'author';
    }
} // class MetaAuthor

?>