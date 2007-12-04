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

require_once($g_documentRoot.'/classes/DatabaseObject.php');
require_once($g_documentRoot.'/classes/Translation.php');

/**
 * @package Campsite
 */
class Attachment extends DatabaseObject {
	var $m_keyColumnNames = array('id');
	var $m_keyIsAutoIncrement = true;
	var $m_dbTableName = 'Attachments';
	var $m_columnNames = array('id',
							   'fk_language_id',
							   'file_name',
							   'extension',
							   'content_disposition',
							   'http_charset',
							   'mime_type',
							   'size_in_bytes',
							   'fk_description_id',
							   'fk_user_id',
							   'last_modified',
							   'time_created');

	function Attachment($p_id = null)
	{
		if (!is_null($p_id)) {
			$this->m_data['id'] = $p_id;
			$this->fetch();
		}
	} // constructor


	function delete()
	{
		if (!$this->exists()) {
			return false;
		}

		// Delete all the references to this image.
		ArticleAttachment::OnAttachmentDelete($this->m_data['id']);

		// Delete the description
		Translation::deletePhrase($this->m_data['fk_description_id']);

		// Delete the record in the database
		$success = parent::delete();

		// Delete the images from disk
		$file = $this->getStorageLocation();
		if (file_exists($file) && is_file($file)) {
			unlink($file);
		}

		return $success;
	} // fn delete


	/**
	 * @return int
	 */
	function getAttachmentId()
	{
		return $this->m_data['id'];
	} // fn getAttachmentId


	/**
	 * If this attachment is language-specific, e.g. a PDF written
	 * in Serbian, this will return the language id of the attachment.
	 * Otherwise, it will return NULL.
	 *
	 * @return int
	 */
	function getLanguageId()
	{
		return $this->m_data['fk_language_id'];
	} // fn getLanguageId


	/**
	 * @param int $p_value
	 * @return boolean
	 */
	function setLanguageId($p_value)
	{
		if (empty($p_value)) {
			return $this->setProperty('fk_language_id', 'NULL', true, true);
		} else {
			return $this->setProperty('fk_language_id', $p_value);
		}
	} // fn setLanguageId


	/**
	 * @return string
	 */
	function getFileName()
	{
		return $this->m_data['file_name'];
	} // fn getFileName


	/**
	 * Return the file name extension
	 *
	 * @return string
	 */
	function getExtension()
	{
		return $this->m_data['extension'];
	} // fn getExtension


	/**
	 * Return whether the "content-disposition" HTTP header should be set.
	 * This will return either NULL or the string "attachment".
	 *
	 * @return mixed
	 */
	function getContentDisposition()
	{
		return $this->m_data['content_disposition'];
	} // fn getContentDisposition


	/**
	 * Set the "content-disposition" HTTP header.
	 *
	 * @param mixed $p_value
	 * @return boolean
	 */
	function setContentDisposition($p_value)
	{
		if (!empty($p_value) && ($p_value != 'attachment')) {
			return false;
		}
		if (empty($p_value)) {
			return $this->setProperty('content_disposition', 'NULL', true, true);
		} else {
			return $this->setProperty('content_disposition', $p_value);
		}
	} // fn setContentDisposition


	/**
	 * Return the CHARSET which should be set in the HTTP header sent
	 * to the downloader.
	 *
	 * @return string
	 */
	function getCharset()
	{
		return $this->m_data['http_charset'];
	} // fn getCharset


	/**
	 * Return the MIME type which should be set in the HTTP header
	 * to the downloader.
	 *
	 * @return string
	 */
	function getMimeType()
	{
		return $this->m_data['mime_type'];
	} // fn getMimeType


	/**
	 * Get the size of the file in bytes
	 *
	 * @return int
	 */
	function getSizeInBytes()
	{
		return $this->m_data['size_in_bytes'];
	} // fn getSizeInBytes


	/**
	 * Get the description ID which is an index into the Translations table.
	 *
	 * @return int
	 */
	function getDescriptionId()
	{
		return $this->m_data['fk_description_id'];
	} // fn getDescriptionId


	/**
	 * Get the description in the given language.
	 * This is a convenience function that wraps the Translation::GetPhrase() function.
	 *
	 * @param int $p_languageId
	 * @return string
	 */
	function getDescription($p_languageId)
	{
		return Translation::GetPhrase($p_languageId, $this->m_data['fk_description_id']);
	} // fn getDescription


	/**
	 * Set the description in the given language.
	 *
	 * @param int $p_languageId
	 * @param string $p_text
	 */
	function setDescription($p_languageId, $p_text)
	{
		Translation::SetPhrase($p_languageId, $this->m_data['fk_description_id'], $p_text);
	} // fn setDescription


	function getLastModified()
	{
		return $this->m_data['last_modified'];
	} // fn getLastModified


	function getTimeCreated()
	{
		return $this->m_data['time_created'];
	} // fn getTimeCreated


	/**
         * Return the full URL to the attached image.
         * @return string
         */
        function getAttachmentUrl()
	{
		global $Campsite;
		$attachmentUrl = $Campsite['FILE_BASE_URL']
				."/".$this->getLevel1DirectoryName()
				."/".$this->getLevel2DirectoryName()
				."/".sprintf('%09d', $this->m_data['id']);
		if (isset($this->m_data['extension']) && !empty($this->m_data['extension'])) {
			$attachmentUrl .= '.'.$this->m_data['extension'];
		}
		return $attachmentUrl;
        } // fn getAttachmentUrl


