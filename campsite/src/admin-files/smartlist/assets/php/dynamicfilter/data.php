<?php

header('Content-type: application/json');

require_once($GLOBALS['g_campsiteDir']. "/classes/Article.php");

// start >= 0
$start = max(0,
    empty($_REQUEST['iDisplayStart']) ? 0 : (int) $_REQUEST['iDisplayStart']);

// results num >= 10 && <= 100
$limit = min(100, max(10,
    empty($_REQUEST['iDisplayLength']) ? 0 : (int) $_REQUEST['iDisplayLength']));

// filters - common
$articlesParams = array();
$filters = array(
    'publication' => array('is', 'integer'),
    'issue' => array('is', 'integer'),
    'section' => array('is', 'integer'),
    'publish_date' => array('is', 'date'),
    'publish_date_from' => array('greater_equal', 'date'),
    'publish_date_to' => array('smaller_equal', 'date'),
    'author' => array('is', 'integer'),
);

// mapping form name => db name
$fields = array(
    'publish_date_from' => 'publish_date',
    'publish_date_to' => 'publish_date',
);

foreach ($filters as $name => $opts) {
    if (!empty($_REQUEST[$name])) {
        $field = !empty($fields[$name]) ? $fields[$name] : $name;
        $articlesParams[] = new ComparisonOperation($field, new Operator($opts[0], $opts[1]), $_REQUEST[$name]);
    }
}

// search
if (isset($_REQUEST['sSearch']) && strlen($_REQUEST['sSearch']) > 0) {
    $search_phrase = $_REQUEST['sSearch'];
    $articlesParams[] = new ComparisonOperation('search_phrase', new Operator('is', 'integer'), $search_phrase);
}

// sorting
$sortOptions = array(
    0 => 'bynumber',
    2 => 'byname',
    11 => 'bycomments',
    12 => 'bypopularity',
    14 => 'bypublishdate',
    15 => 'bycreationdate',
);

$sortBy = 'bypublishdate';
$sortDir = 'desc';
$sortingCols = min(1, (int) $_REQUEST['iSortingCols']);
for ($i = 0; $i < $sortingCols; $i++) {
    $sortOptionsKey = (int) $_REQUEST['iSortCol_' . $i];
    if (!empty($sortOptions[$sortOptionsKey])) {
        $sortBy = $sortOptions[$sortOptionsKey];
        $sortDir = $_REQUEST['sSortDir_' . $i];
        break;
    }
}

// get articles
$articles = Article::GetList($articlesParams, array(array('field' => $sortBy, 'dir' => $sortDir)), $start, $limit, $articlesCount, true);

$return = array();
foreach($articles as $article) {
    $articleLinkParams = '?f_publication_id=' . $article->getPublicationId()
        . '&f_issue_number=' . $article->getIssueNumber() . '&f_section_number=' . $article->getSectionNumber()
        . '&f_article_number=' . $article->getArticleNumber() . '&f_language_id=' . $article->getLanguageId()
        . '&f_language_selected=' . $article->getLanguageId();
    $articleLink = $Campsite['WEBSITE_URL'].'/admin/articles/edit.php' . $articleLinkParams;
    $previewLink = $Campsite['WEBSITE_URL'].'/admin/articles/preview.php' . $articleLinkParams;

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

    $tmpUser = new User($article->getCreatorId());
    $tmpAuthor = new Author($article->getAuthorId());
    $tmpArticleType = new ArticleType($article->getType());

    $onFrontPage = $article->onFrontPage() ? getGS('Yes') : getGS('No');
    $onSectionPage = $article->onSectionPage() ? getGS('Yes') : getGS('No');

    $imagesNo = ArticleImage::GetImagesByArticleNumber($article->getArticleNumber(), true);
    $topicsNo = ArticleTopic::GetArticleTopics($article->getArticleNumber(), true);
    $commentsNo = '';
    if ($article->commentsEnabled()) {
        $commentsNo = ArticleComment::GetArticleComments($article->getArticleNumber(), $article->getLanguageId(), null, true);
    } else {
        $commentsNo = 'No';
    }

    $return[] = array(
        $article->getArticleNumber(),
        $article->getLanguageId(),
        sprintf('<a href="%s%s" title="%s %s">%s</a>',
            $articleLink, $articleLinkParams,
            getGS('Edit'), $article->getName(),
            $article->getName()),
        $tmpArticleType->getDisplayName(),
        $tmpUser->getRealName(),
        $tmpAuthor->getName(),
        $article->getWorkflowStatus(),
        $onFrontPage,
        $onSectionPage,
        $imagesNo,
        $topicsNo,
        $commentsNo,
        $article->getReads(),
        $article->getCreationDate(),
        $article->getPublishDate(),
        $article->getLastModified(),
    );
}

echo(json_encode(array(
    'iTotalRecords' => Article::GetTotalCount(),
    'iTotalDisplayRecords' => $articlesCount,
    'sEcho' => $_GET['sEcho'],
    'aaData' => $return,
)));
