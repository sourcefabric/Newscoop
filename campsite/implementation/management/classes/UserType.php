<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DatabaseObject.php');

/**
 * @package Campsite
 */
class UserType extends DatabaseObject {
	var $m_dbTableName = 'UserTypes';
	var $m_keyColumnNames = array('Name');
	var $m_keyIsAutoIncrement = false;
	var $m_columnNames = array(
		'Name',
		'Reader',
		'ManagePub',
		'DeletePub',
		'ManageIssue',
		'DeleteIssue',
		'ManageSection',
		'DeleteSection',
		'AddArticle',
		'ChangeArticle',
		'DeleteArticle',
		'AddImage',
		'ChangeImage',
		'DeleteImage',
		'ManageTempl',
		'DeleteTempl',
		'ManageUsers',
		'ManageSubscriptions',
		'DeleteUsers',
		'ManageUserTypes',
		'ManageArticleTypes',
		'DeleteArticleTypes',
		'ManageLanguages',
		'DeleteLanguages',
		'ManageDictionary',
		'DeleteDictionary',
		'ManageCountries',
		'DeleteCountries',
		'ManageClasses',
		'MailNotify',
		'ViewLogs',
		'ManageLocalizer',
		'ManageIndexer',
		'Publish',
		'ManageTopics',
		'EditorImage',
		'EditorTextAlignment',
		'EditorFontColor',
		'EditorFontSize',
		'EditorFontFace',
		'EditorTable',
		'EditorSuperscript',
		'EditorSubscript',
		'EditorStrikethrough',
		'EditorIndent',
		'EditorListBullet',
		'EditorListNumber',
		'EditorHorizontalRule',
		'EditorSourceView',
		'EditorEnlarge',
		'EditorTextDirection',
		'EditorLink',
		'EditorSubhead',
		'EditorBold',
		'EditorItalic',
		'EditorUnderline',
		'EditorUndoRedo',
		'EditorCopyCutPaste',
		'ManageReaders');
	
	
	/**
	 * @param string $p_userType
	 */
	function UserType($p_userType = null)
	{
		parent::DatabaseObject($this->m_columnNames);
		if ($p_userType != '') {
			$this->m_data['Name'] = $p_userType;
			if ($this->keyValuesExist()) {
				$this->fetch();
			}
		}
	} // constructor
	
	
	/**
	 * @return string
	 */
	function getName()
	{
		return $this->getProperty('Name');
	} // fn getId
	
	
	/**
	 * Return true if the user type has the permission specified.
	 *
	 * @param string $p_permissionString
	 *
	 * @return boolean
	 */
	function hasPermission($p_permissionString)
	{
		return ($p_permissionString != 'Name' && $p_permissionString != 'Reader'
				&& isset($this->m_data[$p_permissionString])
				&& $this->m_data[$p_permissionString] == 'Y');
	} // fn hasPermission
	
	
	/**
	 * Set the specified permission.
	 *
	 * @param string $p_permissionString
	 *
	 * @param boolean $p_permissionValue
	 *
	 */
	function setPermission($p_permissionString, $p_permissionValue)
	{
		if (array_key_exists($p_permissionString, $this->m_columnNames)
			&& $p_permissionString != 'Name'
			&& $p_permissionString != 'Reader') {
			$this->setProperty($p_permissionString, $p_permissionValue ? 'Y' : 'N');
		}
	} // fn hasPermission
	
	
	/**
	 * Set the user type to staff or subscriber
	 *
	 * @param boolean $p_isAdmin
	 *
	 */
	function setAdmin($p_isAdmin)
	{
		$this->setProperty('Reader', $p_isAdmin ? 'N' : 'Y');
	} // fn isAdmin
	
	
	/**
	 * @return boolean
	 */
	function isAdmin()
	{
		return $this->getProperty('Reader') == 'N';
	} // fn isAdmin
	
} // class User

?>