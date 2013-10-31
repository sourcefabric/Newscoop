<?php
$cs_dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$GLOBALS['g_campsiteDir'] = $cs_dir;

require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'campsite_constants.php');
require_once(CS_PATH_SITE.DIR_SEP.'include'.DIR_SEP.'campsite_init.php');
require_once(CS_PATH_SITE.DIR_SEP.'db_connect.php');
require_once(CS_PATH_CLASSES.DIR_SEP.'Topic.php');

function transfer_topics_3_5($p_parentId = 0)
{
	global $g_ado_db;

	$sql = 'SELECT * FROM TopicsOld';
	if (!is_null($p_parentId)) {
		$sql .= " WHERE ParentId = $p_parentId";
	}
	$sql .= ' ORDER BY TopicOrder DESC, LanguageId ASC';
	$rows = $g_ado_db->GetAll($sql);
	foreach ($rows as $row) {
		$topic = new Topic($row['Id']);
		if ($topic->exists()) {
			$topic->setName($row['LanguageId'], $row['Name']);
		} else {
			$topic->create(array('parent_id'=>$p_parentId, 'names'=>array($row['LanguageId']=>$row['Name'])));
			transfer_topics_3_5($topic->getTopicId());
		}
	}
} // fn transfer_topics_3_5


transfer_topics_3_5();

$GLOBALS['g_ado_db']->Execute('DROP TABLE TopicsOld');

?>