<?php

require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/CampRequest.php');

/**
 * Class CampGetImage
 */
class CampGetImage
{
    const RFC1123 = 'D, d M Y H:i:s T';

    /**
     * @var string $m_imageSource
     *      Path to a local file.
     */
    private $m_imageSource = '';

    /**
     * @var string $m_imageTarget
     *      Path to a local derivate file.
     */
    private $m_imageTarget = '';

    /**
     * @var string $m_isLocal
     *      Flag if image is local ore remote.
     */
    private $m_isLocal = TRUE;

    /**
     * @var Image $m_image
     *      Consists name, type and url of the image
     */
    private $m_image = null;

    /**
     * @var integer $m_ratio
     *      resize ratio in percent
     */
    private $m_ratio = 100;

    /**
     * @var integer $m_resizeWidth
     *      resize width in pixels
     */
    private $m_resizeWidth = 0;

    /**
     * @var integer $m_resizeHeight
     *      resize height in pixels
     */
    private $m_resizeHeight = 0;

    /**
     * @var string
     * 		crop image from
     * 		top-left, top-right, bottom-left, bottom-right
     * 		center-left, center-right, top-center, bottom-center
     * 		top(-center), bottom(-center), (center-)left, (center-)right
     * 		center(-center)
     */
    private $m_crop = null;

    /**
     * @var string
     * 		after resizing
     * 		crop image from
     * 		top, center, bottom, left, right
     */
    private $m_resizeCrop = null;

	/**
     * @param integer $m_ttl
     *      ttl for cached files
     */
    private $m_ttl = 0;

    private $m_basePath;

    private $m_cache_dir = '/image_cache/';

    private $m_derivates_dir = '/derivates/';

    private $m_fetch_dir = '/fetched/';


    /**
     * Class constructor.
     *
     * @param integer $p_imageId
     *      The image identifier
     * @param integer $p_imageRatio
     *      The ratio for image resize
     * @param integer $p_imageWidth
     *      The max width for image resize
     * @param integer $p_imageHeight
     *      The max height for image resize
     */
    public function __construct($p_imageId, $p_imageRatio=100, $p_imageWidth = 0, $p_imageHeight = 0, $p_imageCrop = null, $p_resizeCrop = null)
    {
        $this->m_basePath = $GLOBALS['g_campsiteDir'].'/images/';
        $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');
        $this->m_ttl = $preferencesService->ImagecacheLifetime;

        if (empty($p_imageId) || !is_numeric($p_imageId)) {
            $this->ExitError('Invalid parameters');
        }
        if($p_imageRatio > 0 && $p_imageRatio < 100) {
            $this->m_ratio = $p_imageRatio;
        }
        if($p_imageWidth > 0) {
            $this->m_resizeWidth = $p_imageWidth;
        }
        if($p_imageHeight > 0) {
            $this->m_resizeHeight = $p_imageHeight;
        }
        if (!is_null($p_imageCrop)) {
            $availableCropOptions = array(
				'top-left', 'top-right', 'bottom-left', 'bottom-right',
				'center-left', 'center-right', 'top-center', 'bottom-center',
				'top', 'bottom', 'left', 'right', 'center', 'center-center'
			);

			if (in_array($p_imageCrop, $availableCropOptions)) {
				if ($p_imageCrop == 'top' || $p_imageCrop == 'bottom') {
					$p_imageCrop = $p_imageCrop.'-center';
				}
				if ($p_imageCrop == 'left' || $p_imageCrop == 'right') {
					$p_imageCrop = 'center-'.$p_imageCrop;
				}
				if ($p_imageCrop == 'center') {
					$p_imageCrop = 'center-center';
				}
				$this->m_crop = $p_imageCrop;
			}
        }
        if (!is_null($p_resizeCrop)) {
            $availableCropOptions = array(
				'top', 'bottom', 'left', 'right', 'center'
			);

			if (in_array($p_resizeCrop, $availableCropOptions)) {
				$this->m_resizeCrop = $p_resizeCrop;
			}
        }
        $this->GetImage($p_imageId);
    }   // fn __construct


