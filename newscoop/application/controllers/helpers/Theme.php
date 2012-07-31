<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Action_Helper_Theme extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var string
     */
    private $path;

    /**
     * Get theme path
     *
     * @return string
     */
    public function getPath()
    {
        if ($this->path === null) {
            $uri = CampSite::GetURIInstance();
            $this->path = realpath(APPLICATION_PATH . '/../themes/' . $uri->getThemePath());
        }

        return $this->path;
    }

    /**
     * Get theme file path
     *
     * @param string $file
     * @return string
     */
    public function getFilePath($file)
    {
        return realpath($this->getPath() . '/' . $file);
    }
}
