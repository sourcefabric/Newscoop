<?php
/**
 * Includes
 *
 * We indirectly reference the DOCUMENT_ROOT so we can enable
 * scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
 * is not defined in these cases.
 */
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/db_connect.php');
require_once($g_documentRoot.'/template_engine/classes/CampRequest.php');

/**
 * Class CampGetImage
 */
class CampGetImage
{
    /**
     * @param string $m_imageSource
     *      Path to a local file.
     */
    private $m_imageSource = '';

    /**
     * @param string $m_imageTarget
     *      Path to a local derivate file.
     */
    private $m_imageTarget = '';

    /**
     * @param string $m_isLocal
     *      Flag if image is local ore remote.
     */
    private $m_isLocal = TRUE;

    /**
     * @param array $m_imageMetaData
     *      Consists name, type and url of the image
     */
    private $m_imageMetaData = array();


    /**
     * @param integer $m_ratio
     *      resize ratio in percent
     */
    private $m_ratio = 100;

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
     * @param integer $p_imageNr
     *      The image number within the article
     * @param integer $p_articleNr
     *      The article number
     * @param integer $p_imageRatio
     *      The ratio for image resize
     */
    public function __construct($p_imageNr, $p_articleNr, $p_imageRatio=100)
    {
        $this->m_basePath = $_SERVER['DOCUMENT_ROOT'].'/images/';

        if (empty($p_articleNr) || empty($p_imageNr)
        || !is_numeric($p_articleNr) || !is_numeric($p_imageNr)) {
            $this->ExitError('Invalid parameters');
        }
        if($p_imageRatio>0 && $p_imageRatio<100){
            $this->m_ratio = $p_imageRatio;
        }
        $this->GetImage($p_imageNr, $p_articleNr);
    }   // fn __construct


    /**
     * Sets path to the local image file.
     *
     * @param string $p_path
     */
    public function setSourcePath()
    {
        if (!$this->m_isLocal) {
            $fetched = $this->m_cache_dir.$this->m_fetch_dir;       
        }
        
        if ($this->CheckLocalFile($this->m_basePath.$fetched.$this->getLocalFileName())) {
            $this->m_isLocal = true;
            $this->m_imageSource = $this->m_basePath.$fetched.$this->getLocalFileName();
            
        } elseif ($this->CheckRemoteFile($this->m_imageMetaData['URL'])) {
            $this->m_isLocal = false;
            $this->m_imageSource = $this->m_imageMetaData['URL'];
            
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
        if (!$this->m_isLocal) {
            $fetched = $this->m_fetch_dir;       
        }
        if ($this->m_ratio < 100) {
            $derivates = $this->m_derivates_dir.$this->m_ratio.'/';    
        } 
        
        return $this->m_basePath.$this->m_cache_dir.$fetched.$derivates.$this->getLocalFileName();
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
        switch($this->m_imageMetaData['ContentType']){
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
        header('Content-type: ' . $this->m_imageMetaData['ContentType']);

        if ($this->m_isLocal && $this->m_ratio == 100) {
            // do not cache local 100% images
            readfile($this->getSourcePath());
            
        } else {
            $res = $this->imageCacheHandler();
    
            switch ($res) {
                case 'target_exists':
                case 'target_created':
                    readfile($this->getTargetPath());
                break;
                default:
                    // image was already send to brwoser by buildImageCache()
                break;
            }
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

        $ratio = $this->m_ratio/100;
        $w_dest = round($w_src*$ratio);
        $h_dest = round($h_src*$ratio);

        $dest = imagecreatetruecolor($w_dest,$h_dest);
        imagecopyresized($dest, $p_im, 0, 0, 0, 0, $w_dest, $h_dest, $w_src, $h_src);
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
        if (empty($this->m_imageMetaData['URL'])) {
            return $this->m_imageMetaData['ImageFileName'];
        } else {
            return md5($this->m_imageMetaData['URL']).'.'.$this->getEnding();
        }
    }


    /**
     * Receives image name, type and url if any from DB.
     *
     * @param string $p_imageNr
     * @param string $p_articleNr
     */
    private function GetImage($p_imageNr, $p_articleNr)
    {
        global $g_ado_db;

        $query = 'SELECT `Images`.`URL`, `Images`.`ImageFileName`, `Images`.`ContentType`
                  FROM `Images`, `ArticleImages`
                  WHERE `Images`.`Id` = `ArticleImages`.`IdImage`
                  AND `ArticleImages`.NrArticle = "'.$g_ado_db->Escape($p_articleNr).'"
                  AND `ArticleImages`.`Number` = "'.$g_ado_db->Escape($p_imageNr).'"
                  LIMIT 1';

        $this->m_imageMetaData = $g_ado_db->GetRow($query);

        if(empty($this->m_imageMetaData)){
            $this->ExitError('Image not found');
        }
        
        if (!$this->setSourcePath()) {
            $this->ExitError('File "'.$this->m_imageMetaData['ImageFileName'].$this->m_imageMetaData['URL'].'" not found');
        }

        $this->PushImage();
    } // fn GetImage


    /**
     * Create the cached version of an image.
     * If failed, send the derivate to the browser.
     *
     * @return unknown
     */
    private function imageCacheHandler()
    {
        if (is_readable($this->getTargetPath())) {
            // cached image exists
            return 'target_exists';
        }

        $cachedir  = dirname($this->getTargetPath());
        $cachefile = basename($this->getTargetPath());

        $func_ending = $this->GetEnding();
        $t = $this->ReadImage($func_ending);
        $t = $this->ResizeImage($t);
        $function = 'image'.$func_ending;

        if (is_dir($cachedir) && is_writable($cachedir)) {
            $function($t, $this->getTargetPath());
            return 'target_created';
        } else {
            // try to create the folder
            if (self::MkDirRecursive($cachedir)) {
                $function($t, $this->getTargetPath());
                return 'target_created';
            } else {
                // fallback without caching
                return $function($t, '');
            }
        }
    }


    /**
     *  Create an directory tree.
     *
     * @param string $p_dir
     * @return boolean
     */
    static private function MkdirRecursive($p_dir) {
        foreach (explode('/', $p_dir) as $piece) {
            $subdir .= '/'.$piece;
            @mkdir($subdir);
        }
        if (is_dir($p_dir) && is_writable($p_dir)) {
            return true;
        }
        return false;
    }
} // class CampGetImagePlus

?>