<?php

require_once dirname(__FILE__) . '/bootstrap.php';

class YourArticlesWidget extends Widget
{
    public function getTitle()
    {
        return 'Your Articles';
    }

    public function render()
    {
        require_once $GLOBALS['g_campsiteDir'] . '/admin-files/smartlist/Smartlist.php';
        $smartlist = new Smartlist();
        $smartlist->setItems(Article::GetArticlesByUser($this->getUser()->getUserId()));
        $smartlist->render();
    }
}