    /**
     * Sets path to the local image file.
     *
     */
    public function setSourcePath()
    {
        $fetched = !$this->m_isLocal ? $this->m_cache_dir.$this->m_fetch_dir : null;
        if ($this->CheckLocalFile($this->m_basePath.$fetched.$this->getLocalFileName())) {
            $this->m_isLocal = true;
            $this->m_imageSource = $this->m_basePath.$fetched.$this->getLocalFileName();

        } elseif ($this->CheckRemoteFile($this->m_image->getUrl())) {
            $this->m_isLocal = false;
            $this->m_imageSource = $this->m_image->getUrl();

        } else {
            return false;
        }
        return true;
    }   // fn setSourcePath


    /**
     * Returns path to the local image file.
     *
     */
    public function getSourcePath()
    {
        return $this->m_imageSource;
    }   // fn getSourcePath

    /**
     * Returns path to the local image derivate file.
     *
     */
    public function getTargetPath()
    {
        $fetched = !$this->m_isLocal ? $this->m_fetch_dir : null;
        $derivates = null;
        if ($this->m_ratio > 0 && $this->m_ratio < 100) {
            $derivates = $this->m_derivates_dir.$this->m_ratio.'/';
        } elseif ($this->m_resizeCrop == null && $this->m_crop == null && ($this->m_resizeWidth > 0 || $this->m_resizeHeight > 0)) {
        	$derivates = $this->m_derivates_dir.$this->m_resizeWidth.'x'.$this->m_resizeHeight.'/';
        } elseif ($this->m_resizeCrop != null) {
            $derivates = $this->m_derivates_dir.$this->m_resizeWidth.'x'.$this->m_resizeHeight.'_crop_'.$this->m_resizeCrop.'/';
        } elseif ($this->m_crop != null) {
            $derivates = $this->m_derivates_dir.$this->m_resizeWidth.'x'.$this->m_resizeHeight.'_forcecrop_'.$this->m_crop.'/';
        }

        $path = $this->m_basePath.$this->m_cache_dir.$fetched.$derivates.$this->getLocalFileName();
        return $path;
    }   // fn getTargetPath


    /**
     * Writes the given error message and exit.
     *
     * @param string $p_errorMessage
     */
    public function ExitError($p_errorMessage)
    {
        header('Content-type: text/html; charset=utf-8');
        die($p_errorMessage);
    }  // fn ExitError


    /**
     * Checkes if local file exists
     *
     * @param string $p_imagePath
     */
    private function CheckLocalFile($p_imagePath)
    {
        return is_readable($p_imagePath);
    }  // fn CheckLocalFile


    /**
     * Checkes if remote file exists
     *
     * @param string $p_imageUrl
     */
    private function CheckRemoteFile($p_imageUrl)
    {
        $status = array();
        $status = get_headers($p_imageUrl);
        return (strpos($status[0],'404'))?0:1;
    }  // fn CheckRemoteFile


    /**
     * Reads image from a local or remote file.
     * Creates image string
     *
     * @param string $p_ending
     * @return resource
     */
    private function ReadImage($p_ending)
    {
        $func = 'imagecreatefrom'.$p_ending;
        return $func($this->getSourcePath());
    }  // fn ReadImage


    /**
     * Create an proper file ending for given ContenType
     *
     * @return string
     */
    private function GetEnding()
    {
        switch($this->m_image->getContentType()){
            case 'image/gif': $func_ending ='gif'; break;
            case 'image/jpeg': $func_ending ='jpeg'; break;
            case 'image/png': $func_ending ='png'; break;
            default: $func_ending ='jpeg'; break;
        }
        return $func_ending;
    }

