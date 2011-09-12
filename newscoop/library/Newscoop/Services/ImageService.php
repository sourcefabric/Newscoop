<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

/**
 */
class ImageService
{
    /** @var Zend_View_Abstract */
    private $view;

    /** @var array */
    private $supportedTypes = array(
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/bmp',
    );

    /**
     * @param Zend
     */
    public function __construct(\Zend_View_Abstract $view)
    {
        $this->view = $view;
    }

    /**
     * Save image
     *
     * @param string $info
     * @return string
     */
    public function save(array $info)
    {
        if (!in_array($info['type'], $this->supportedTypes)) {
            throw new \InvalidArgumentException("Unsupported image type '$info[type]'.");
        }

        $name = sha1_file($info['tmp_name']) . '.' . array_pop(explode('.', $info['name']));
        if (!file_exists(APPLICATION_PATH . "/../images/$name")) {
            rename($info['tmp_name'], APPLICATION_PATH . "/../images/$name");
        }

        return $name;
    }

    /**
     * Get src for image
     *
     * @param string $path
     * @param int $width
     * @param int $height
     * @return string
     */
    public function getSrc($path, $width = 400, $height = 300)
    {
        if (empty($path)) {
            return;
        }

        return $this->view->url(array(
            'image' => $path,
            'width' => $width,
            'height' => $height,
        ), 'image');
    }
}
