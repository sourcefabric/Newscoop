<?
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DatabaseObject.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/include/Yahc.class.php');

define('CAMPSITE_IMAGE_DIRECTORY', '/images/');
define('CAMPSITE_IMAGE_PREFIX', 'cms-image-');
define('CAMPSITE_IMAGEMAGICK', TRUE);
define('CAMPSITE_THUMBNAIL_COMMAND', 'convert -sample 64x64');
define('CAMPSITE_THUMBNAIL_DIRECTORY', '/images/thumbnails/');
define('CAMPSITE_THUMBNAIL_PREFIX', 'cms-thumb-');
// TODO: this should be replaced with the php.ini defined temp directory.
define('CAMPSITE_TMP_DIR', '/tmp/');

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
		'LastModified',
		'TimeCreated');

	/**
	 *
	 *
	 */
	function Image($p_imageId = null) 	{
		parent::DatabaseObject($this->m_columnNames);
		if (!is_null($p_imageId)) {
			$this->setProperty('Id', $p_imageId, false);
			$this->fetch();
		}
	} // constructor

	
	/**
	 * Delete the row from the database, all article references to this image,
	 * and the file(s) on disk.
	 *
	 * @return boolean
	 *		TRUE if the record was deleted, false if not.
	 */
	function delete() {
		// Delete all the references to this image.
		Article::OnImageDelete($this->getImageId());
		
		// Delete the record in the database
		parent::delete();
		
		// Delete the images from disk
		if (file_exists($this->getImageStorageLocation()) 
			&& is_file($this->getImageStorageLocation())) {
			unlink($this->getImageStorageLocation());
		}
		if (file_exists($this->getThumbnailStorageLocation()) 
			&& is_file($this->getThumbnailStorageLocation())) {
			unlink($this->getThumbnailStorageLocation());
		}
		return true;
	} // fn delete
	
	
	/**
	 * Return true if the image is being used by an article.
	 *
	 * @return boolean
	 */
	function inUse() {
		global $Campsite;
		$queryStr = 'SELECT IdImage FROM ArticleImages WHERE IdImage='.$this->getImageId();
		if ($Campsite['db']->GetOne($queryStr)) {
			return true;
		}
		else {
			return false;
		}
	} // fn inUse
	
	
	/**
	 * @return int
	 */
	function getImageId() {
		return $this->getProperty('Id');
	} // fn getImageId
	
	
	/**
	 * @return string
	 */
	function getDescription() {
		return $this->getProperty('Description');
	} // fn getDescription
	
	
	/**
	 * @return string
	 */
	function getPhotographer() {
		return $this->getProperty('Photographer');
	} // fn getPhotographer
	
	
	/**
	 * @return string
	 */
	function getPlace() {
		return $this->getProperty('Place');
	} // fn getPlace
	
	
	/**
	 * @return string
	 */
	function getDate() {
		return $this->getProperty('Date');
	} // fn getDate
	
	
	/**
	 * @return string
	 */
	function getLocation() {
		return $this->getProperty('Location');
	} // fn getLocation
	
	
	/**
	 * @return string
	 */
	function getUrl() {
		return $this->getProperty('URL');
	} // fn getUrl
	
	
	/**
	 * @return string
	 */
	function getContentType() {
		return $this->getProperty('ContentType');
	} // fn getContentType
	
	
	/**
	 * Return the full path to the image file.
	 * @return string
	 */
	function getImageStorageLocation() {
		return $_SERVER['DOCUMENT_ROOT'].CAMPSITE_IMAGE_DIRECTORY
			.$this->m_data['ImageFileName'];
	} // fn getImageStorageLocation
	
	
	/**
	 * Return the full path to the thumbnail file.
	 * @return string
	 */
	function getThumbnailStorageLocation() {
		return $_SERVER['DOCUMENT_ROOT'].CAMPSITE_THUMBNAIL_DIRECTORY
			.$this->m_data['ThumbnailFileName'];
	} // fn getThumbnailStorageLocation
	
	
	/**
	 * Return the full URL to the image image.
	 * @return string
	 */
	function getImageUrl() {
		global $Campsite;
		if ($this->m_data['Location'] == 'local') {
			return $Campsite['website_url'].CAMPSITE_IMAGE_DIRECTORY.$this->m_data['ImageFileName'];
		}
		else {
			return $this->m_data['URL'];
		}
	} // fn getImageUrl
	
	
	/**
	 * Get the full URL to the thumbnail image.
	 * @return string
	 */
	function getThumbnailUrl() {
		global $Campsite;
		return $Campsite['website_url'].CAMPSITE_THUMBNAIL_DIRECTORY.$this->m_data['ThumbnailFileName'];
	} // fn getThumbnailUrl
	
	
	function GetMaxId() {
		global $Campsite;
		$queryStr = 'SHOW TABLE STATUS LIKE "Images"';
		$result = $Campsite['db']->getRow($queryStr);
		return $result['Auto_increment'];
	} // fn GetMaxId
	
	
	function GetTotalImages() {
		global $Campsite;
		$queryStr = 'SHOW TABLE STATUS LIKE "Images"';
		$result = $Campsite['db']->getRow($queryStr);
		return $result['Rows'];
	} // fn GetTotalImages
	
	
	/**
	 * This function should be called when an image is uploaded.  It will save
	 * the image to the appropriate place on the disk, create a thumbnail for it,
	 * and create a database entry for the file.
	 *
	 * @param array p_fileVar
	 * 		The variable from the $_FILES array.  The array specifies the following:
	 *		$a["name"] = original name of the file.
	 * 		$a["type"] = the MIME type of the file, e.g. image/gif
	 *		$a["tmp_name"] = the temporary storage location on disk of the file
	 *		$a["size"] = size of the file, in bytes
	 *		$a["error"] = 0 (zero) if there was no error
	 *
	 * @param array p_attributes
	 *		Optional attributes which are stored in the database.
	 *		Indexes can be the following: 'description', 'photographer', 'place', 'date'
	 *
	 * @param int p_id
	 *		If the image already exists and we just want to update it, specify the
	 *		current image ID here.
	 *
	 * @return Image
	 *		The Image object that was created or updated.
	 */
	function OnImageUpload($p_fileVar, $p_attributes, $p_id = null) {
		if (!is_array($p_fileVar)) {
			return null;
		}
	 	if (!is_null($p_id)) {
	 		$image =& new Image($p_id);
	 		$image->update($p_attributes);
		    if ($p_fileVar['type'] != $image->getContentType()) {
		    	// Remove the old image & thumbnail.
		    	unlink($image->getImageStorageLocation());
		    	unlink($image->getThumbnailStorageLocation());
		    }
	    } else {
	    	$image =& new Image();
	    	$image->create($p_attributes);
	    }
		$image->setProperty('ContentType', $p_fileVar['type']);
		
		$fileExtension = split("\.", $p_fileVar['name']);
		$fileExtension = $fileExtension[(count($fileExtension)-1)];
	    $target = $_SERVER['DOCUMENT_ROOT'].CAMPSITE_IMAGE_DIRECTORY
	    	.CAMPSITE_IMAGE_PREFIX.sprintf('%09d', $image->getImageId()).'.'.$fileExtension;
	    $thumbnail = $_SERVER['DOCUMENT_ROOT'].CAMPSITE_THUMBNAIL_DIRECTORY
	    	.CAMPSITE_THUMBNAIL_PREFIX.sprintf('%09d', $image->getImageId()).'.'.$fileExtension;
	    $image->setProperty('ImageFileName', basename($target));
	    $image->setProperty('ThumbnailFileName', basename($thumbnail));
	    
        if (!move_uploaded_file ($p_fileVar['tmp_name'], $target)) {
             return getGS('Unable to move Image to <B>$1</B>', $target);
        }
		chmod($target, 0644);
        if (CAMPSITE_IMAGEMAGICK) {
            $cmd = CAMPSITE_THUMBNAIL_COMMAND.' '.$target.' '.$thumbnail;
            #echo $cmd;
            system($cmd);
            chmod($thumbnail, 0644);
        }
        return $image;
	} // fn OnImageUpload
	
	
	/**
	 * Download the remote file and save it to disk, create a thumbnail for it,
	 * and create a database entry for the file.
	 *
	 * @param string p_url
	 *		The remote location of the file. ("http://...");
	 *
	 * @param array p_attributes
	 *
	 * @param int p_id
	 *		If you are updating an image, specify its ID here.
	 *
	 * @return void
	 */
	function OnAddRemoteImage($p_url, $p_attributes, $p_id = null) {
	    $data =& new Yahc($p_url, 'CAMPWARE');
	    $data->request_protocol = 'HTTP/1.0';
	    $data->request_method = 'GET';
	    if ($data->connect()) {
	        // URL OK
	        $data->send_request();
	        $data->get_response();
	        $hrows = explode ("\r\n", $data->response_HEADER);
	        foreach ($hrows as $row) {
	            if (preg_match('/Content-Type:/', $row)) {
	                $ctype = trim(substr($row, strlen('Content-Type:')));
	            }
	        }
	        if (preg_match('/image/', $ctype)) {
	            // content-type = image
	            if (!is_null($p_id)) {
	            	$image =& new Image($p_id);
	            	$image->update($p_attributes);
	            } else {
	            	$image =& new Image();
	            	$image->create($p_attributes);
	            }
	
	            if (CAMPSITE_IMAGEMAGICK) {
	                $tmpname =CAMPSITE_TMP_DIR.'img'.md5(rand());
	                if ($tmphandle = fopen($tmpname, 'w')) {
	                    fwrite($tmphandle, $data->response_HTML);
	                    fclose($tmphandle);
	                    $cmd = CAMPSITE_THUMBNAIL_COMMAND.' '
	                    	. $tmpname . ' ' . $image->getThumbnailStorageLocation();
	                    system($cmd);
	                    unlink($tmpname);
	                } else {
	                    return getGS('Cannot create <B>$1</B>', $tmpname);
	                }
	            }
	        } else {
	            // wrong URL
	            return getGS('URL <B>$1</B> have wrong content type <B>$2</B>', $cURL, $ctype);
	        }
	    } else {
	        // no connection
	        return getGS('Unable to read image from <B>$1</B>', $cURL);
	    }
	    return $image;
	} // fn OnAddRemoteImage

	
	/**
	 *
	 * @return array
	 */
	function getArticlesThatUseImage() {
		global $Campsite;
		$article =& new Article();
		$columnNames = $article->getColumnNames();
		$columnQuery = array();
		foreach ($columnNames as $columnName) {
			$columnQuery[] = 'Articles.'.$columnName;
		}
		$columnQuery = implode(',', $columnQuery);
		$queryStr = 'SELECT '.$columnQuery.' FROM Articles, ArticleImages '
					.' WHERE ArticleImages.IdImage='.$this->getProperty('Id')
					.' AND ArticleImages.NrArticle=Articles.Number';
		$rows =& $Campsite['db']->GetAll($queryStr);
		$articles = array();
		if (is_array($rows)) {
			foreach ($rows as $row) {
				$tmpArticle =& new Article();
				$tmpArticle->fetch($row);
				$articles[] =& $tmpArticle;
			}
		}
		return $articles;
	} // fn getArticlesThatUseImage
	
	
	function toTemplate() {
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
	
} // class Image
?>