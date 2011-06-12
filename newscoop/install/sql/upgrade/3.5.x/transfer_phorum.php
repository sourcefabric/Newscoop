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
    $g_ado_db->StartTrans();
    $comments = array();
    $status_mapper = array( 2=>0, 1=>1, 0=>2);
    $sql = 'SELECT * FROM `phorum_messages` `p` JOIN `ArticleComments` `ac` ON `p`.`message_id` = `ac`.`fk_comment_id` ORDER BY `p`.`message_id` ASC';
    $rows = $g_ado_db->GetAll($sql);
    foreach ($rows as $row) {
        $get_commenter = "SELECT `cc`.`id` FROM `comment_commenter` `cc` WHERE `cc`.`email` = '".$row['email']."' AND `cc`.`name` = '".$row['author']."' LIMIT 1";
        $ids = $g_ado_db->GetRow($get_commenter);
        $ip = gethostbyname($row['ip']);
        if(isset($ids['id']))
            $commenter = $ids['id'];
        else {
            $insert_commenter = 'INSERT INTO `comment_commenter` SET '
                                .'  `email`           = "'.$row['email'].'" '
                                .', `name`            = "'.$row['author'].'" '
                                .', `ip`              = "'.$ip.'" '
                                .', `time_created`    = "'.date('Y/m/d H:i:s', $row['datestamp']).'" '
                                .', `time_updated`    = "'.date('Y/m/d H:i:s', $row['modifystamp']).'" ';
         if($row['user_id'] != '0') {
             $insert_commenter.=  ', `fk_user_id`     = "'.$row['user_id'].'" ';
         }
         $g_ado_db->Execute($insert_commenter);
         $commenter = $g_ado_db->Insert_ID();
        }
        $comment_sql = 'INSERT INTO `comment` SET '
                       .'  `fk_comment_commenter_id`   = "'.$commenter.'" '
                       .', `fk_forum_id`               = "'.$row['forum_id'].'" '
                       .', `fk_thread_id`              = "'.$row['fk_article_number'].'" '
                       .', `subject`                   = "'.$row['subject'].'" '
                       .', `message`                   = "'.$row['body'].'" '
                       .', `fk_language_id`            = "'.$row['fk_language_id'].'" '
                       .', `time_created`              = "'.date('Y/m/d H:i:s', $row['datestamp']).'" '
                       .', `time_updated`              = "'.date('Y/m/d H:i:s', $row['modifystamp']).'" '
                       .', `status`                    = "'.$status_mapper[$row['status']].'" '
                       .', `ip`                        = "'.$ip.'" '
                       .', `thread_level`              = "'.$row['thread_depth'].'" '
                       .', `thread_order`              = "'.$row['thread_order'].'" ';
		 if(isset($comments[$row['parent_id']])) {
		     $comment_sql.= ', `fk_parent_id`          = "'.$comments[$row['parent_id']].'" ';
		 }
		 $g_ado_db->Execute($comment_sql);
		 $comment = $g_ado_db->Insert_ID();
		 $comments[$row['message_id']] = $comment;
    }
    $sql = 'SELECT * FROM `phorum_banlists`';
    $rows = $g_ado_db->GetAll($sql);
    foreach ($rows as $row) {
        $search = $row['string'];
        if( $row['type']== '1')
            $search = gethostbyname($search);
        $acceptance_sql = 'INSERT INTO `comment_acceptance` SET '
                           .'  `for_column`             = "'.$row['type'].'" '
                           .', `type`                   = "1" '
                           .', `search_type`            = "'.$row['pcre'].'" '
                           .', `search`                 = "'.$search.'" ';
        $g_ado_db->Execute($acceptance_sql);
    }
    $g_ado_db->CompleteTrans();
} // fn transfer_topics_3_5


transfer_phorum_3_6();

?>