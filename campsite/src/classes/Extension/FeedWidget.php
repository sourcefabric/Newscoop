<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

/**
 * Base feed widget
 */
abstract class FeedWidget extends Widget
{
    /**
     * @var string
     * @setting
     */
    protected $url = 'http://www.sourcefabric.org/en/?tpl=259';

    /**
     * @var int
     * @setting
     */
    protected $count = 8;

    /** @var int */
    protected $ttl = 900; // 15m

    public function render()
    {
        ob_start();
        $even = false;
        $count = $this->getSetting('count');
        foreach ($this->getItems($this->getSetting('url')) as $item) {
            if ($count <= 0) {
                break;
            } else {
                $count--;
            }

            echo $even ? '<li class="even">' : '<li>';
            $even = !$even;

            // add link
            printf('<a href="%s" title="%s" target="_blank">%s</a>',
                $item->link,
                $item->title,
                $item->title);

            if ($this->isFullscreen()) {
                echo '<p>', $item->description, '</p>';
            }

            echo '</li>', "\n";
        }
        $content = ob_get_clean();

        if (empty($content)) {
            echo '<p>', getGS("No news from '$1'.", $this->getSetting('url')), '</p>';
        } else {
            echo '<ul class="rss">', "\n";
            echo $content;
            echo '</ul>', "\n";
        }
    }

    /**
     * Get feed items from specified url or cache
     * @param string $url
     * @return SimpleXMLElement
     */
    private function getItems($url)
    {
        $cache = $this->getCache();
        $feed = $cache->fetch($url);
        if (empty($feed)) {
            $feed = file_get_contents($url);
            $cache->add($url, $feed, $this->ttl);
        }
        return simplexml_load_string($feed);
    }
}
