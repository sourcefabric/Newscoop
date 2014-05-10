<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */

/**
 * Class CampVersion
 */
final class CampVersion
{
    /**
     * @var string
     */
    private $m_organization = 'Sourcefabric z.ú.';

    /**
     * @var string
     */
    private $m_package = 'Newscoop';

    /**
     * @var string
     */
    private $m_release = '4.3';

    /**
     * @var string
     */
    private $m_devStatus = '';

    /**
     * @var string
     */
    private $m_codeName = 'Vitor';

    /**
     * @var string
     */
    private $m_releaseDate = '2013-06-10';

    /**
     * @var string
     */
    private $m_license = 'GNU GPL v.3';

    /**
     * @var string
     */
    private $m_website = 'http://www.sourcefabric.org';

    public function getVersion() {
        $version = $this->m_release;
        if (!empty($this->m_devStatus)) {
            $version .= '-' . $this->m_devStatus;
        }
        if (!empty($this->m_codeName)) {
            $version .= ' "' . $this->m_codeName . '"';
        }
        return $version;
    }

    public function getOrganization()
    {
        return $this->m_organization;
    }

    public function getPackage()
    {
        return $this->m_package;
    }

    public function getRelease()
    {
        return $this->m_release;
    }

    public function getDevelopmentStatus()
    {
        return $this->m_devStatus;
    }

    public function getCodeName()
    {
        return $this->m_codeName;
    }

    public function getReleaseDate()
    {
        return $this->m_releaseDate;
    }

    public function getCopyright()
    {
        $c = '&copy;&nbsp;' . $this->getCopyrightYear() . '&nbsp;<a href="' . $this->m_website
            . '" target="_blank">' . $this->m_organization . '</a>';
        return $c;
    }

    public function getLicense()
    {
        return $this->m_license;
    }

    public function getWebURL()
    {
        return $this->m_website;
    }

    public function getCopyrightYear()
    {
        return date("Y");
    }

    function getFullInfo()
    {
        $text  = $this->m_package.' '.$this->getVersion();
        $text .= ' '.$this->m_releaseDate;
        $text .= ' '.$this->m_organization;
        return $text;
    }
}
