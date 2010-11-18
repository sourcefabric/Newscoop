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
        $xml = simplexml_load_file($this->url);
        $even = false;
        foreach ($xml->item as $item) {
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
}
