<?php
/**
 * @package Newscoop
 *
 * @copyright 2010, 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Base feed widget
 */
abstract class FeedWidget extends Widget
{
    /**
     * @var string
     * @setting
     * @label Title
     */
    protected $title = 'Feed reader';

    /**
     * @var string
     * @setting
     */
    protected $url = '';

    /**
     * @var int
     * @setting
     */
    protected $count = 5;

    /** @var int */
    protected $ttl = 900; // 15m

    /**
     * Render feeds
     * @return void
     */
    public function render()
    {
        $even = false;
        $count = $this->getCount();

        try {
            $url = $this->getUrl();
            $items = $this->getItems($url);
        } catch (Exception $e) {
            echo '<p>', getGS("Can't fetch news from '$1'", $url), '</p>';
            return;
        }

        ob_start();
        foreach ($this->getItems($this->getUrl()) as $item) {
            if ($count <= 0) {
                break;
            } else {
                $count--;
            }

            echo $even ? '<li class="even">' : '<li>';
            $even = !$even;

            // add link
            printf('<a href="%s" title="%s" target="_blank">%s</a>',
                $item->getLink(),
                $item->getTitle(),
                $item->getTitle());

            if ($this->isFullscreen()) {
                echo '<p>', $item->getDescription(), '</p>';
            }

            echo '</li>', "\n";
        }
        $content = ob_get_clean();

        if (empty($content)) {
            echo '<p>', getGS('No news.'), '</p>';
            return;
        }

        echo '<ul class="rss">', "\n";
        echo $content;
        echo '</ul>', "\n";
    }

    /**
     * Get feed items from specified url
     *
     * @param string $p_url
     * @return Iterator
     */
    private function getItems($p_url)
    {
        // get cache
        $cache = Zend_Cache::factory('Core', 'File', array(
            'lifetime' => 300,
            'automatic_serialization' => true,
        ), array(
            'cache_dir' => APPLICATION_PATH . '/../cache',
        ));

        // set reader
        Zend_Feed_Reader::setCache($cache);
        Zend_Feed_Reader::useHttpConditionalGet();

        // get feed
        $feed = Zend_Feed_Reader::import($p_url);
        return $feed;
    }
}
