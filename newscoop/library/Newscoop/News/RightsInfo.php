<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 * RightsInfo
 * @EmbeddedDocument
 */
class RightsInfo
{
    /**
     * @Id
     * @var string
     */
    protected $id;

    /**
     * @String
     * @var string
     */
    protected $copyrightHolder;

    /**
     * @String
     * @var string
     */
    protected $copyrightNotice;

    /**
     * @param SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->copyrightHolder = (string) $xml->copyrightHolder['literal'];
        $this->copyrightNotice = (string) $xml->copyrightNotice;
    }

    /**
     * Get copyright holder
     *
     * @return string
     */
    public function getCopyrightHolder()
    {
        return $this->copyrightHolder;
    }

    /**
     * Get copyright notice
     *
     * @return string
     */
    public function getCopyrightNotice()
    {
        return $this->copyrightNotice;
    }
}
