<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric z.ú.
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop;

/**
 * Themes service interface
 */
interface ThemesServiceInterface
{
    /**
     * Gets current theme path
     *
     * @return string Returns current theme path
     */
    public function getThemePath();
}
