<?php
/**
 * @package Newscoop
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop;

/**
 * Editor interface.
 */
interface EditorInterface
{
	/**
     * Gets the current editor link.
     * If there is other editor enabled it will be used,
     * else it will choose default one.
     *
     * @param Article|\Article $article Article object
     *
     * @return string Editor's link
     */
    public function getLink($article);

    /**
     * Gets the default editor link's parameters
     *
     * @param Article|\Article $article Article object
     *
     * @return string
     */
    public function getLinkParameters($article);
}
