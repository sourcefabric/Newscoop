<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 * Settings
 * @Document
 */
class Settings
{
    /**
     * @Id(strategy="NONE")
     * @var string
     */
    protected $id = 'ingest';

    /**
     * @String
     * @var string
     */
    protected $articleTypeName = 'newsml';

    /**
     * @Int
     * @var int
     */
    protected $publicationId;

    /**
     * @Int
     * @var int
     */
    protected $sectionNumber;

    /**
     * Set article type name
     *
     * @param string $articleTypeName
     * @return void
     */
    public function setArticleTypeName($articleTypeName)
    {
        $this->articleTypeName = (string) $articleTypeName;
    }

    /**
     * Get article type name
     *
     * @return string
     */
    public function getArticleTypeName()
    {
        return $this->articleTypeName;
    }

    /**
     * Set publication id
     *
     * @param int $publicationId
     * @return void
     */
    public function setPublicationId($publicationId)
    {
        $this->publicationId = (int) $publicationId;
    }

    /**
     * Get publication id
     *
     * @return int
     */
    public function getPublicationId()
    {
        return $this->publicationId ? (int) $this->publicationId : null;
    }

    /**
     * Set section numer
     *
     * @param int $sectionNumber
     * @return void
     */
    public function setSectionNumber($sectionNumber)
    {
        $this->sectionNumber = (int) $sectionNumber;
    }

    /**
     * Get section number
     *
     * @return int
     */
    public function getSectionNumber()
    {
        return $this->sectionNumber ? (int) $this->sectionNumber : null;
    }
}