    private function GetHeaders()
    {
        $headers = array();

        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
        }
        else {
            foreach($_SERVER as $key => $value) {
                if (strtolower(substr($key, 0, 5)) == 'http_') {
                    $key = str_replace(' ', '-' , ucwords(strtolower(str_replace('_', ' ', substr($key,5)))));
                    $headers[$key] = $value;
                } else {
                    $headers[$key] = $value;
                }
            }
        }
        return $headers;
    }

    /**
     * Sends headers and output image
     * Send image to resize if need
     *
     */
    private function PushImage()
    {
        header('Expires: ' . gmdate(self::RFC1123, time() + $this->m_ttl));
        header('Cache-Control: public, max-age=' . $this->m_ttl);
        header('Pragma: cache');

        if ($this->m_isLocal && $this->m_ratio == 100 && $this->m_resizeWidth == 0 && $this->m_resizeHeight == 0 && $this->m_crop == null && $this->m_resizeCrop == null) {
            // do not cache local 100% images

            // Getting headers sent by the client.
            $headers = $this->GetHeaders();
            $fmt = filemtime($this->getSourcePath());

            // Checking if the client is validating his cache and if it is current.
            if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == $fmt)) {
                // Client's cache IS current, so we just respond '304 Not Modified'.
                header('Last-Modified: '.gmdate(self::RFC1123, $fmt), true, 304);
            }
            else {
                // Image not cached or cache outdated, we respond '200 OK' and output the image.
                header('Last-Modified: '.gmdate(self::RFC1123, $fmt), true, 200);
                header('Content-Length: '.filesize($this->getSourcePath()));
                header('Content-Type: '.$this->m_image->getContentType());

                return readfile($this->getSourcePath());
            }

        }
        else {
            $this->imageCacheHandler();
        }

    }  // fn PushImage

	/**
     * crops image
     *
     *
     * @return resource
     */
    private function CropImage()
    {
        //list($current_width, $current_height) = getimagesize($filename);
        list($current_width, $current_height) = getimagesize($this->m_imageSource);

        // Resulting size of the image after cropping
        $width = $this->m_resizeWidth;
        $height = $this->m_resizeHeight;

        if (!$width) {
            $width = $current_width;
        }
        if (!$height) {
            $height = $current_height;
        }

        // Cropping coordinates
        $cropPosition = explode('-', $this->m_crop);
        if ($cropPosition[0] == 'top') {
            $top = 0;
        }
        if ($cropPosition[0] == 'center') {
            $top = ($current_height - $height) / 2;
        }
        if ($cropPosition[0] == 'bottom') {
            $top = ($current_height - $height);
        }
        if ($cropPosition[1] == 'left') {
            $left = 0;
        }
        if ($cropPosition[1] == 'center') {
            $left = ($current_width - $width) / 2;
        }
        if ($cropPosition[1] == 'right') {
            $left = ($current_width - $width);
        }

        // Resample the image
        $canvas = @imagecreatetruecolor($width, $height);
        $current_image = @imagecreatefromjpeg($this->getSourcePath());
        @imagecopy($canvas, $current_image, 0, 0, $left, $top, $current_width, $current_height);
        return($canvas);
    }

    /**
     * crops resized image to fit size
     *
     * @param resource $p_im
     * @return resource
     */
    private function CropResizedImage($p_im)
    {
        $current_width = imagesx($p_im);
        $current_height = imagesy($p_im);

        // Resulting size of the image after cropping
        $width = $this->m_resizeWidth;
        $height = $this->m_resizeHeight;

        if ($current_width == $width && $current_height == $height) {
			// no cropping necessary
			return($p_im);
		}

        // hcrop
        if ($width < $current_width) {
			// translate vertical and horizontal values to each other for convenience
			if ($this->m_resizeCrop == 'top') $this->m_resizeCrop = 'left';
			if ($this->m_resizeCrop == 'bottom') $this->m_resizeCrop = 'right';

			if ($this->m_resizeCrop == 'left') {
				$top = 0;
				$left = 0;
			}
			if ($this->m_resizeCrop == 'center') {
				$top = 0;
				$left = ($current_width - $width) / 2;
			}
			if ($this->m_resizeCrop == 'right') {
				$top = 0;
				$left = ($current_width - $width);
			}
		}
		// vcrop
		if ($height < $current_height) {
			// translate vertical and horizontal values to each other for convenience
			if ($this->m_resizeCrop == 'left') $this->m_resizeCrop = 'top';
			if ($this->m_resizeCrop == 'right') $this->m_resizeCrop = 'bottom';

			if ($this->m_resizeCrop == 'top') {
				$top = 0;
				$left = 0;
			}
			if ($this->m_resizeCrop == 'center') {
				$top = ($current_height - $height) / 2;
				$left = 0;
			}
			if ($this->m_resizeCrop == 'bottom') {
				$top = ($current_height - $height);
				$left = 0;
			}
		}

        // Resample the image
        $canvas = @imagecreatetruecolor($width, $height);
        @imagecopy($canvas, $p_im, 0, 0, $left, $top, $current_width, $current_height);
        return($canvas);
    }

    /**
     * resizes image
     *
     * @param resource $p_im
     * @return resource
     */
    private function ResizeImage($p_im)
    {
        $w_src = imagesx($p_im);
        $h_src = imagesy($p_im);
		if ($this->m_ratio > 0 && $this->m_ratio < 100) {
			$ratio = $this->m_ratio / 100;
			$w_dest = @round($w_src * $ratio);
			$h_dest = @round($h_src * $ratio);
		} else {
			// if both width and height are set, get the smaller resulting
			// image dimension
			// but if m_resizeCrop is set, then get the bigger resulting one
			// because we will crop the excess
			if ($this->m_resizeWidth > 0 && $this->m_resizeHeight > 0) {
				$h_dest = (100 / ($w_src / $this->m_resizeWidth)) * 0.01;
				$h_dest = @round($h_src * $h_dest);

				if ($this->m_resizeCrop == null) {
					if ($h_dest < $this->m_resizeHeight) {
						$w_dest = $this->m_resizeWidth;
					} else {
						$w_dest = (100 / ($h_src / $this->m_resizeHeight)) * 0.01;
						$w_dest = @round($w_src * $w_dest);
						$h_dest = $this->m_resizeHeight;
					}
				}
				else {
					if ($h_dest > $this->m_resizeHeight) {
						$w_dest = $this->m_resizeWidth;
					} else {
						$w_dest = (100 / ($h_src / $this->m_resizeHeight)) * 0.01;
						$w_dest = @round($w_src * $w_dest);
						$h_dest = $this->m_resizeHeight;
					}
				}
			} elseif ($this->m_resizeWidth > 0 && $this->m_resizeHeight == 0) {
				// autocompute height
				$h_dest = (100 / ($w_src / $this->m_resizeWidth)) * 0.01;
				$h_dest = @round($h_src * $h_dest);
				$w_dest = $this->m_resizeWidth;
			} elseif ($this->m_resizeHeight > 0 && $this->m_resizeWidth == 0) {
				// autocompute width
				$w_dest = (100 / ($h_src / $this->m_resizeHeight)) * 0.01;
				$w_dest = @round($w_src * $w_dest);
				$h_dest = $this->m_resizeHeight;
			}
		}
		$dest = @imagecreatetruecolor($w_dest, $h_dest);
		$imageType = $this->m_image->getContentType();

		// fix transparent backgrounds for png/gif
		if (in_array($imageType, array('image/gif', 'image/png'))) {
            imagesavealpha($dest, TRUE);
            $color = imagecolorallocatealpha($dest, 0, 0, 0, 127);

            imagealphablending($dest, FALSE);
            imagefilledrectangle($dest, 0, 0, $w_dest - 1, $h_dest - 1, $color);
            imagealphablending($dest, TRUE);
            imagecolortransparent($dest, $color);
        }

        @imagecopyresampled($dest, $p_im, 0, 0, 0, 0, $w_dest, $h_dest, $w_src, $h_src);
        return $dest;
    }  // fn ResizeImage


    /**
     * Return an local filename.
     * For the url's, use md5 hash for it.
     *
     * @return string filename
     */
    private function getLocalFileName()
    {
        if ($this->m_image->getUrl() == '') {
            return $this->m_image->getImageFileName();
        } else {
            return md5($this->m_image->getUrl()).'.'.$this->getEnding();
        }
    }


    /**
     * Receives image name, type and url if any from DB.
     *
     * @param string $p_imageId
     */
    private function GetImage($p_imageId)
    {
        $this->m_image = new Image($p_imageId);
        if (!$this->m_image->exists()) {
            $this->ExitError('Image not found');
        }
        if (!$this->setSourcePath()) {
            $this->ExitError('File "'.$this->m_image->getImageStorageLocation().$this->m_image->getUrl().'" not found');
        }

        $this->m_image->fixMissingThumbnail();
        $this->PushImage();
    } // fn GetImage


    /**
     * Create the cached version of an image.
     * If failed, send the original or dynamically created derivate to the browser.
     *
     * @return unknown
     */
    private function imageCacheHandler()
    {
        if ($this->m_ttl == 0) {
            // cache disabled
            return $this->createImage();
        }

        if (is_readable($this->getTargetPath())) {
            if ($this->cacheHasExpired()) {
                // remove cached version
                $this->removeCachedImage();
            } else {
                // use the cached image
                return $this->sendCachedImage();
            }
        }

        $targetpath = $this->getTargetPath();

        $cachedir  = dirname($targetpath);
        $cachefile = basename($targetpath);

        if (!is_dir($cachedir) || !is_writable($cachedir)) {
            // try to create the folder and cache file
            if (!self::MkDirRecursive($cachedir)) {
                // cache folder not creatable/writable
                return $this->createImage();
            }
        }

        if (!$this->createImage($this->getTargetPath())) {
            // fallback without caching
            return $this->createImage();
        }
    }

    private function sendCachedImage()
    {
        // Getting headers sent by the client.
        $headers = $this->GetHeaders();
        $fmt = filemtime($this->getTargetPath());

        // Checking if the client is validating his cache and if it is current.
        if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == $fmt)) {
            // Client's cache IS current, so we just respond '304 Not Modified'.
            header('Last-Modified: '.gmdate(self::RFC1123, $fmt), true, 304);
        }
        else {
            // Image not cached or cache outdated, we respond '200 OK' and output the image.
            header('Last-Modified: '.gmdate(self::RFC1123, $fmt), true, 200);
            header('Content-Length: '.filesize($this->getTargetPath()));
            header('Content-Type: '.$this->m_image->getContentType());

            return readfile($this->getTargetPath()) !== false;
        }
    }

    /**
     * @param string $p_target
     */
    private function createImage($p_target=null)
    {
        $func_ending = $this->GetEnding();
        $t = $this->ReadImage($func_ending);

        if ($this->m_crop == null) {
            $t = $this->ResizeImage($t);
            if ($this->m_resizeCrop != null) {
                $t = $this->CropResizedImage($t);
            }
        }
        else {
            $t = $this->CropImage($t);
        }

        $function = 'image'.$func_ending;

        if (!$p_target) {
            header('Content-type: ' . $this->m_image->getContentType());
            header('Last-Modified: ' . gmdate(self::RFC1123));

            header('Newscoop-Image-Cache: disabled');
            return $function($t);
        }
        else {
            $function($t, $p_target);
            imagedestroy($t);
            return $this->sendCachedImage();
        }
    }

    private function cacheHasExpired()
    {
        if ($this->m_ttl == -1) {
            // infinite cache
            return false;
        }

        $mtime = filemtime($this->getTargetPath());

        if (time() > $mtime + $this->m_ttl) {
            return true;
        }
        return false;
    }

    private function removeCachedImage()
    {
        unlink($this->getTargetPath());
    }


    /**
     *  Create an directory tree.
     *
     * @param string $p_dir
     * @return boolean
     */
    static private function MkdirRecursive($p_dir) {
    	$subdir = '';
        foreach (explode('/', $p_dir) as $piece) {
            $subdir .= '/'.$piece;
            @mkdir($subdir);
        }
        if (is_dir($p_dir) && is_writable($p_dir)) {
            return true;
        }
        return false;
    }
}
