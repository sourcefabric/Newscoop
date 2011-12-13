<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 * GroupRef
 * @EmbeddedDocument
 */
class GroupRef
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
    protected $idRef;

    /**
     * @param string $idRef
     */
    public function __construct($idRef)
    {
        $this->idRef = (string) $idRef;
    }

    /**
     * Get idref
     *
     * @return string
     */
    public function getIdRef()
    {
        return $this->idRef;
    }
}
