<?php
$cs_dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$GLOBALS['g_campsiteDir'] = $cs_dir;
require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'campsite_constants.php');
require_once(CS_PATH_SITE.DIR_SEP.'include'.DIR_SEP.'campsite_init.php');
require_once(CS_PATH_SITE.DIR_SEP.'db_connect.php');
require_once(CS_PATH_CLASSES.DIR_SEP.'Topic.php');

function transfer_phorum_3_6($p_parentId = 0)
{
    global $g_ado_db;

    $sql = 'SELECT * FROM phorum_banlists';
    $rows = $g_ado_db->GetAll($sql);
    foreach ($rows as $row) {
        //p.`forum_id`, p.`type`, p.`pcre`, p.`string` F
        $row['forum_id'];
    }
} // fn transfer_topics_3_5


transfer_phorum_3_6();

$GLOBALS['g_ado_db']->Execute('DROP TABLE TopicsOld');

?>