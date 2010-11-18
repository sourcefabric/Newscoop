<?php
/**
 * Source fabric rss feed reader
 */
class SourcefabricRss extends Widget
{
    /** @var string */
    protected $url = 'http://www.sourcefabric.org/en/?tpl=259';

    /** @var int */
    protected $count = 8;

    public function render()
    {
        ob_start();
        $even = false;
        foreach ($this->getItems($this->url) as $item) {
            if ($this->count <= 0) {
                break;
            } else {
                $this->count--;
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
            echo "<p>No news from $this->url</p>";
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
            $cache->add($url, $feed, 300);
        }
        return simplexml_load_string($feed);
    }
}
