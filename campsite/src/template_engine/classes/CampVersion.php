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
    private $m_organization = 'Sourcefabric o.p.s';

    /**
     * @var string
     */
    private $m_package = 'Campsite';

    /**
     * @var string
     */
    private $m_release = '3.4.1';

    /**
     * @var string
     */
    private $m_devStatus = '';

    /**
     * @var string
     */
    private $m_codeName = 'Luca';

    /**
     * @var string
     */
    private $m_releaseDate = '2010-07-30';

    /**
     * @var string
     */
    private $m_copyrightYear = 2010;

    /**
     * @var string
     */
    private $m_license = 'GNU GPL v.2';

    /**
     * @var string
     */
    private $m_website = 'http://www.sourcefabric.org';


    /**
     * Class constructor
     */
    final public function __construct() { } // fn __construct


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
    } // fn getOrganization


    public function getPackage()
    {
        return $this->m_package;
    } // fn getPackage


    public function getRelease()
    {
        return $this->m_release;
    } // fn getRelease


    public function getDevelopmentStatus()
    {
        return $this->m_devStatus;
    } // fn getDevelopmentStatus


    public function getCodeName()
    {
        return $this->m_codeName;
    } // fn getCodeName


    public function getReleaseDate()
    {
        return $this->m_releaseDate;
    } // fn getReleaseDate


    public function getCopyright()
    {
        return 'Copyright &copy; ' . $this->m_organization . ' '
        . $this->m_copyrightYear . '. All rights reserved.';
    } // fn getCopyright


    public function getLicense()
    {
        return $this->m_license;
    } // fn getLicense


    public function getWebURL()
    {
        return $this->m_webURL;
    } // fn getWebURL


    function getFullInfo()
    {
        $text  = $this->m_package.' '.$this->getVersion();
        $text .= ' '.$this->m_releaseDate;
        $text .= ' '.$this->m_organization;
        return $text;
    } // fn getFullInfo

} // fn class CampVersion

?>