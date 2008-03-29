<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */

/**
 * Class CampVersion
 */
final class CampVersion
{
    /**
     * @var string
     */
    private $m_organization = 'Campware - MDLF';

    /**
     * @var string
     */
    private $m_package = 'Campsite';

    /**
     * @var string
     */
    private $m_release = '3.0.0';

    /**
     * @var string
     */
    private $m_devStatus = 'rc2';

    /**
     * @var string
     */
    private $m_codeName = 'Filip';

    /**
     * @var string
     */
    private $m_releaseDate = '2008-03-29';

    /**
     * @var string
     */
    private $m_copyrightYear = 2008;

    /**
     * @var string
     */
    private $m_license = 'GPL v.2';

    /**
     * @var string
     */
    private $m_website = 'http://www.campware.org/';


    /**
     * Class constructor
     */
    final public function __construct() { } // fn __construct


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
        $text  = $this->m_package.' "'.$this->m_codeName.'" '.$this->release;
        $text .= (empty($this->m_devStatus)) ? '-'.$this->m_devStatus : '';
        $text .= ' '.$this->m_releaseDate.'<br />';
        $text .= $this->m_copyright;
    } // fn getFullInfo

} // fn class CampVersion

?>