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

require_once($g_documentRoot.'/classes/User.php');
require_once($g_documentRoot.'/classes/Exceptions.php');


/**
 * @package Campsite
 */
final class MetaUser {
    //
    private $m_data = null;
    //
    private $m_instance = false;
    //
    private $m_baseFields = array(
                                  'Id',
                                  'Name',
                                  'UName',
                                  'EMail',
                                  'City',
                                  'StrAddress',
                                  'State',
                                  'CountryCode',
                                  'Phone',
                                  'Fax',
                                  'Contact',
                                  'Phone2',
                                  'Title',
                                  'Gender',
                                  'Age',
                                  'PostalCode',
                                  'Employer',
                                  'Position',
                                  'Interests',
                                  'How',
                                  'Languages',
                                  'Improvements',
                                  'Field1',
                                  'Field2',
                                  'Field3',
                                  'Field4',
                                  'Field5',
                                  'Text1',
                                  'Text2',
                                  'Text3'
                                  );


    public function __construct($p_userId)
    {
        $userObj = new User($p_userId);

        if (!is_object($userObj) || !$userObj->exists()) {
            return false;
        }
        foreach ($userObj->m_data as $key => $value) {
            if (in_array($key, $this->m_baseFields)) {
                $this->m_data[$key] = $value;
            }
        }
        $this->m_instance = true;
    } // fn __construct


    public function __get($p_property)
    {
        if (!is_array($this->m_data)) {
            return false;
        }
        if (!array_key_exists($p_property, $this->m_data)) {
            return false;
        }

        return $this->m_data[$p_property];
    } // fn __get


    public function __set($p_property, $p_value)
    {
        throw new InvalidFunctionException(get_class($this), '__set');
    } // fn __set


    public function defined()
    {
        return $this->m_instance;
    } // fn defined

} // class MetaUser

?>