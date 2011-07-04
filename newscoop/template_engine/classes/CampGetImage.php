<?php

require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/CampRequest.php');

/**
 * Class CampGetImage
 */
class CampGetImage
{
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
    public function __construct($p_imageId, $p_imageRatio=100, $p_imageWidth = 0, $p_imageHeight = 0)
    {
        $this->m_basePath = $GLOBALS['g_campsiteDir'].'/images/';
        $this->m_ttl = SystemPref::Get('ImagecacheLifetime');

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
        $this->GetImage($p_imageId);
    }   // fn __construct


    /**
     * Sets path to the local image file.
     *
     * @param string $p_path
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
        } elseif ($this->m_resizeWidth > 0 || $this->m_resizeHeight > 0) {
        	$derivates = $this->m_derivates_dir.$this->m_resizeWidth.'x'.$this->m_resizeHeight.'/';
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
     */
    private function ReadImage($p_ending)
    {
        $func = 'imagecreatefrom'.$p_ending;;
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


    /**
     * Sends headers and output image
     * Send image to resize if need
     *
     */
    private function PushImage()
    {
        header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        //        header('Cache-Control: no-store, no-cache, must-revalidate');
        //        header('Cache-Control: post-check=0, pre-check=0', false);
        //        header('Pragma: no-cache');
        header('Content-type: ' . $this->m_image->getContentType());

        if ($this->m_isLocal && $this->m_ratio == 100
	        && $this->m_resizeWidth == 0 && $this->m_resizeHeight == 0) {
            // do not cache local 100% images
            readfile($this->getSourcePath());

        } else {
            $this->imageCacheHandler();
        }
    }  // fn PushImage


    /**
     * resizes image
     *
     * @param resource $p_im
     */
    private function ResizeImage($p_im)
    {
        $w_src = imagesx($p_im);
        $h_src = imagesy($p_im);
	if ($this->m_ratio > 0 && $this->m_ratio <= 100) {
	    $ratio = $this->m_ratio / 100;
	    $w_dest = @round($w_src * $ratio);
	    $h_dest = @round($h_src * $ratio);
	} else {
	    // if both width and height are set, get the smaller resulting
	    // image dimension
	    if ($this->m_resizeWidth > 0 && $this->m_resizeHeight > 0) {
	        $h_dest = (100 / ($w_src / $this->m_resizeWidth)) * 0.01;
		$h_dest = @round($h_src * $h_dest);
		if ($h_dest < $this->m_resizeHeight) {
		    $w_dest = $this->m_resizeWidth;
		} else {
		    $w_dest = (100 / ($h_src / $this->m_resizeHeight)) * 0.01;
		    $w_dest = @round($w_src * $w_dest);
		    $h_dest = $this->m_resizeHeight;
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
            return basename($this->m_image->getImageStorageLocation());
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

        $cachedir  = dirname($this->getTargetPath());
        $cachefile = basename($this->getTargetPath());

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
        header('Campsite-Image-Cache: Cache created at '.date('r', filemtime($this->getTargetPath())));
        return readfile($this->getTargetPath()) !== false;
    }

    private function createImage($p_target=null)
    {
        $func_ending = $this->GetEnding();
        $t = $this->ReadImage($func_ending);
        $t = $this->ResizeImage($t);
        $function = 'image'.$func_ending;

        if (!$p_target) {
            header('Campsite-Image-Cache: disabled');
            return $function($t);
        } else {
            $function($t, $p_target);
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
