<?php

class Image extends Archive_FileBase {

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
            $cmd = $Campsite['THUMBNAIL_COMMAND'] . ' ' . escapeshellarg($tmpname)
            	 . ' ' . escapeshellarg($image->getThumbnailStorageLocation());
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

} // class Image
?>