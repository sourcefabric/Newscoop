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

require_once($g_documentRoot.'/db_connect.php');
require_once($g_documentRoot.'/classes/DatabaseObject.php');
require_once($g_documentRoot.'/classes/DbObjectArray.php');
require_once($g_documentRoot.'/classes/Log.php');
require_once($g_documentRoot.'/classes/Article.php');
require_once($g_documentRoot.'/classes/ArticleImage.php');
require_once('HTTP/Client.php');

/**
 * @package Campsite
 */
class Image extends DatabaseObject {
	var $m_keyColumnNames = array('Id');
	var $m_keyIsAutoIncrement = true;
	var $m_dbTableName = 'Images';
	var $m_columnNames = array(
		'Id',
		'Description',
		'Photographer',
		'Place',
		'Caption',
		'Date',
		'ContentType',
		'Location',
		'URL',
		'ThumbnailFileName',
		'ImageFileName',
		'UploadedByUser',
		'LastModified',
		'TimeCreated');

	/**
	 * An image is both the orginal image, plus a thumbnail image,
	 * plus metadata.
	 *
	 * @param int $p_imageId
	 */
	public function Image($p_imageId = null)
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['Id'] = $p_imageId;
		if ($this->keyValuesExist()) {
			$this->fetch();
		}
	} // constructor


	/**
	 * Update the image data in the database.
	 *
	 * @param array $p_columns
	 * @param boolean $p_commit
	 * @param boolean $p_isSql
	 * @return boolean
	 */
	public function update($p_columns = null, $p_commit = true, $p_isSql = false)
	{
		$success = parent::update($p_columns, $p_commit, $p_isSql);
		if ($success) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('Changed image properties of $1', $this->m_data['Id']);
			Log::Message($logtext, null, 43);
		}
		return $success;
	} // fn update


	/**
	 * Delete the row from the database, all article references to this image,
	 * and the file(s) on disk.
	 *
	 * @return mixed
	 *		TRUE if the record was deleted,
	 * 		return a PEAR_Error on failure.
	 */
	public function delete()
	{
		if (function_exists("camp_load_translation_strings")) {
			camp_load_translation_strings("api");
		}

		// Deleting the images from disk is the most common place for
		// something to go wrong, so we do that first.
		$thumb = $this->getThumbnailStorageLocation();
		$imageFile = $this->getImageStorageLocation();

		if (file_exists($thumb) && !is_writable($thumb)) {
			return new PEAR_Error(camp_get_error_message(CAMP_ERROR_DELETE_FILE, $thumb), CAMP_ERROR_DELETE_FILE);
		}
		if (file_exists($imageFile) && !is_writable($imageFile)) {
			return new PEAR_Error(camp_get_error_message(CAMP_ERROR_DELETE_FILE, $imageFile), CAMP_ERROR_DELETE_FILE);
		}
		if (file_exists($imageFile) && !unlink($imageFile)) {
			return new PEAR_Error(camp_get_error_message(CAMP_ERROR_DELETE_FILE, $imageFile), CAMP_ERROR_DELETE_FILE);
		}
		if (file_exists($thumb) && !unlink($thumb)) {
			return new PEAR_Error(camp_get_error_message(CAMP_ERROR_DELETE_FILE, $thumb), CAMP_ERROR_DELETE_FILE);
		}

		// Delete all the references to this image.
		ArticleImage::OnImageDelete($this->getImageId());

		// Delete the record in the database
		if (!parent::delete()) {
			return new PEAR_Error(getGS("Could not delete record from the database."));
		}

		$logtext = getGS('Image $1 deleted', $this->m_data['Id']);
		Log::Message($logtext, null, 42);
		return true;
	} // fn delete


	/**
	 * Commit current values to the database.
	 * The values "TimeCreated" and "LastModified" are ignored.
	 *
	 * @return boolean
	 *		Return TRUE if the database was updated, false otherwise.
	 */
	public function commit()
	{
		return parent::commit(array("TimeCreated", "LastModified"));
	} // fn commit


	/**
	 * Return true if the image is being used by an article.
	 *
	 * @return boolean
	 */
	public function inUse()
	{
		global $g_ado_db;
		// It is in use only if there is an entry in both
		// the ArticleImages table and the Articles table.
		$queryStr = 'SELECT Articles.Number FROM Articles, ArticleImages '
					.' WHERE IdImage='.$this->getImageId()
					.' AND Articles.Number=ArticleImages.NrArticle';
		if ($g_ado_db->GetOne($queryStr)) {
			return true;
		} else {
			return false;
		}
	} // fn inUse


	/**
	 * @return int
	 */
	public function getImageId()
	{
		return $this->m_data['Id'];
	} // fn getImageId


	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->m_data['Description'];
	} // fn getDescription


	/**
	 * @return string
	 */
	public function getPhotographer()
	{
		return $this->m_data['Photographer'];
	} // fn getPhotographer


	/**
	 * @return string
	 */
	public function getPlace()
	{
		return $this->m_data['Place'];
	} // fn getPlace


	/**
	 * @return string
	 */
	public function getDate()
	{
		return $this->m_data['Date'];
	} // fn getDate


	/**
	 * @return string
	 */
	public function getLocation()
	{
		return $this->m_data['Location'];
	} // fn getLocation


	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->m_data['URL'];
	} // fn getUrl


	/**
	 * @return string
	 */
	public function getContentType()
	{
		return $this->m_data['ContentType'];
	} // fn getContentType


	/**
	 * Return the full path to the image file.
	 * @return string
	 */
	public function getImageStorageLocation()
	{
		global $Campsite;
		if ($this->m_data['Location'] == 'local') {
			return $Campsite['IMAGE_DIRECTORY'].$this->m_data['ImageFileName'];
		} else {
			return $this->m_data['URL'];
		}
	} // fn getImageStorageLocation


	/**
	 * Return the full path to the thumbnail file.
	 * @return string
	 */
	public function getThumbnailStorageLocation()
	{
		global $Campsite;
		return $Campsite['THUMBNAIL_DIRECTORY'].$this->m_data['ThumbnailFileName'];
	} // fn getThumbnailStorageLocation


	/**
	 * Generate the full path to the thumbnail storage location on disk.
	 * @param string $p_fileExtension
	 *		The file extension for the filename.
	 * @return string
	 */
	public function generateThumbnailStorageLocation($p_fileExtension)
	{
		global $Campsite;
	    $thumbnailStorageLocation = $Campsite['THUMBNAIL_DIRECTORY']
	    	.$Campsite['THUMBNAIL_PREFIX'].sprintf('%09d', $this->getImageId())
	    	.'.'.$p_fileExtension;
	    return $thumbnailStorageLocation;
	} // fn generateThumbnailStorageLocation


	/**
	 * Generate the full path to the image storage location on disk.
	 * @param string $p_fileExtension
	 *		The file extension for the filename.
	 * @return string
	 */
	public function generateImageStorageLocation($p_fileExtension)
	{
		global $Campsite;
	    $imageStorageLocation = $Campsite['IMAGE_DIRECTORY']
	    	.$Campsite['IMAGE_PREFIX'].sprintf('%09d', $this->getImageId())
	    	.'.'.$p_fileExtension;
	    return $imageStorageLocation;
	} // fn generateImageStorageLocation


	/**
	 * Return the full URL to the image image.
	 * @return string
	 */
	public function getImageUrl()
	{
		global $Campsite;
		if ($this->m_data['Location'] == 'local') {
			return $Campsite['IMAGE_BASE_URL'].$this->m_data['ImageFileName'];
		} else {
			return $this->m_data['URL'];
		}
	} // fn getImageUrl


	/**
	 * Get the full URL to the thumbnail image.
	 * @return string
	 */
	public function getThumbnailUrl()
	{
		global $Campsite;
		return $Campsite['THUMBNAIL_BASE_URL'].$this->m_data['ThumbnailFileName'];
	} // fn getThumbnailUrl


	/**
	 * @return int
	 */
	public static function GetMaxId()
	{
		global $g_ado_db;
		$queryStr = 'SHOW TABLE STATUS LIKE "Images"';
		$result = $g_ado_db->GetRow($queryStr);
		return $result['Auto_increment'];
	} // fn GetMaxId


	/**
	 * @return int
	 */
	public static function GetTotalImages()
	{
		global $g_ado_db;
		$queryStr = 'SHOW TABLE STATUS LIKE "Images"';
		$result = $g_ado_db->GetRow($queryStr);
		return $result['Rows'];
	} // fn GetTotalImages


	private function __ImageTypeToExtension($p_imageType)
	{
		$extension = '';
		switch($p_imageType) {
           case IMAGETYPE_GIF: $extension = 'gif'; break;
           case IMAGETYPE_JPEG: $extension = 'jpg'; break;
           case IMAGETYPE_PNG: $extension = 'png'; break;
           case IMAGETYPE_SWF: $extension = 'swf'; break;
           case IMAGETYPE_PSD: $extension = 'psd'; break;
           case IMAGETYPE_BMP: $extension = 'bmp'; break;
           case IMAGETYPE_TIFF_II: $extension = 'tiff'; break;
           case IMAGETYPE_TIFF_MM: $extension = 'tiff'; break;
           case IMAGETYPE_JPC: $extension = 'jpc'; break;
           case IMAGETYPE_JP2: $extension = 'jp2'; break;
           case IMAGETYPE_JPX: $extension = 'jpx'; break;
           case IMAGETYPE_JB2: $extension = 'jb2'; break;
           case IMAGETYPE_SWC: $extension = 'swc'; break;
           case IMAGETYPE_IFF: $extension = 'aiff'; break;
           case IMAGETYPE_WBMP: $extension = 'wbmp'; break;
           case IMAGETYPE_XBM: $extension = 'xbm'; break;
        }
        return $extension;
	}


	private function __GetImageTypeCreateMethod($p_imageType)
	{
		$method = null;
		switch ($p_imageType) {
           case IMAGETYPE_GIF: $method = 'imagecreatefromgif'; break;
           case IMAGETYPE_JPEG: $method = 'imagecreatefromjpeg'; break;
           case IMAGETYPE_PNG: $method = 'imagecreatefrompng'; break;
           case IMAGETYPE_SWF: $method = null; break;
           case IMAGETYPE_PSD: $method = null; break;
           case IMAGETYPE_BMP: $method = null; break;
           case IMAGETYPE_TIFF_II: $method = null; break;
           case IMAGETYPE_TIFF_MM: $method = null; break;
           case IMAGETYPE_JPC: $method = null; break;
           case IMAGETYPE_JP2: $method = null; break;
           case IMAGETYPE_JPX: $method = null; break;
           case IMAGETYPE_JB2: $method = null; break;
           case IMAGETYPE_SWC: $method = null; break;
           case IMAGETYPE_IFF: $method = null; break;
           case IMAGETYPE_WBMP: $method = 'imagecreatefromwbmp'; break;
           case IMAGETYPE_XBM: $method = 'imagecreatefromxbm'; break;
		}
		return $method;
	}


	/**
	 * This function should be called when an image is uploaded.  It will save
	 * the image to the appropriate place on the disk, create a thumbnail for it,
	 * and create a database entry for the file.
	 *
	 * @param array $p_fileVar
	 *     <pre>
	 * 		The variable from the $_FILES array.  The array specifies the following:
	 *		$a["name"] = original name of the file.
	 * 		$a["type"] = the MIME type of the file, e.g. image/gif
	 *		$a["tmp_name"] = the temporary storage location on disk of the file
	 *		$a["size"] = size of the file, in bytes (not required)
	 *		$a["error"] = 0 (zero) if there was no error
	 *     </pre>
	 *
	 * @param array $p_attributes
	 *		Optional attributes which are stored in the database.
	 *		Indexes can be the following: 'Description', 'Photographer', 'Place', 'Date'
	 *
	 * @param int $p_userId
	 *		The user who uploaded the file.
	 *
	 * @param int $p_id
	 *		If the image already exists and we just want to update it, specify the
	 *		current image ID here.
	 *
	 * @return mixed
	 *		The Image object that was created or updated on success,
     * 		return PEAR_Error on error.
	 */
	public static function OnImageUpload($p_fileVar, $p_attributes,
	                                     $p_userId = null, $p_id = null,
	                                     $p_isLocalFile = false)
	{
		global $Campsite;
		if (function_exists("camp_load_translation_strings")) {
			camp_load_translation_strings("api");
		}

		if (!is_array($p_fileVar)) {
			return new PEAR_Error("Invalid arguments given to Image::OnImageUpload()");
		}

		// Verify its a valid image file.
		$imageInfo = @getimagesize($p_fileVar['tmp_name']);
		if ($imageInfo === false) {
			return new PEAR_Error(getGS("The file uploaded is not an image."));
		}
		$extension = Image::__ImageTypeToExtension($imageInfo[2]);

		// Check if image & thumbnail directories are writable.
		$imageDir = $Campsite['IMAGE_DIRECTORY'];
		$thumbDir = $Campsite['THUMBNAIL_DIRECTORY'];
		if (!file_exists($imageDir) || !is_writable($imageDir)) {
			return new PEAR_Error(camp_get_error_message(CAMP_ERROR_WRITE_DIR, $imageDir),
								  CAMP_ERROR_WRITE_DIR);
		}
		if (!file_exists($thumbDir) || !is_writable($thumbDir)) {
			return new PEAR_Error(camp_get_error_message(CAMP_ERROR_WRITE_DIR, $thumbDir),
								  CAMP_ERROR_WRITE_DIR);
		}

		// Are we updating or creating?
	 	if (!is_null($p_id)) {
	 		// Updating the image
	 		$image = new Image($p_id);
	 		$image->update($p_attributes);
	    	// Remove the old image & thumbnail because
			// the new file may have a different file extension.
			if (file_exists($image->getImageStorageLocation())) {
    			unlink($image->getImageStorageLocation());
			}
			if (file_exists($image->getThumbnailStorageLocation())) {
    			unlink($image->getThumbnailStorageLocation());
			}
	    } else {
	    	// Creating the image
	    	$image = new Image();
	    	$image->create($p_attributes);
			$image->setProperty('TimeCreated', 'NULL', true, true);
			$image->setProperty('LastModified', 'NULL', true, true);
	    }
	    $image->setProperty('Location', 'local', false);
	    // If we are using PHP version >= 4.3
	    if (isset($imageInfo['mime'])) {
	    	$image->setProperty('ContentType', $imageInfo['mime'], false);
	    } else {
			$image->setProperty('ContentType', $p_fileVar['type'], false);
	    }
		if (!is_null($p_userId)) {
			$image->setProperty('UploadedByUser', $p_userId, false);
		}
        if (!isset($p_attributes['Date'])) {
        	$image->setProperty('Date', 'NOW()', true, true);
        }
	    $target = $image->generateImageStorageLocation($extension);
	    $thumbnail = $image->generateThumbnailStorageLocation($extension);
	    $image->setProperty('ImageFileName', basename($target), false);
	    $image->setProperty('ThumbnailFileName', basename($thumbnail), false);

	    if ($p_isLocalFile) {
	    	if (!copy($p_fileVar['tmp_name'], $target)) {
	        	if (is_null($p_id)) {
	        		$image->delete();
	        	}
	    		return new PEAR_Error(camp_get_error_message(CAMP_ERROR_CREATE_FILE, $target),
	    							  CAMP_ERROR_CREATE_FILE);
	    	}
	    } else {
	        if (!move_uploaded_file($p_fileVar['tmp_name'], $target)) {
	        	if (is_null($p_id)) {
	        		$image->delete();
	        	}
	    		return new PEAR_Error(camp_get_error_message(CAMP_ERROR_CREATE_FILE, $target),
	    							  CAMP_ERROR_CREATE_FILE);
	        }
	    }
		chmod($target, 0644);

		$createMethodName = Image::__GetImageTypeCreateMethod($imageInfo[2]);
		if ($createMethodName != null) {
			$imageHandler = $createMethodName($target);
            if ($imageHandler == false) {
                return new PEAR_Error(camp_get_error_message(CAMP_ERROR_UPLOAD_FILE, $p_fileVar['name']), CAMP_ERROR_UPLOAD_FILE);
            }
			$thumbnailImage = Image::ResizeImage($imageHandler, $Campsite['THUMBNAIL_MAX_SIZE'],
												 $Campsite['THUMBNAIL_MAX_SIZE']);
			if (PEAR::isError($thumbnailImage)) {
				return $result;
			}
			$result = Image::SaveImageToFile($thumbnailImage, $thumbnail, $imageInfo[2]);
			if (PEAR::isError($result)) {
				return $result;
			}
           	chmod($thumbnail, 0644);
		} elseif ($Campsite['IMAGEMAGICK_INSTALLED']) {
            $cmd = $Campsite['THUMBNAIL_COMMAND'].' '.$target.' '.$thumbnail;
            system($cmd);
            if (file_exists($thumbnail)) {
            	chmod($thumbnail, 0644);
            } else {
	    		return new PEAR_Error(camp_get_error_message(CAMP_ERROR_CREATE_FILE, $thumbnail),
	    							  CAMP_ERROR_CREATE_FILE);
            }
        } else {
        	return new PEAR_Error(getGS("Image type $1 is not supported.",
								  image_type_to_mime_type($p_imageType)));
        }
        $image->commit();
		$logtext = getGS('The image $1 has been added.',
						$image->m_data['Description']." (".$image->m_data['Id'].")");
		Log::Message($logtext, null, 41);

        return $image;
	} // fn OnImageUpload


	/**
	 * Saves the image refered by the resource handler to a file
	 *
	 * @param resource $p_image
	 * 		Image resource handler
	 * @param string $p_fileName
	 * 		The full path of the file
	 * @param int $p_type
	 * 		The image type
	 * @param bool $p_addExtension
	 * 		If true it will add the proper extension to the file name.
	 * @return mixed
	 * 		true if successful, PEAR_Error object in case of error
	 */
	public static function SaveImageToFile($p_image, $p_fileName,
	                                       $p_imageType, $p_addExtension = true)
	{
		$method = null;
		switch ($p_imageType) {
           case IMAGETYPE_GIF: $method = 'imagegif'; break;
           case IMAGETYPE_JPEG: $method = 'imagejpeg'; break;
           case IMAGETYPE_PNG: $method = 'imagepng'; break;
           case IMAGETYPE_WBMP: $method = 'imagewbmp'; break;
           case IMAGETYPE_XBM: $method = 'imagexbm'; break;
		} // these are the supported image types
		if ($method == null) {
			return new PEAR_Error(getGS("Image type $1 is not supported.",
								  image_type_to_mime_type($p_imageType)));
		}
		if (!$method($p_image, $p_fileName)) {
			return new PEAR_Error(camp_get_error_message(CAMP_ERROR_CREATE_FILE, $p_fileName),
	    						  CAMP_ERROR_CREATE_FILE);
		}
		return true;
	}


	/**
	 * Resizes the given image
	 *
	 * @param resource $p_image
	 * 		The image resource handler
	 * @param int $p_maxWidth
	 * 		The maximum width of the resized image
	 * @param int $p_maxHeight
	 * 		The maximum height of the resized image
	 * @param bool $p_keepRatio
	 * 		If true keep the image ratio
	 * @return int
	 * 		Return the new image resource handler.
	 */
	public static function ResizeImage($p_image, $p_maxWidth, $p_maxHeight,
	                                   $p_keepRatio = true)
	{
		$origImageWidth = imagesx($p_image);
		$origImageHeight = imagesy($p_image);
		if ($origImageWidth <= 0 || $origImageHeight <= 0) {
		    return new PEAR_Error(getGS("The file uploaded is not an image."));
		}

        $p_maxWidth = is_numeric($p_maxWidth) ? (int) $p_maxWidth : 0;
        $p_maxHeight = is_numeric($p_maxHeight) ? (int) $p_maxHeight : 0;
		if ($p_maxWidth <= 0 || $p_maxHeight <= 0) {
		    return new PEAR_Error(getGS("Invalid resize width/height."));
		}
		if ($p_keepRatio) {
			$ratioOrig = $origImageWidth / $origImageHeight;
			$ratioNew = $p_maxWidth / $p_maxHeight;
			if ($ratioNew > $ratioOrig) {
				$newImageWidth = $p_maxHeight * $ratioOrig;
				$newImageHeight = $p_maxHeight;
			} else {
				$newImageWidth = $p_maxWidth;
				$newImageHeight = $p_maxWidth / $ratioOrig;
			}
		} else {
			$newImageWidth = $p_maxWidth;
			$newImageHeight = $p_maxHeight;
		}
		$newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
		imagecopyresampled($newImage, $p_image, 0, 0, 0, 0, $newImageWidth, $newImageHeight,
						   $origImageWidth, $origImageHeight);
		return $newImage;
	}


	/**
	 * Download the remote file and save it to disk, create a thumbnail for it,
	 * and create a database entry for the file.
	 *
	 * @param string $p_url
	 *		The remote location of the file. ("http://...");
	 *
	 * @param array $p_attributes
	 *		Optional attributes which are stored in the database.
	 *		Indexes can be the following: 'Description', 'Photographer', 'Place', 'Date'
	 *
	 * @param int $p_userId
	 *		The user ID of the user who uploaded the image.
	 *
	 * @param int $p_id
	 *		If you are updating an image, specify its ID here.
	 *
	 * @return mixed
	 * 		Return an Image object on success, return a PEAR_Error otherwise.
	 */
	public static function OnAddRemoteImage($p_url, $p_attributes,
	                                        $p_userId = null, $p_id = null)
	{
		global $Campsite;
		if (function_exists("camp_load_translation_strings")) {
			camp_load_translation_strings("api");
		}

		// Check if thumbnail directory is writable.
		$imageDir = $Campsite['IMAGE_DIRECTORY'];
		$thumbDir = $Campsite['THUMBNAIL_DIRECTORY'];
		if (!file_exists($imageDir) || !is_writable($imageDir)) {
			return new PEAR_Error(camp_get_error_message(CAMP_ERROR_WRITE_DIR, $imageDir), CAMP_ERROR_WRITE_DIR);
		}
		if (!file_exists($thumbDir) || !is_writable($thumbDir)) {
			return new PEAR_Error(camp_get_error_message(CAMP_ERROR_WRITE_DIR, $thumbDir), CAMP_ERROR_WRITE_DIR);
		}

		$client = new HTTP_Client();
	    $client->get($p_url);
	    $response = $client->currentResponse();
	    if ($response['code'] != 200) {
	    	return new PEAR_Error(getGS("Unable to fetch image from remote server."));
	    }
	    foreach ($response['headers'] as $headerName => $value) {
	    	if (strtolower($headerName) == "content-type") {
	    		$ContentType = $value;
	    		break;
	    	}
	    }

        // Check content type
        if (!preg_match('/image/', $ContentType)) {
            // wrong URL
            return new PEAR_Error(getGS('URL "$1" is invalid or is not an image.', $p_url));
        }

    	// Save the file
        $tmpname = $Campsite['TMP_DIRECTORY'].'img'.md5(rand());
        if (is_writable($Campsite['TMP_DIRECTORY'])) {
	        if ($tmphandle = fopen($tmpname, 'w')) {
	            fwrite($tmphandle, $response['body']);
	            fclose($tmphandle);
	        }
        } else {
	    	return new PEAR_Error(camp_get_error_message(CAMP_ERROR_CREATE_FILE, $tmpname), CAMP_ERROR_CREATE_FILE);
	    }

        // Check if it is really an image file
        $imageInfo = getimagesize($tmpname);
        if ($imageInfo === false) {
        	unlink($tmpname);
            return new PEAR_Error(getGS('URL "$1" is not an image.', $cURL));
        }

        // content-type = image
        if (!is_null($p_id)) {
        	// Updating the image
        	$image = new Image($p_id);
        	$image->update($p_attributes);
	    	// Remove the old image & thumbnail because
	    	// the new file might have a different file extension.
	    	if (file_exists($image->getImageStorageLocation())) {
				if (is_writable(dirname($image->getImageStorageLocation()))) {
		    		unlink($image->getImageStorageLocation());
				} else {
	    			return new PEAR_Error(camp_get_error_message(CAMP_ERROR_DELETE_FILE, $image->getImageStorageLocation()), CAMP_ERROR_DELETE_FILE);
				}
	    	}
	    	if (file_exists($image->getThumbnailStorageLocation())) {
				if (is_writable(dirname($image->getThumbnailStorageLocation()))) {
		    		unlink($image->getThumbnailStorageLocation());
				} else {
	    			return new PEAR_Error(camp_get_error_message(CAMP_ERROR_DELETE_FILE, $image->getThumbnailStorageLocation()), CAMP_ERROR_DELETE_FILE);
				}
	    	}
        } else {
        	// Creating the image
        	$image = new Image();
        	$image->create($p_attributes);
        	$image->setProperty('TimeCreated', 'NULL', true, true);
        	$image->setProperty('LastModified', 'NULL', true, true);
        }
        if (!isset($p_attributes['Date'])) {
        	$image->setProperty('Date', 'NOW()', true, true);
        }
        $image->setProperty('Location', 'remote', false);
        $image->setProperty('URL', $p_url, false);
	    if (isset($imageInfo['mime'])) {
	    	$image->setProperty('ContentType', $imageInfo['mime'], false);
	    }

        // Remember who uploaded the image
        if (!is_null($p_userId)) {
			$image->setProperty('UploadedByUser', $p_userId, false);
        }

        if ($Campsite['IMAGEMAGICK_INSTALLED']) {
		    // Set thumbnail file name
		    $extension = Image::__ImageTypeToExtension($imageInfo[2]);
		    $thumbnail = $image->generateThumbnailStorageLocation($extension);
		    $image->setProperty('ThumbnailFileName', basename($thumbnail), false);

		    if (!is_writable(dirname($image->getThumbnailStorageLocation()))) {
            	return new PEAR_Error(camp_get_error_message(CAMP_ERROR_CREATE_FILE, $image->getThumbnailStorageLocation()), CAMP_ERROR_CREATE_FILE);
		    }

		    // Create the thumbnail
            $cmd = $Campsite['THUMBNAIL_COMMAND'].' '
            	. $tmpname . ' ' . $image->getThumbnailStorageLocation();
            system($cmd);
            if (file_exists($image->getThumbnailStorageLocation())) {
            	chmod($image->getThumbnailStorageLocation(), 0644);
            }
        }
        unlink($tmpname);
        $image->commit();

		$logtext = getGS('The image $1 has been added.',
						$image->m_data['Description']." (".$image->m_data['Id'].")");
		Log::Message($logtext, null, 41);

	    return $image;
	} // fn OnAddRemoteImage


	/**
	 * Get an array of users who have uploaded images.
	 * @return array
	 */
	public static function GetUploadUsers()
	{
		$tmpUser = new User();
		$columnNames = $tmpUser->getColumnNames();
		$queryColumnNames = array();
		foreach ($columnNames as $columnName) {
			$queryColumnNames[] = 'liveuser_users.'.$columnName;
		}
		$queryColumnNames = implode(",", $queryColumnNames);
		$queryStr = 'SELECT DISTINCT liveuser_users.Id, '.$queryColumnNames
					.' FROM Images, liveuser_users '
                    .' WHERE Images.UploadedByUser = liveuser_users.Id';
		$users = DbObjectArray::Create('User', $queryStr);
		return $users;
	} // fn GetUploadUsers


	/**
	 * Fetch an image object by matching the URL.
	 * @param string $p_url
	 * @return Image
	 */
	public static function GetByUrl($p_url)
	{
		global $g_ado_db;
		$queryStr = "SELECT * FROM Images WHERE URL='".mysql_real_escape_string($p_url)."'";
		$row = $g_ado_db->GetRow($queryStr);
		$image = new Image();
		$image->fetch($row);
		return $image;
	} // fn GetByUrl


	/**
	 * Return an array that can be used in a template.
	 *
	 * @return array
	 */
	public function toTemplate()
	{
		$template = array();
		$template['id'] = $this->getImageId();
		$template['description'] = $this->getDescription();
		$template['photographer'] = $this->getPhotographer();
		$template['place'] = $this->getPlace();
		$template['date'] = $this->getDate();
		$template['content_type'] = $this->getContentType();
		$template['image_url'] = $this->getImageUrl();
		$template['thumbnail_url'] = $this->getThumbnailUrl();
		return $template;
	} // fn toTemplate


    /**
     * Returns an images list based on the given parameters.
     *
     * @param array $p_parameters
     *    An array of ComparionOperation objects
     * @param string $p_order
     *    An array of columns and directions to order by
     * @param integer $p_start
     *    The record number to start the list
     * @param integer $p_limit
     *    The offset. How many records from $p_start will be retrieved.
     *
     * @return array $issueList
     *    An array of Issue objects
     */
    public static function GetList($p_parameters, $p_order = null,
                                   $p_start = 0, $p_limit = 0)
    {
        global $g_ado_db;

        if (!is_array($p_parameters)) {
            return null;
        }

        $sqlClauseObj = new SQLSelectClause();

    } // fn GetList

} // class Image
?>