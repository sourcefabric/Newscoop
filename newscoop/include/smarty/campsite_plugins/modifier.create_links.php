<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

$this->smarty->loadPlugin('smarty_shared_modifier_escape');

/**
 * Create links from urls
 *
 * @param string $input
 * @return string
 */
function smarty_modifier_create_links($input)
{
    return preg_replace_callback(array(
        '@(^|\s)https?://[a-z0-9][a-z0-9-]+(?:[.][a-z0-9-]+)*[.][a-z]{2,4}(?:/\S*)?@i',
        '@(^|\s)(www)(?:[.][a-z0-9-]+)+[.][a-z]{2,4}(?:/\S*)?@i',
    ), function($url) {
        return sprintf('%s<a rel="nofollow" href="%s">%s</a>',
            $url[1],
            smarty_modifier_escape((!empty($url[2]) ? 'http://' : '') .trim($url[0]), 'html'), // add http:// to www. links
            smarty_modifier_escape(preg_replace('@^https?://@', '', trim($url[0])), 'html')
        );
    }, $input);
}
