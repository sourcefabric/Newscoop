<?php
class HelloWorld extends Widget
{
    public function getTitle()
    {
        return 'Hello world';
    }

    public function render()
    {
        echo '<p>Hello world!</p>';
    }
}
