<?php
/**
 * @package Campsite
 */

require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DbObjectArray.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');


/**
 * @package Campsite
 */
class Image extends DatabaseObject
{
	var $m_keyColumnNames = array('Id');
	var $m_keyIsAutoIncrement = true;
	var $m_dbTableName = 'Images';
	var $m_columnNames = array(
		'Id',
		'Description',
		'Photographer',
		'Place',
		'Date',
		'ContentType',
		'Location',
		'URL',
		'ThumbnailFileName',
		'ImageFileName',
		'UploadedByUser',
		'LastModified',
		'TimeCreated',
        'photographer_url',
		'Source',
		'Status'
	);

	static private $s_defaultOrder = array(array('field'=>'default', 'dir'=>'asc'));


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
		require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleImage.php');

        $translator = \Zend_Registry::get('container')->getService('translator');

        $imageStorageService = Zend_Registry::get('container')->getService('image.update_storage');
        if ($imageStorageService->isDeletable($this->getImageFileName())) {
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
        }

		// Delete all the references to this image.
		ArticleImage::OnImageDelete($this->getImageId());

		$imageId = $this->getImageId();
		$imageDescription = $this->getDescription();

        // @ticket CS-4225
        $em = \Zend_Registry::get('container')->getService('em');
        $entity = $em->find('Newscoop\Image\LocalImage', $imageId);
        $em->remove($entity);
        $em->flush();

		// Delete the record in the database
		if (!parent::delete()) {
			return new PEAR_Error($translator->trans("Could not delete record from the database.", array(), 'api'));
		}
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
	 * @return int
	 */
	public function getImageFileName()
	{
		return $this->m_data['ImageFileName'];
	} // fn getImageId


	/**
	 * @return string
	 */
	public function getDescription()
	{
		return strip_tags($this->m_data['Description']);
	} // fn getDescription


	/**
	 * @return string
	 */
	public function getPhotographer()
	{
		return strip_tags($this->m_data['Photographer']);
	} // fn getPhotographer

    /**
     * Get photographer url
     *
     * @return string
     */
    public function getPhotographerUrl()
    {
        return (string) $this->m_data['photographer_url'];
    }

	/**
	 * @return string
	 */
	public function getPlace()
	{
		return strip_tags($this->m_data['Place']);
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
		return strip_tags($this->m_data['Location']);
	} // fn getLocation