	/**
	 * Get the full path to the storage location of the file on disk.
	 *
	 * @param string $p_fileExtension
	 * @return string
	 */
	function getStorageLocation()
	{
		global $Campsite;
	    $storageLocation = $Campsite['FILE_DIRECTORY']
	    				   ."/".$this->getLevel1DirectoryName()
	    				   ."/".$this->getLevel2DirectoryName()
	    				   ."/".sprintf('%09d', $this->m_data['id']);
	    if (isset($this->m_data['extension']) && !empty($this->m_data['extension'])) {
	    	$storageLocation .= '.'.$this->m_data['extension'];
	    }
	    return $storageLocation;
	} // fn getStorageLocation


	function getLevel1DirectoryName()
	{
		global $Campsite;
		$level1Dir = floor($this->m_data['id']/($Campsite['FILE_NUM_DIRS_LEVEL_1']*$Campsite['FILE_NUM_DIRS_LEVEL_2']));
		$level1ZeroPad = strlen($Campsite['FILE_NUM_DIRS_LEVEL_1']);
 		return sprintf('%0'.$level1ZeroPad.'d', $level1Dir);
	} // fn getLevel1DirectoryName


	function getLevel2DirectoryName()
	{
		global $Campsite;
		$level2Dir = ($this->m_data['id']/$Campsite['FILE_NUM_DIRS_LEVEL_2'])%$Campsite['FILE_NUM_DIRS_LEVEL_1'];
		$level2ZeroPad = strlen($Campsite['FILE_NUM_DIRS_LEVEL_2']);
	    return sprintf('%0'.$level2ZeroPad.'d', $level2Dir);
	} // fn getLevel2DirectoryName


	function makeDirectories()
	{
	    // Make the directories if they dont exist
	    global $Campsite;
	    $level1 = $Campsite['FILE_DIRECTORY']."/".$this->getLevel1DirectoryName();
	    if (!file_exists($level1)) {
	    	mkdir($level1, 0755);
	    }
		$level2 = $level1."/".$this->getLevel2DirectoryName();
	    if (!file_exists($level2)) {
	    	mkdir($level2, 0755);
	    }
	} // fn makeDirectories


	/**
	 * This function should be called when an attachment is uploaded.  It will
	 * save the attachment to the appropriate place on the disk, and create a
	 * database entry for the file.
	 *
	 * @param array $p_fileVar
	 *     <pre>
	 * 		The variable from the $_FILES array.  The array specifies the following:
	 *		$a["name"] = original name of the file.
	 * 		$a["type"] = the MIME type of the file
	 *		$a["tmp_name"] = the temporary storage location on disk of the file
	 *		$a["size"] = size of the file, in bytes (not required)
	 *		$a["error"] = 0 (zero) if there was no error
	 *     </pre>
	 *
	 * @param array $p_attributes
	 *		Optional attributes which are stored in the database.
	 *		Indexes can be the following: 'content_disposition', 'fk_language_id', 'http_charset', 'fk_user_id'
	 *
	 * @param int $p_id
	 *		If the attachment already exists and we just want to update it, specify the
	 *		current ID here.
	 *
	 * @return mixed
	 *		The Attachment object that was created or updated.
	 *		Return a PEAR_Error on failure.
	 */
	function OnFileUpload($p_fileVar, $p_attributes, $p_id = null)
	{
		if (!is_array($p_fileVar)) {
			return null;
		}

		// Verify its a valid file.
		$filesize = filesize($p_fileVar['tmp_name']);
		if ($filesize === false) {
			return new PEAR_Error("Attachment::OnFileUpload(): invalid parameters received.");
		}

		// Are we updating or creating?
	 	if (!is_null($p_id)) {
	 		// Updating the attachment
	 		$attachment =& new Attachment($p_id);
	 		$attachment->update($p_attributes);
	    	// Remove the old file because
			// the new file may have a different file extension.
			if (file_exists($attachment->getStorageLocation())) {
	    		unlink($attachment->getStorageLocation());
			}
	    } else {
	    	// Creating the attachment
	    	$attachment =& new Attachment();
	    	$attachment->create($p_attributes);
			$attachment->setProperty('time_created', 'NULL', true, true);
	    }
	    $attachment->setProperty('file_name', $p_fileVar['name'], false);
		$attachment->setProperty('mime_type', $p_fileVar['type'], false);
		$attachment->setProperty('size_in_bytes', $p_fileVar['size'], false);
		$extension = "";
		$fileParts = split('\.', $p_fileVar['name']);
		if (count($fileParts) > 1) {
			$extension = array_pop($fileParts);
		    $attachment->setProperty('extension', $extension, false);
		}
	    $target = $attachment->getStorageLocation();
	    $attachment->makeDirectories();
        if (!move_uploaded_file($p_fileVar['tmp_name'], $target)) {
        	$attachment->delete();
            return new PEAR_Error(camp_get_error_message(CAMP_ERROR_CREATE_FILE, $target), CAMP_ERROR_CREATE_FILE);
        }
		chmod($target, 0644);
        $attachment->commit();
        return $attachment;
	} // fn OnFileUpload

} // class Attachment

?>
