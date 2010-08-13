<?php
header('Content-type: application/json');

require_once($GLOBALS['g_campsiteDir']. "/classes/Article.php");

if (!isset($_REQUEST['startIndex']) or intval($_REQUEST['startIndex']) < 1) {
    $startIndex = 0;
} else {
    $startIndex = intval($_REQUEST['startIndex']);
}
if (!isset($_REQUEST['results']) or intval($_REQUEST['results']) < 10) {
    $results = 10;
} else {
    $results = intval($_REQUEST['results']);
    $results = $results > 35 ? 35 : $results;
}


$articlesParams = array();
if (isset($_REQUEST['publication']) && $_REQUEST['publication'] > 0) {
    $publication_id = (int) $_REQUEST['publication'];
    $articlesParams[] = new ComparisonOperation('idpublication', new Operator('is', 'integer'), $publication_id);
}

if (isset($_REQUEST['issue']) && $_REQUEST['issue'] > 0) {
    $issue_nr = (int) $_REQUEST['issue'];
    $articlesParams[] = new ComparisonOperation('nrissue', new Operator('is', 'integer'), $issue_nr);
}

if (isset($_REQUEST['section']) && $_REQUEST['section'] > 0) {
    $section_nr = (int) $_REQUEST['section'];
    $articlesParams[] = new ComparisonOperation('nrsection', new Operator('is', 'integer'), $section_nr);
}


if (isset($_REQUEST['query']) && strlen($_REQUEST['query']) > 0) {
    $search_phrase = $_REQUEST['query'];
    $articlesParams[] = new ComparisonOperation('search_phrase', new Operator('is', 'integer'), $search_phrase);
}
if (isset($_REQUEST['filter_type']) && strlen($_REQUEST['filter_type']) > 0
        && isset($_REQUEST['filter_input']) && strlen($_REQUEST['filter_input']) > 0) {
    if ($_REQUEST['filter_input'] == 'iduser') {
        $articlesParams[] = new ComparisonOperation($_REQUEST['filter_type'], new Operator('is', 'integer'), $_REQUEST['filter_input']);
    } elseif ($_REQUEST['filter_type'] == 'publish_date') {
        $selectedDate = $_REQUEST['filter_input'];
        $articlesParams[] = new ComparisonOperation('publish_date', new Operator('is', 'date'), $selectedDate);
    } elseif ($_REQUEST['filter_type'] == 'publish_range') {
        $intervalDates = explode(',', $_REQUEST['filter_input']);
        $articlesParams[] = new ComparisonOperation('publish_date', new Operator('greater_equal', 'date'), $intervalDates[0]);
        $articlesParams[] = new ComparisonOperation('publish_date', new Operator('smaller_equal', 'date'), $intervalDates[1]);
    } elseif ($_REQUEST['filter_type'] == 'topic') {
        $topic = $_REQUEST['filter_input'];
        $articlesParams[] = new ComparisonOperation('topic', new Operator('is', 'integer'), $topic);
    } else {
        $articlesParams[] = new ComparisonOperation($_REQUEST['filter_type'], new Operator('is', 'string'), $_REQUEST['filter_input']);
    }
}

$articles = Article::GetList($articlesParams, array(array('field'=>'bycreationdate', 'dir'=>'asc')), 0, 100, $articlesCount, true);

$return = array();
foreach($articles as $article) {
    //
    $articleLinkParams = '?f_publication_id=' . $article->getPublicationId()
        . '&f_issue_number=' . $article->getIssueNumber() . '&f_section_number=' . $article->getSectionNumber()
        . '&f_article_number=' . $article->getArticleNumber() . '&f_language_id=' . $article->getLanguageId()
        . '&f_language_selected=' . $article->getLanguageId();
    //
    $articleLink = '/admin/articles/edit.php' . $articleLinkParams;
    //
    $previewLink = '/admin/articles/preview.php' . $articleLinkParams;
    //
    $lockInfo = '';
    $lockHighlight = false;
    $timeDiff = camp_time_diff_str($article->getLockTime());
    if ($article->isLocked() && ($timeDiff['days'] <= 0)) {
        $lockUser = new User($article->getLockedByUser());
        if ($timeDiff['hours'] > 0) {
            $lockInfo = getGS('The article has been locked by $1 ($2) $3 hour(s) and $4 minute(s) ago.',
                htmlspecialchars($lockUser->getRealName()),
                htmlspecialchars($lockUser->getUserName()),
                $timeDiff['hours'], $timeDiff['minutes']);
        } else {
            $lockInfo = getGS('The article has been locked by $1 ($2) $3 minute(s) ago.',
                htmlspecialchars($lockUser->getRealName()),
                htmlspecialchars($lockUser->getUserName()),
                $timeDiff['minutes']);
        }
        if ($article->getLockedByUser() != $g_user->getUserId()) {
            $lockHighlight = true;
        }
    }

    //
    $tmpUser = new User($article->getCreatorId());
    //
    $tmpAuthor = new Author($article->getAuthorId());
    //
    $tmpArticleType = new ArticleType($article->getType());
    //
    $onFrontPage = $article->onFrontPage() ? getGS('Yes') : getGS('No');
    //
    $onSectionPage = $article->onSectionPage() ? getGS('Yes') : getGS('No');
    //
    $imagesNo = ArticleImage::GetImagesByArticleNumber($article->getArticleNumber(), true);
    //
    $topicsNo = ArticleTopic::GetArticleTopics($article->getArticleNumber(), true);
    //
    $commentsNo = '';
    if ($article->commentsEnabled()) {
        $commentsNo = ArticleComment::GetArticleComments($article->getArticleNumber(), $article->getLanguageId(), null, true);
    } else {
        $commentsNo = 'No';
    }
    $return[] = array(
        'art_id' => $article->getArticleNumber(),
        'art_name' => $article->getTitle(),
        'art_type' => $tmpArticleType->getDisplayName(),
        'art_createdby' => $tmpUser->getRealName(),
        'art_author' => $tmpAuthor->getName(),
        'art_status' => $article->getWorkflowDisplayString(),
        'art_ofp' => $onFrontPage,
        'art_osp' => $onSectionPage,
        'art_images' => $imagesNo,
        'art_topics' => $topicsNo,
        'art_comments' => $commentsNo,
        'art_reads' => $article->getReads(),
        'art_lastmodifieddate' => $article->getLastModified(),
        'art_publishdate' => $article->getPublishDate(),
        'art_creationdate' => $article->getCreationDate(),
        'art_islocked' => $article->isLocked(),
        'art_lockinfo' => $lockInfo,
        'art_lockhighlight' => $lockHighlight,
        'art_link' => $articleLink,
        'art_previewlink' => $previewLink,
        'art_languageid' => $article->getLanguageId(),
    );
}

$return = array_slice($return, $startIndex, $results);

require_once('JSON.php');    
$json = new Services_JSON();
echo($json->encode(array(
	"recordsReturned" => count($return),
    "totalRecords" => $articlesCount,
    "startIndex" => $startIndex,
    "records" => $return,
)));
?>