<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Storage;

/**
 * Storage interface
 */
interface Storage
{
    /**
     * Store item
     *
     * @param string $key
     * @param mixed $data
     * @return bool
     */
    public function storeItem($key, $data);

    /**
     * Fetch item
     *
     * @param string $key
     * @return mixed
     */
    public function fetchItem($key);

    /**
     * Delete item
     *
     * @param string $key
     * @return void
     */
    public function deleteItem($key);

    /**
     * Copy item
     *
     * @param string $from
     * @param string $to
     * @return bool
     */
    public function copyItem($from, $to);

    /**
     * Move item
     *
     * @param string $from
     * @param string $to
     * @return bool
     */
    public function moveItem($from, $to);

    /**
     * List items
     *
     * @param string $path
     * @return array
     */
    public function listItems($path);
}
