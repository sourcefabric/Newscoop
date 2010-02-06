<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Translation.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');

/**
 * @package Campsite
 */
class Attachment extends DatabaseObject {
	var $m_keyColumnNames = array('gunid');
	var $m_keyIsAutoIncrement = true;
	var $m_dbTableName = 'Attachments';
	var $m_columnNames = array('gunid',
							   'file_name',
							   'extension',
							   'mime_type',
							   'size_in_bytes',
							   'fk_user_id',
							   'last_modified',
							   'time_created');

	public function Attachment($p_id = null)
	{
		if (!is_null($p_id)) {
			$this->m_data['gunid'] = $p_id;
			$this->fetch();
		}
	} // constructor


	public function delete()
	{
		if (!$this->exists()) {
			return false;
		}

		// Delete all the references to this image.
		ArticleAttachment::OnAttachmentDelete($this->m_data['gunid']);

		// Delete the description
		Translation::deletePhrase($this->m_data['fk_description_id']);

		$tmpData = $this->m_data;

		// Delete the record in the database
		$success = parent::delete();

		// Delete the images from disk
		$file = $this->getStorageLocation();
		if (file_exists($file) && is_file($file)) {
			unlink($file);
		}

		$logtext = getGS('File #$1 "$2" deleted.', $tmpData['id'], $tmpData['file_name']);
		Log::Message($logtext, null, 39);
		return $success;
	} // fn delete


	/**
	 * @return int
	 */
	public function getAttachmentId()
	{
		return $this->m_data['gunid'];
	} // fn getAttachmentId


	/**
	 * @return string
	 */
	public function getFileName()
	{
		return $this->m_data['file_name'];
	} // fn getFileName


	/**
	 * Return the file name extension
	 *
	 * @return string
	 */
	public function getExtension()
	{
		return $this->m_data['extension'];
	} // fn getExtension


	/**
	 * Return the MIME type which should be set in the HTTP header
	 * to the downloader.
	 *
	 * @return string
	 */
	public function getMimeType()
	{
		return $this->m_data['mime_type'];
	} // fn getMimeType


	/**
	 * Get the size of the file in bytes
	 *
	 * @return int
	 */
	public function getSizeInBytes()
	{
		return $this->m_data['size_in_bytes'];
	} // fn getSizeInBytes


	public function getLastModified()
	{
		return $this->m_data['last_modified'];
	} // fn getLastModified


	public function getTimeCreated()
	{
		return $this->m_data['time_created'];
	} // fn getTimeCreated

} // class Attachment

?>