<?php
/**
 * @title Hello World
 * @author Petr Jašek
 * @description Widget sample.
 * @homepage http://www.sourcefabric.org
 * @version 1.0
 */
class HelloWorld extends Widget
{
    public function render()
    {
        echo '<p>', $this->_('Hello world!'), '</p>';

        if ($this->isFullscreen()) {
            echo '<p>Hi!</p>';
        }
    }
}
