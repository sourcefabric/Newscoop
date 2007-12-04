<?php
require_once 'conf.php';
require_once 'createlu.php';
require_once 'HTML/Template/IT.php';

// Setup template objects

if (!$usr->isLoggedIn()) {
    $tpl =& new HTML_Template_IT('./');
    $tpl->loadTemplatefile('login_form.tpl', true, false);
    $login = $tpl->get();
} else {
    $login  = '<p>User: ' . $usr->getProperty('handle') . ' !</p>';
    $login .= '<p><a href="admin.php">Go to the admin part</a></p>';
    $login .= '<p><a href="?logout=1">Logout</a></p>';
}

$tpl = &new HTML_Template_IT('./');
$tpl->loadTemplatefile('home.tpl', true, true);

// assign the content to the vars
$tpl->setVariable('GENERALNEWS', getNews($db, 'general'));
$tpl->setVariable('LOGIN',       $login);

$tpl->show();

// This function is to fetch news from the MDB2
function getNews(&$db, $newsCategory)
{
    $query = "
        SELECT
            news_id      AS id,
            DATE_FORMAT(news_date, '%D %b %Y at %H:%I:%S') AS date,
            news_title   AS title,
            news_content AS content
        FROM
            news
        WHERE
            news_category = '$newsCategory'
        AND
            news_id<>0
        ORDER BY
            date ASC";

    $news = $db->queryAll($query, null, MDB2_FETCHMODE_ASSOC, true);

    if (PEAR::isError($news)) {
        die($news->getMessage() . ' ' . $news->getUserinfo());
    } else {
        $tpl =& new HTML_Template_IT('./');

        $tpl->loadTemplatefile('news.tpl', true, true);

        foreach ($news as $name) {
            $tpl->setCurrentBlock('row');
            $tpl->setVariable('DATE',    $name['date']);
            $tpl->setVariable('TITLE',   $name['title']);
            $tpl->setVariable('CONTENT', $name['content']);
            $tpl->parseCurrentBlock('row');
        }
        return $tpl->get();
    }
}
?>
