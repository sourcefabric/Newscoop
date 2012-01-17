<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

/**
 * Default Article Rendition
 */
class DefaultArticleImageRendition extends ArticleImageRendition
{
    /**
     * Test if is default picture
     *
     * @return bool
     */
    public function isDefault()
    {
        return true;
    }
}
