<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class ImageController extends Zend_Controller_Action
{
    const DATE_FORMAT = 'D, d M Y H:i:s \G\M\T';

    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $types = array(
        'png',
        'gif',
        'tiff',
        'bmp',
    );

    public function cacheAction()
    {
        $this->getResponse()->clearHeaders();
        $this->getResponse()->setHeader('Cache-Control', sprintf('public, max-age=%d', 3600 * 24 * 30), true);
        $this->getResponse()->setHeader('Pragma', 'cache', true);
        $this->getResponse()->setHeader('Expires', gmdate(self::DATE_FORMAT, date_create('+30 days')->getTimestamp()), true);

        try {
            $image = $this->_helper->service('image')->generateFromSrc($this->_getParam('src'));
            $this->getResponse()->setHeader('Content-Type', $this->getContentType($this->getPath()), true);
            $this->getResponse()->sendHeaders();
            $this->getResponse()->setBody(file_get_contents($this->getPath()));
        } catch (\Exception $e) {
            $this->getResponse()->clearHeaders();
            $this->getResponse()->setHttpResponseCode(404);
        }

        $this->getResponse()->sendResponse();
        exit;
    }

    /**
     * Get image path
     *
     * @return string
     */
    private function getPath()
    {
        if ($this->path === null) {
            $image = \Zend_Registry::get('container')->getParameter('image');
            $this->path = implode('/', array(
                rtrim($image['cache_path'], '/'),
                $this->_getParam('src'),
            ));
        }

        return $this->path;
    }

    /**
     * Get content type for given file
     *
     * @param string $path
     * @return string
     */
    private function getContentType($path)
    {
        if (file_exists($path)) {
            $info = getimagesize($path);
            return $info['mime'];
        }

        foreach ($this->types as $type) {
            if (preg_match("/\.{$type}\$/i", $path)) {
                return "image/{$type}";
            }
        }

        return 'image/jpeg';
    }
}
