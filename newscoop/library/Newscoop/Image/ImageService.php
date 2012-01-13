<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

use Nette\Image as NetteImage;

require_once __DIR__ . '/../../Nette/exceptions.php';

/**
 * Image Service
 */
class ImageService
{
    /**
     * @var array
     */
    private $config = array();

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get image src
     *
     * @param string $image
     * @param int $width
     * @param int $height
     * @param string $instructions
     * @return string
     */
    public function getSrc($image, $width, $height, $instructions = 'center_center')
    {
        return implode('/', array(
            $this->config['cache_url'],
            "{$width}x{$height}",
            $instructions,
            rawurlencode($image),
        ));
    }

    /**
     * Generate image for given src
     *
     * @param string $src
     * @return void
     */
    public function generateFromSrc($src)
    {
        $matches = array();
        if (!preg_match('#' . $this->config['cache_url'] . '/([0-9]+)x([0-9]+)/([_a-z0-9]+)/([._%a-zA-Z0-9]+)$#', $src, $matches)) {
            return;
        }

        list(, $width, $height, $instructions, $imagePath) = $matches;

        $destFolder = rtrim($this->config['cache_path'], '/') . '/' . dirname(ltrim($src, './'));

        if (!realpath($destFolder)) {
            mkdir($destFolder, 0755, true);
        }

        if (!is_dir($destFolder)) {
            throw new \RuntimeException("Can't create folder '$destFolder'.");
        }

        $image = NetteImage::fromFile(APPLICATION_PATH . '/../' . rawurldecode($imagePath));
        $image->resize($width, $height, NetteImage::FILL)->crop('50%', '50%', $width, $height);
        $image->save($destFolder . '/' . $imagePath);
        $image->send();
    }
}
