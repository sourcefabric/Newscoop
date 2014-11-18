<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Search;

use Newscoop\Search\DocumentInterface;

/**
 * Service Interface
 *
 * Provides an interface for Search services
 */
interface ServiceInterface
{
    const DATE_FORMAT = 'Y-m-d\TH:i:s\Z';

    /**
     * Get the type of the item
     *
     * @return string identifier for type
     */
    public function getType();

    /**
     * Test if item is indexed
     *
     * @param DocumentInterface $item
     * @return bool
     */
    public function isIndexed(DocumentInterface $item);

    /**
     * Test if item can be indexed
     *
     * @param DocumentInterface $item
     * @return bool
     */
    public function isIndexable(DocumentInterface $item);

    /**
     * Get document for item
     *
     * @param DocumentInterface $item
     * @return array
     */
    public function getDocument(DocumentInterface $item);

    /**
     * Get document id
     *
     * @param DocumentInterface $item
     * @return string
     */
    public function getDocumentId(DocumentInterface $item);
}