	/**
	 * Returns true if the image was stored locally
	 */
	public function isLocal()
	{
		return (int)$this->m_data['Location'] == 'local';
	} // fn isLocal


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
     * @return string
     */
    public function getType()
    {
        return substr($this->m_data['ContentType'], strlen('image/'));
    } // fn getType

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->m_data['Source'];
    } // fn getSource

    /**
     * @return int
     */
    public function getUploadingUserId()
    {
        return $this->m_data['UploadedByUser'];
    } // fn getUploadingUserId

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->m_data['Status'];
    } // fn getSource

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


	public function fixMissingThumbnail()
	{
	   global $Campsite;
       if(!file_exists($Campsite['THUMBNAIL_DIRECTORY'].$this->m_data['ThumbnailFileName'])) {
           $this->generateThumbnailFromImage();
       }
	}


	/**
	 * Get the full URL to the thumbnail image.
	 * @return string
	 */
	public function getThumbnailUrl()
	{
		global $Campsite;
		$this->fixMissingThumbnail();
		return $Campsite['THUMBNAIL_BASE_URL'].$this->m_data['ThumbnailFileName'];
	} // fn getThumbnailUrl


	/**
     * Generate the thumbnail from the existing image.
     *
     * @return mixed
     *      The Image object that was created or updated on success,
     *      return error on error.
     */
    public function generateThumbnailFromImage()
    {
        global $Campsite;

        $translator = \Zend_Registry::get('container')->getService('translator');
        // Verify its a valid image file.
        $imageInfo = @getimagesize($this->getImageStorageLocation());
        if ($imageInfo === false) {
            return new PEAR_Error($translator->trans("The file uploaded is not an image.", array(), 'api'));
        }
        $extension = Image::__ImageTypeToExtension($imageInfo[2]);

        $thumbDir = $Campsite['THUMBNAIL_DIRECTORY'];
        if (!file_exists($thumbDir) || !is_writable($thumbDir)) {
            return FALSE;
        }

        $target = $this->generateImageStorageLocation($extension);
        $thumbnail = $this->generateThumbnailStorageLocation($extension);

        try {

        	$createMethodName = Image::__GetImageTypeCreateMethod($imageInfo[2]);
            if (!isset($createMethodName)) {
                throw new Exception($translator->trans("Image type $1 is not supported.", array(
                                    '$1' => image_type_to_mime_type($p_imageType)), 'api'));
            }

            $imageHandler = $createMethodName($target);
	        $thumbnailImage = Image::ResizeImage($imageHandler,
	            $Campsite['THUMBNAIL_MAX_SIZE'], $Campsite['THUMBNAIL_MAX_SIZE']);
	        if (PEAR::isError($thumbnailImage)) {
	            throw new Exception($thumbnailImage->getMessage(), $thumbnailImage->getCode());
	        }
	        $result = Image::SaveImageToFile($thumbnailImage, $thumbnail, $imageInfo[2]);
	        if (PEAR::isError($result)) {
	            throw new Exception($result->getMessage(), $result->getCode());
	        }
            self::chmod($thumbnail, 0644);
        } catch (Exception $ex) {
            if (file_exists($thumbnail)) {
                @unlink($thumbnail);
            }
        	return new PEAR_Error($ex->getMessage(), $ex->getCode());
        }
        return $thumbnailImage;
    }



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
        $translator = \Zend_Registry::get('container')->getService('translator');
		if (!is_array($p_fileVar)) {
			return new PEAR_Error("Invalid arguments given to Image::OnImageUpload()");
		}

		// Verify its a valid image file.
		$imageInfo = @getimagesize($p_fileVar['tmp_name']);
		if ($imageInfo === false) {
			return new PEAR_Error($translator->trans("The file uploaded is not an image.", array(), 'api'));
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
	 		$image->update($p_attributes, false);
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

	    try {
    	    if ($p_isLocalFile) {
    	    	if (!copy($p_fileVar['tmp_name'], $target)) {
    	    		throw new Exception(camp_get_error_message(CAMP_ERROR_CREATE_FILE, $target),
    	    							CAMP_ERROR_CREATE_FILE);
    	    	}
    	    } else {
    	        if (!rename($p_fileVar['tmp_name'], $target)) {
    	    		throw new Exception(camp_get_error_message(CAMP_ERROR_CREATE_FILE, $target),
    	    							CAMP_ERROR_CREATE_FILE);
    	        }
    	    }

            self::chmod($target, 0644);

    		$createMethodName = Image::__GetImageTypeCreateMethod($imageInfo[2]);
    		if (!isset($createMethodName)) {
            	throw new Exception($translator->trans("Image type $1 is not supported.", array(
    								'$1' => image_type_to_mime_type($p_imageType)), 'api'));
            }

            $imageHandler = $createMethodName($target);
            if (!$imageHandler) {
                throw new Exception(camp_get_error_message(CAMP_ERROR_UPLOAD_FILE, $p_fileVar['name']), CAMP_ERROR_UPLOAD_FILE);
            }

            $thumbnailImage = Image::ResizeImage($imageHandler,
                $Campsite['THUMBNAIL_MAX_SIZE'], $Campsite['THUMBNAIL_MAX_SIZE']);
            if (PEAR::isError($thumbnailImage)) {
                throw new Exception($thumbnailImage->getMessage(), $thumbnailImage->getCode());
            }

            $result = Image::SaveImageToFile($thumbnailImage, $thumbnail, $imageInfo[2]);
            if (PEAR::isError($result)) {
                throw new Exception($result->getMessage(), $result->getCode());
            }

            self::chmod($thumbnail, 0644);
	    } catch (Exception $ex) {
	        if (file_exists($target)) {
	            @unlink($target);
	        }
	        if (file_exists($thumbnail)) {
	            @unlink($thumbnail);
	        }
            if (is_null($p_id)) {
                $image->delete();
            }
	        return new PEAR_Error($ex->getMessage(), $ex->getCode());
	    }

        $user = Zend_Registry::get('container')->getService('user')->getCurrentUser();
        if ($user && $user->isAdmin()) {
            $image->m_data['Status'] = 'approved';
            $image->m_data['Source'] = 'local';
        }

        $image->commit();

        return $image;
	} // fn OnImageUpload


	/**
	 * Saves the image refered by the resource handler to a file
	 *
	 * @param resource $p_image
	 * 		Image resource handler
	 * @param string $p_fileName
	 * 		The full path of the file
	 * @param bool $p_addExtension
	 * 		If true it will add the proper extension to the file name.
	 * @return mixed
	 * 		true if successful, PEAR_Error object in case of error
	 */
	public static function SaveImageToFile($p_image, $p_fileName,
	                                       $p_imageType, $p_addExtension = true)
	{  
        $translator = \Zend_Registry::get('container')->getService('translator');
		$method = null;
		switch ($p_imageType) {
           case IMAGETYPE_GIF: $method = 'imagegif'; break;
           case IMAGETYPE_JPEG: $method = 'imagejpeg'; break;
           case IMAGETYPE_PNG: $method = 'imagepng'; break;
           case IMAGETYPE_WBMP: $method = 'imagewbmp'; break;
           case IMAGETYPE_XBM: $method = 'imagexbm'; break;
		} // these are the supported image types
		if ($method == null) {
			return new PEAR_Error($translator->trans("Image type $1 is not supported.", array(
								  '$1' => image_type_to_mime_type($p_imageType)), 'api'));
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
     * @param int $type
     *      Image type
	 * @return int
	 * 		Return the new image resource handler.
	 */
	public static function ResizeImage($p_image, $p_maxWidth, $p_maxHeight,
	                                   $p_keepRatio = true, $type = IMAGETYPE_JPEG)
	{   
        $translator = \Zend_Registry::get('container')->getService('translator');
        if (!isset($p_image) || empty($p_image)) {
            return new PEAR_Error('The image resource handler is not available.');
        }
        require_once APPLICATION_PATH . '/../library/Nette/Image.php';

		$origImageWidth = imagesx($p_image);
		$origImageHeight = imagesy($p_image);
		if ($origImageWidth <= 0 || $origImageHeight <= 0) {
		    return new PEAR_Error($translator->trans("The file uploaded is not an image.", array(), 'api'));
		}

        $p_maxWidth = is_numeric($p_maxWidth) ? (int) $p_maxWidth : 0;
        $p_maxHeight = is_numeric($p_maxHeight) ? (int) $p_maxHeight : 0;
		if ($p_maxWidth <= 0 || $p_maxHeight <= 0) {
		    return new PEAR_Error($translator->trans("Invalid resize width/height.", array(), 'api'));
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

		$newImage = \Nette\Image::fromBlank($newImageWidth, $newImageHeight);
        $newImage->alphaBlending(false);
        $newImage->saveAlpha(true);

		imagecopyresampled($newImage->getImageResource(), $p_image, 0, 0, 0, 0, $newImageWidth, $newImageHeight,
						   $origImageWidth, $origImageHeight);

		return $newImage->getImageResource();
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
		
        $translator = \Zend_Registry::get('container')->getService('translator');
		// Check if thumbnail directory is writable.
		$imageDir = $Campsite['IMAGE_DIRECTORY'];
		$thumbDir = $Campsite['THUMBNAIL_DIRECTORY'];
		if (!file_exists($imageDir) || !is_writable($imageDir)) {
			return new PEAR_Error(camp_get_error_message(CAMP_ERROR_WRITE_DIR, $imageDir), CAMP_ERROR_WRITE_DIR);
		}
		if (!file_exists($thumbDir) || !is_writable($thumbDir)) {
			return new PEAR_Error(camp_get_error_message(CAMP_ERROR_WRITE_DIR, $thumbDir), CAMP_ERROR_WRITE_DIR);
		}

        // fetch headers
        $headers = get_headers($p_url, TRUE);
        if (strpos($headers[0], '200 OK') === FALSE) {
            return new PEAR_Error($translator->trans("Unable to fetch image from remote server.", array(), 'api'));
        }

        // get type
        $ContentType = $headers['Content-Type'];

        // Check content type
        if (strpos($ContentType, 'image') === FALSE) { // wrong URL
            return new PEAR_Error($translator->trans('URL $1 is invalid or is not an image.', array('$1' => $p_url), 'api'));
        }

    	// check path
        if (!is_writable($Campsite['TMP_DIRECTORY'])) {
	    	return new PEAR_Error(camp_get_error_message(CAMP_ERROR_CREATE_FILE, $tmpname), CAMP_ERROR_CREATE_FILE);
        }

        // save image
        $tmpname = $Campsite['TMP_DIRECTORY'].'img'.md5(uniqid());
        file_put_contents($tmpname, file_get_contents($p_url));

        // Check if it is really an image file
        $imageInfo = getimagesize($tmpname);
        if ($imageInfo === false) {
        	unlink($tmpname);
            return new PEAR_Error($translator->trans('URL $1 is not an image.', array('$1' => $cURL), 'api'));
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

        // create thumbnail
		$extension = Image::__ImageTypeToExtension($imageInfo[2]);
		$thumbnail = $image->generateThumbnailStorageLocation($extension);
		$image->setProperty('ThumbnailFileName', basename($thumbnail), false);

		if (!is_writable(dirname($thumbnail))) {
            return new PEAR_Error(camp_get_error_message(CAMP_ERROR_CREATE_FILE, $image->getThumbnailStorageLocation()), CAMP_ERROR_CREATE_FILE);
		}

        $createMethodName = Image::__GetImageTypeCreateMethod($imageInfo[2]);
    	if (!isset($createMethodName)) {
            throw new Exception($translator->trans("Image type $1 is not supported.", array(
    							'$1' => image_type_to_mime_type($ContentType)), 'api'));
        }

        $imageHandler = $createMethodName($tmpname);
        if (!$imageHandler) {
            throw new Exception(camp_get_error_message(CAMP_ERROR_UPLOAD_FILE, $p_fileVar['name']), CAMP_ERROR_UPLOAD_FILE);
        }

        $thumbnailImage = Image::ResizeImage($imageHandler,
            $Campsite['THUMBNAIL_MAX_SIZE'], $Campsite['THUMBNAIL_MAX_SIZE']);
        if (PEAR::isError($thumbnailImage)) {
            throw new Exception($thumbnailImage->getMessage(), $thumbnailImage->getCode());
        }

        $result = Image::SaveImageToFile($thumbnailImage, $thumbnail, $imageInfo[2]);
        if (PEAR::isError($result)) {
            throw new Exception($result->getMessage(), $result->getCode());
        }

        if (file_exists($thumbnail)) {
            self::chmod($thumbnail, 0644);
        }

        unlink($tmpname);
        $image->commit();

	    return $image;
	} // fn OnAddRemoteImage

    /**
     * Get an array of Images uploaded by the user with $user_id.
     *
     * @param int $user_id
     * @return array Image objects
     */
	public static function GetUploadedImagesForUser($user_id)
	{
        global $g_ado_db;
        $images = array();

        $queryStr = "SELECT * FROM Images WHERE UploadedByUser=" . $g_ado_db->escape($user_id);
        $rows = $g_ado_db->GetAll($queryStr);

        foreach ( $rows as $row ) {
            $image = new Image();
            $image->fetch($row);

            $images[] = $image;
        }

        return $images;
	}

	/**
	 * Fetch an image object by matching the URL.
	 * @param string $p_url
	 * @return Image
	 */
	public static function GetByUrl($p_url)
	{
		global $g_ado_db;
		$queryStr = "SELECT * FROM Images WHERE URL=" . $g_ado_db->escape($p_url);
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
        $template['photographer_url'] = $this->getPhotographerUrl();
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
     * @param integer $p_count
     *
     * @return array $issueList
     *    An array of Issue objects
     */
    public static function GetList(array $p_parameters, array $p_order = array(),
                                   $p_start = 0, $p_limit = 0, &$p_count, $p_skipCache = false)
    {
        global $g_ado_db;

        if (!$p_skipCache && CampCache::IsEnabled()) {
        	$paramsArray['parameters'] = serialize($p_parameters);
        	$paramsArray['order'] = (is_null($p_order)) ? 'null' : $p_order;
        	$paramsArray['start'] = $p_start;
        	$paramsArray['limit'] = $p_limit;
        	$cacheListObj = new CampCacheList($paramsArray, __METHOD__);
        	$imagesList = $cacheListObj->fetchFromCache();
        	if ($imagesList !== false && is_array($imagesList)) {
        		return $imagesList;
        	}
        }

        $selectClauseObj = new SQLSelectClause();
        $countClauseObj = new SQLSelectClause();

        // sets the where conditions
        foreach ($p_parameters as $param) {
            $comparisonOperation = self::ProcessListParameters($param);
            if (sizeof($comparisonOperation) < 3) {
                break;
            }

            if ($comparisonOperation['symbol'] == 'match') {
                $whereCondition = 'MATCH(' . $comparisonOperation['left'] . ") AGAINST("
            	    . $g_ado_db->escape($comparisonOperation['right']) . " IN BOOLEAN MODE)";
            } else {
                $whereCondition = $g_ado_db->escapeOperation($comparisonOperation);
            }
            $selectClauseObj->addWhere($whereCondition);
            $countClauseObj->addWhere($whereCondition);
        }

        // sets the columns to be fetched
        $tmpImage = new Image();
		$columnNames = $tmpImage->getColumnNames(true);
        foreach ($columnNames as $columnName) {
            $selectClauseObj->addColumn($columnName);
        }
        $countClauseObj->addColumn('COUNT(*)');

        // sets the base table
        $selectClauseObj->setTable($tmpImage->getDbTableName());
        $countClauseObj->setTable($tmpImage->getDbTableName());
        unset($tmpImage);

        // sets the ORDER BY condition
        $p_order = array_merge($p_order, self::$s_defaultOrder);
        $order = self::ProcessListOrder($p_order);
        foreach ($order as $orderDesc) {
            $orderColumn = $orderDesc['field'];
            $orderDirection = $orderDesc['dir'];
            $selectClauseObj->addOrderBy($orderColumn . ' ' . $orderDirection);
        }

        // sets the limit
        $selectClauseObj->setLimit($p_start, $p_limit);

        // builds the query and executes it
        $selectQuery = $selectClauseObj->buildQuery();

        $images = $g_ado_db->GetAll($selectQuery);
        if (is_array($images)) {
        	$countQuery = $countClauseObj->buildQuery();
        	$p_count = $g_ado_db->GetOne($countQuery);

        	// builds the array of image objects
        	$imagesList = array();
        	foreach ($images as $image) {
        		$imgObj = new Image($image['Id']);
        		if ($imgObj->exists()) {
        			$imagesList[] = $imgObj;
        		}
        	}
        } else {
        	$imagesList = array();
        	$p_count = 0;
        }
        if (!$p_skipCache && CampCache::IsEnabled()) {
        	$cacheListObj->storeInCache($imagesList);
        }

        return $imagesList;
    } // fn GetList


    /**
     * Processes a parameter (condition) coming from template tags.
     *
     * @param array $p_param
     *      The array of parameters
     *
     * @return array $comparisonOperation;
     *      The array containing processed values of the condition
     */
    private static function ProcessListParameters($p_param)
    {
    	$comparisonOperation = array();
    	$comparisonOperation['right'] = $p_param->getRightOperand();

    	switch (strtolower($p_param->getLeftOperand())) {
    		case 'search':
    			$comparisonOperation['left'] = 'Images.Description, Images.Photographer, Images.Place';
    			break;
    		case 'description':
    			$comparisonOperation['left'] = 'Images.Description';
    			break;
    		case 'photographer':
    			$comparisonOperation['left'] = 'Images.Photographer';
    			break;
    		case 'place':
    			$comparisonOperation['left'] = 'Images.Place';
    			break;
    		case 'date':
    			$comparisonOperation['left'] = 'Images.Date';
    			break;
    		case 'local':
    			$comparisonOperation['left'] = 'Images.Location';
    			$comparisonOperation['right'] = 'local';
    			break;
    		case 'type':
    			$comparisonOperation['left'] = 'Images.ContentType';
    			$comparisonOperation['right'] = 'image/' . $p_param->getRightOperand();
    			break;
    		case 'last_modified':
    			$comparisonOperation['left'] = 'Images.LastModified';
    			break;
    		case 'status':
    		    $comparisonOperation['right'] = strtolower($comparisonOperation['right']);
    		    if ($comparisonOperation['right'] == 'approved'
    		    || $comparisonOperation['right'] == 'unapproved') {
    		        $comparisonOperation['left'] = 'Images.Status';
    		    }
    		    break;
            case 'user':
                $comparisonOperation['left'] = 'Images.UploadedByUser';
                break;
    		default:
    			return null;
    	}

        $operatorObj = $p_param->getOperator();
        $comparisonOperation['symbol'] = $operatorObj->getSymbol('sql');

        return $comparisonOperation;
    } // fn ProcessListParameters


    /**
     * Processes an order directive coming from template tags.
     *
     * @param array $p_order
     *      The array of order directives in the format:
     *      array('field'=>field_name, 'dir'=>order_direction)
     *      field_name can take one of the following values:
     *        bydescription, byphotographer, bydate, bylastdate
     *      order_direction can take one of the following values:
     *        asc, desc
     *
     * @return array
     *      The array containing processed values of the condition
     */
    private static function ProcessListOrder(array $p_order)
    {
        $order = array();
        foreach ($p_order as $orderDesc) {
            $field = $orderDesc['field'];
            $direction = $orderDesc['dir'];
            $dbField = null;
            switch (strtolower($field)) {
            	case 'default':
                    $dbField = 'Images.Id';
                    break;
                case 'bydescription':
                    $dbField = 'Images.Description';
                    break;
                case 'byphotographer':
                    $dbField = 'Images.Photographer';
                    break;
                case 'bydate':
                    $dbField = 'Images.Date';
                    break;
                case 'bylastupdate':
                	$dbField = 'Images.LastModified';
                	break;
            }
            if (!is_null($dbField)) {
                $direction = !empty($direction) ? $direction : 'asc';
                $order[] = array('field'=>$dbField, 'dir'=>$direction);
            }
        }
        return $order;
    }


    /**
     * Process multi-upload file.
     *
     * @param string $p_tmpFile
     * @param string $p_newFile
     * @param int $p_userId
     * @param int $p_attributes
     *
     * @return Image|NULL
     */
    public static function ProcessFile($p_tmpFile, $p_newFile, $p_userId = NULL, $p_attributes = NULL)
    {
        $tmp_name = $GLOBALS['Campsite']['IMAGE_DIRECTORY'] . $p_tmpFile;
        $image_ary = getimagesize($tmp_name);

        $file = array(
            'name' => $p_newFile,
            'tmp_name' => $tmp_name,
            'type' => $image_ary['mime'],
            'size' => filesize($tmp_name),
            'error' => 0,
        );

        $attributes = array(
            'Description' => '',
            'Photographer' => '',
            'Place' => '',
            'Date' => '',
            'Source' => 'local',
            'Status' => 'approved'
        );

        if ($p_attributes != NULL && is_array($p_attributes)) {
			foreach ($p_attributes as $key => $value) {
				$attributes[$key] = $value;
			}
		}

        try {
            $image = self::OnImageUpload($file, $attributes, $p_userId);
            return $image;
        } catch (PEAR_Error $e) {
            return NULL;
        }

    } // fn ProcessImage

    /**
     * Chmod if user has rights
     *
     * @param string $path
     * @param int $mode
     * @return void
     */
    private static function chmod($path, $mode)
    {
        if (posix_getuid() === fileowner($path)) {
            chmod($path, $mode);
        }
    }
}
