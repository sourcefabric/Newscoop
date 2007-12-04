<?php

header("Content-type: text/html; charset=UTF-8");

global $_SERVER;
global $Campsite;
global $DEBUG;

// initialize needed global variables
$_SERVER['DOCUMENT_ROOT'] = getenv("DOCUMENT_ROOT");

require_once($_SERVER['DOCUMENT_ROOT'].'/include/campsite_constants.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/conf/configuration.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/conf/liveuser_configuration.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');

$Interview = new Interview($_REQUEST['interview_id']);
$Interview->store();
echo $Interview->getForm('index.php', null, null, null, true);

#$Interview->create(2, 3, 'titel 1', true, 'kurzbeschreibung', 'beschreibung', '2007-01-02 11:11:11', '2008-03-04 14:00:00', '2005-01-01 00:00:00', '2006-01-01 00:00:00', 255);

#$Interview->delete();

/*
foreach(Interview::GetInterviews(null, null, null, array('title' => 'asc')) as $Interview) {
    echo $Interview->getTitle();
    echo '<hr>';   
}


$Item = new InterviewItem(1, $_REQUEST['item_id']);
#$Item->storeQuestion();
$Item->storeAnswer();
echo $Item->getAnswerForm('index.php',null, null, null, true);

#$Item->create(22, 'What did you do?');

#$Item->positionAbsolute(3);


foreach(InterviewItem::GetInterviewItems(1) as $InterviewItem) {
    echo $InterviewItem->getQuestion().': '.$InterviewItem->getAnswer();
    echo '<hr>';   
}
*/


?>
