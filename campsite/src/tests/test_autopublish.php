<?php
require_once($GLOBALS['g_campsiteDir']."/classes/Article.php");
require_once($GLOBALS['g_campsiteDir']."/classes/ArticlePublish.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Issue.php");
require_once($GLOBALS['g_campsiteDir']."/classes/IssuePublish.php");

// AUTOPUBLISH TESTS

echo "<h2>ARTICLE AUTOPUBLISH TESTS</h2>";

// create an article
echo "Creating article...<br>";
$newArticle = new Article(1, 1, 1, 1);
$newArticle->create('fastnews', 'autopublish test '.rand());
if (!$newArticle->exists()) {
    echo "failed to create article.<br>";
    die();
}

$newArticle->dumpToHtml();

// create an article event in the past
echo "Creating event to publish the article...<br>";
$datetime = strftime("%Y-%m-%d %H:%M:00");
$articlePublishObj = new ArticlePublish($newArticle->getArticleId(), 1, $datetime);
$articlePublishObj->create();
$articlePublishObj->setPublishAction('P');
$articlePublishObj->dumpToHtml();

echo "Getting events...<br>";
$events = ArticlePublish::GetPendingActions();
foreach ($events as $event) {
    $event->dumpToHtml();
    $event->doAction();
}

echo "Has the article been updated?<br>";
$newArticle->fetch();
$newArticle->dumpToHtml();

// Schedule future event
echo "Scheduling future event...<br>";
$datetime = strftime("2100-%m-%d %H:%M:00");
$articlePublishObj2 = new ArticlePublish($newArticle->getArticleId(), 1, $datetime);
$articlePublishObj2->create();
$articlePublishObj2->setPublishAction('P');

// Verify
echo "Number of pending actions (should be zero): ".count(ArticlePublish::GetPendingActions())."<br>";

// Schedule past event
echo "Scheduling past event...<br>";
$datetime = strftime("1900-%m-%d %H:%M:00");
$articlePublishObj3 = new ArticlePublish($newArticle->getArticleId(), 1, $datetime);
$articlePublishObj3->create();
$articlePublishObj3->setPublishAction('P');

// Verify
echo "Number of pending actions (should be one): ".count(ArticlePublish::GetPendingActions())."<br>";

// delete the article
echo "Deleting the article.<br>";
$newArticle->delete();

echo "Deleting the events.<br>";
$articlePublishObj->delete();
$articlePublishObj2->delete();
$articlePublishObj3->delete();

echo "<h2>ISSUE AUTOPUBLISH TESTS</h2>";
echo "Creating an issue...<br>";
$issueId = rand();
$issue = new Issue(1, 1, $issueId);
$issue->create($issueId);
$issue->fetch();
$issue->dumpToHtml();
$article1 = new Article(1, 1, $issueId, 1);
$article1->create('fastnews', 'issue schueduled publish test '.rand());
$article2 = new Article(1, 1, $issueId, 1);
$article2->create('fastnews', 'issue schueduled publish test '.rand());

// Create issue publish event
echo "Creating issue publish event...<br>";
$datetime = strftime("%Y-%m-%d %H:%M:00");
$issuePublishEvent = new IssuePublish(1, $issueId, 1, $datetime);
$issuePublishEvent->create();
$issuePublishEvent->setPublishAction('P');
$issuePublishEvent->setPublishArticlesAction('Y');
$issuePublishEvent->fetch();
$issuePublishEvent->dumpToHtml();

echo "Executing pending events:<br>";
$events = IssuePublish::GetPendingActions();
foreach ($events as $event) {
    $event->doAction();
    $event->dumpToHtml();
}

// Check if issues are published
echo "Is the issue published?<br>";
$issue->fetch();
$issue->dumpToHtml();

// Are the articles published?
echo "Are the articles published?<br>";
$article1->fetch();
$article1->dumpToHtml();
$article2->fetch();
$article2->dumpToHtml();

echo "Number of remaining events (should be zero): ".count(IssuePublish::GetPendingActions())."<br>";

echo "Deleting objects.<br>";
$issue->delete();
$article1->delete();
$article2->delete();
$issuePublishEvent->delete();

echo "done.<br>";
?>