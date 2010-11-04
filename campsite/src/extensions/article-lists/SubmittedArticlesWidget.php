<?php

require_once dirname(__FILE__) . '/bootstrap.php';

class SubmittedArticlesWidget extends Widget
{
    public function getTitle()
    {
        return 'Submitted Articles';
    }

    public function render()
    {
        if ($this->getUser()->hasPermission('ChangeArticle') || $this->getUser()->hasPermission('Publish')) {
            $list = new Smartlist();
            $list->setItems(Article::GetSubmittedArticles());
            $list->render();
        } else {
            echo '<p>Access Denied</p>';
        }
    }
}
