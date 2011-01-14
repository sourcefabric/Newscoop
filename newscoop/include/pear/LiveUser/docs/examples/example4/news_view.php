<?php
// CREATING ENVIRONMENT
require_once 'conf.php';

$tpl->loadTemplatefile('news_view.tpl.php');

$res = $db->query('SELECT
                       DATE_FORMAT(news.created_at,"%d.%m.%Y - %H:%i") AS date,
                       news.news,
                       liveuser_peoples.handle
                   FROM
                       news
                   INNER JOIN
                       liveuser_perm_peoples
                   ON
                       news.owner_user_id = liveuser_perm_peoples.perm_user_id
                   LEFT JOIN
                       liveuser_peoples
                   ON
                       liveuser_perm_peoples.auth_user_id = liveuser_peoples.authUserId
                   ORDER BY
                     news.created_at DESC');

while ($row = $res->fetchRow()) {
    $tpl->setCurrentBlock('row');

    $tpl->setVariable(array('time'     => $row['date'],
                            'news'     => stripslashes($row['news']),
                            'email'    => $row['handle'] . '@your-company.com',
                            'author'   => $row['handle']));

    $tpl->parseCurrentBlock();
}

    include_once 'finish.inc.php';
?>
