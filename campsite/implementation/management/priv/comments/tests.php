<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("comments");
require_once($_SERVER['DOCUMENT_ROOT']."/include/phorum_load.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_forum.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_message.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_user.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/include/pear/PHPUnit.php');

class PhorumUser_Test extends PHPUnit_TestCase
{
	function PhorumUser_Test($name)
	{
		$this->PHPUnit_TestCase($name);
	}

	function testUserCreateDelete()
	{
		$user = Phorum_user::GetByUserName('bob');
		if ($user) {
			if (!$user->delete()) {
				$this->fail("Could not delete pre-existing user");
				return;
			}
		}
		$user =& new Phorum_user();
		$user->create('bob', 'test@yahoo.com');
		if (!$user->exists()) {
			$this->fail("Could not create user.");
		}

		if (!$user->delete()) {
			$this->fail("Could not delete user");
		}
	}


} // class PhorumUser_Test


class PhorumMessage_Test extends PHPUnit_TestCase
{
    var $m_forum;

	// constructor of the test suite
    function PhorumMessage_Test($name)
    {
       $this->PHPUnit_TestCase($name);
    }


    function setup()
    {
		$this->m_forum =& new Phorum_forum(1);
		if (!$this->m_forum->exists()) {
			$this->m_forum->create();
		}
		$this->m_forum->setIsModerated(false);
		$this->m_forumNumMessages = $this->m_forum->getNumMessages();
    }


    function testCreateMessage()
    {
		$message =& new Phorum_message();
		$message->create(1, 'hello', 'world');
		$messageId = $message->getMessageId();
		if (!$message->exists()) {
			$this->fail("Error creating message.");
		}

		// Check if the number of messages was incremented
		$this->m_forum->fetch();
		if ($this->m_forum->getNumMessages() != ($this->m_forumNumMessages+1)) {
			$this->fail("Error updating forum message count. (Was:".$this->m_forumNumMessages.", Now: ".$this->m_forum->getNumMessages().")");
		}

		$message2 =& new Phorum_message($messageId);
		if ($message2->getSubject() != $message->getSubject()) {
			$this->fail("Error fetching message");
		}
    } // fn testCreateMessage


    function testDeleteStandaloneMessage()
    {
    	$message =& new Phorum_message();
    	$message->create(1, 'delete me');
    	$messageId = $message->getMessageId();
    	$message->delete();
    	$message2 =& new Phorum_message($messageId);
    	if ($message2->exists()) {
    		$this->fail("Could not delete message");
    	}
    } // fn testDeleteStandaloneMessage


    function testCreateThreadOfMessages()
    {
		$message1 =& new Phorum_message();
		$message1->create(1, 'Message 1', 'la la');

		$message2 =& new Phorum_message();
		$message2->create(1, 'Message 2', 'wow', $message1->getThreadId(), $message1->getMessageId());

		$message3 =& new Phorum_message();
		$message3->create(1, 'Message 3', 'cool', $message1->getThreadId(), $message1->getMessageId());

		$messages = Phorum_message::GetMessages(array("thread" => $message1->getThreadId()));

		if (count($messages) != 3) {
			$this->fail("Creating a thread of messages failed.");
		}

		$message1->delete(PHORUM_DELETE_TREE);
		$message2->fetch();
		$message3->fetch();
		if ($message2->exists() || $message3->exists()) {
		    $this->fail("Thread not deleted correctly");
		}
    } // fn testCreateThreadOfMessages


    function testUpdateThreadInfo()
    {
        // Create thread start.
    	$message =& new Phorum_message();
    	$message->create(1, 'delete me');
    	$messageId = $message->getMessageId();

    	// add message to the thread.
    	$message2 =& new Phorum_message();
    	$message2->create(1, "delete me", "wow", $messageId, $messageId);

    	$message->fetch();
    	$threadCount = $message->getNumMessagesInThread();

    	$message2->delete();

    	$message->fetch();
    	$threadCount2 = $message->getNumMessagesInThread();

    	if ($threadCount != ($threadCount2 + 1)) {
    		$this->fail("Thread stats not updated correctly.");
    	}
    }
} // class PhorumMessage_test

$suite  = new PHPUnit_TestSuite("PhorumUser_Test");
$suite->addTestSuite("PhorumMessage_Test");
$result = PHPUnit::run($suite);

echo $result->toHtml();

//$message =& new Phorum_message(1);
//camp_dump($message);




?>