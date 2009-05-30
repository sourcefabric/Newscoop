<?php
/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleImage.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/CampRequest.php');

/**
 * Class CampGetImage
 */
class CampGetImage
{
    /**
     * @param string $m_imagePath
     *      Path to a local file.
     */
    private $m_imagePath = '';

    /**
     * @param boolean $m_location
     *      Flag if image is local ore remote.
     */
    private $m_isLocal = TRUE;

    /**
     * @param Image $m_image
     *      Consists name, type and url of the image
     */
    private $m_image = null;


    /**
     * @param integer $m_ratio
     *      resize ratio in percent
     */
    private $m_ratio = 100;


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
    public function __construct($p_imageNr, $p_articleNr,$p_imageRatio=100)
    {
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
    public function SetImagePath($p_path)
    {
        $this->m_imagePath = $p_path;
        return 0;
    }   // fn SetImagePath


    /**
     * Returns path to the local image file.
     *
     */
    public function GetImagePath()
    {
        return $this->m_imagePath;
    }   // fn GetImagePath


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
        return file_exists($p_imagePath);
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
        return $func($this->GetImagePath());
    }  // fn ReadImage
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

        if($this->m_ratio<100){
           $func_ending = '';
           switch($this->m_image->getContentType()){
               case 'image/gif':$func_ending ='gif'; break;
               case 'image/jpeg':$func_ending ='jpeg'; break;
               case 'image/png':$func_ending ='png'; break;
               default:$func_ending ='jpeg';break;
           }
           $t = $this->ReadImage($func_ending);
           $t = $this->ResizeImage($t);
           $function = 'image'.$func_ending;
           $function($t);
        }
        else{
            readfile($this->GetImagePath());
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
     * Receives image name, type and url if any from DB.
     *
     * @param string $p_imageNr
     * @param string $p_articleNr
     */
    private function GetImage($p_imageNr, $p_articleNr)
    {
        $articleImage = new ArticleImage($p_articleNr, null, $p_imageNr);
        if (!$articleImage->exists()) {
        	$this->ExitError('Image not found');
        }
        $this->m_image = new Image($articleImage->getImageId());
        if (!$this->m_image->exists()) {
            $this->ExitError('Image not found');
        }
        $url = $this->m_image->getUrl();
        $this->m_isLocal = empty($url);
        $this->SetImagePath($this->m_image->getImageStorageLocation());

        if(!($this->m_isLocal?$this->CheckLocalFile($this->GetImagePath()):
                              $this->CheckRemoteFile($this->GetImagePath()))){
            $this->ExitError('File "'.$this->GetImagePath().'" not found');
        }
        $this->PushImage();
    } // fn GetImage
} // class CampGetImagePlus

?>