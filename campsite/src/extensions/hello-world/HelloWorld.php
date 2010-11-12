<?php
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
