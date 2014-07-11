<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/articles/article_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Log.php");

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

// Check permissions
if (!$g_user->hasPermission('AddArticle')) {
    camp_html_display_error($translator->trans("You do not have the right to add articles."));
    exit;
}

// Get input
$f_publication_id = Input::Get('f_publication_id', 'int', 0, true);
$f_issue_number = Input::Get('f_issue_number', 'int', 0, true);
$f_section_number = Input::Get('f_section_number', 'int', 0, true);
$f_language_id = Input::Get('f_language_id', 'int', 0, true);

// For choosing the article location.
$f_destination_publication_id = Input::Get('f_destination_publication_id', 'int', 0, true);
$f_destination_issue_number = Input::Get('f_destination_issue_number', 'int', 0, true);
$f_destination_section_number = Input::Get('f_destination_section_number', 'int', 0, true);

$f_article_name = trim(Input::Get('f_article_name', 'string', ''));
$f_article_type = trim(Input::Get('f_article_type', 'string', ''));
$f_article_language = trim(Input::Get('f_article_language', 'int', 0));

$f_language_id = ($f_language_id > 0) ? $f_language_id : $f_article_language;

$linkArgs = '?f_article_name=' . urlencode($f_article_name)
    . '&f_article_type=' . urlencode($f_article_type)
    . "&f_article_language=$f_article_language";
if ($f_publication_id != 0) {
    $linkArgs .= "&f_publication_id=$f_publication_id"
        . "&f_issue_number=$f_issue_number"
        . "&f_section_number=$f_section_number"
        . "&f_language_id=$f_language_id";
    $backLink = "/$ADMIN/articles/add.php" . $linkArgs;
} else {
    $backLink = "/$ADMIN/articles/add_move.php" . $linkArgs;
}

// Check input
if (empty($f_article_name)) {
    camp_html_add_msg($translator->trans('You must fill in the $1 field.', array('$1' => '<B>'.$translator->trans('Name').'</B>')));
}

if (empty($f_article_type)) {
    camp_html_add_msg($translator->trans('You must select an article type.'));
}

if (empty($f_article_language)) {
    camp_html_add_msg($translator->trans('You must select a language.'));
}

if (camp_html_has_msgs()) {
    camp_html_goto_page($backLink);
}

$articleType = new ArticleType($f_article_type);
if (!$articleType->exists()) {
    camp_html_display_error($translator->trans('Invalid type: $1', array('$1' => $f_article_type), 'articles'));
}

$publication_id = ($f_destination_publication_id > 0) ? $f_destination_publication_id : $f_publication_id;
$issue_number = ($f_destination_issue_number > 0) ? $f_destination_issue_number : $f_issue_number;
$section_number = ($f_destination_section_number > 0) ? $f_destination_section_number : $f_section_number;

if ($publication_id > 0) {
    $publicationObj = new Publication($publication_id);
    if (!$publicationObj->exists()) {
        camp_html_display_error($translator->trans('Publication does not exist.'));
        exit;
    }

    if ($issue_number > 0) {
        $issueObj = new Issue($publication_id, $f_article_language, $issue_number);
        if (!$issueObj->exists()) {
            camp_html_display_error($translator->trans('Issue does not exist.'));
            exit;
        }

        if ($section_number > 0) {
            $sectionObj = new Section($publication_id, $issue_number, $f_article_language, $section_number);
            if (!$sectionObj->exists()) {
                camp_html_display_error($translator->trans('Section does not exist.'));
                exit;
            }
        }
    }
}

// Create article
$articleObj = new Article($f_article_language);

$conflictingArticles = Article::GetByName($f_article_name, $publication_id, $issue_number, $section_number, null, true);
if (count($conflictingArticles) > 0) {
    $conflictingArticle = array_pop($conflictingArticles);
    $conflictingArticleLink = camp_html_article_url($conflictingArticle, $conflictingArticle->getLanguageId(), 'edit.php');
    camp_html_add_msg($translator->trans("You cannot have two articles in the same section with the same name.  The article name you specified is already in use by the article $1.",
         array('$1' => "<a href='$conflictingArticleLink'>".$conflictingArticle->getName()."</a>"), 'articles'));
    camp_html_goto_page($backLink);
} else {
    $articleObj->create($f_article_type, $f_article_name, $publication_id, $issue_number, $section_number);
}

if ($articleObj->exists()) {
    $em = $this->_helper->service('em');
    $currentUser = $this->_helper->service('user')->getCurrentUser();
    $author = $currentUser->getAuthorId();
    $articleObj->setCreatorId($g_user->getUserId());
    if (empty($author)) {
        $authorObj = new Author($g_user->getRealName());
        if (!$authorObj->exists()) {
            $authorData = Author::ReadName($g_user->getRealName());
            $authorData['email'] = $g_user->getEmail();
            $authorObj->create($authorData);
        } else {
            $authorUser = $em->getRepository('Newscoop\Entity\Author')
                ->findOneById($authorObj->getId());

            $currentUser->setAuthor($authorUser);
            $em->flush();
        }
    } else {
        $authorObj = new Author($author);
    }

    if ($authorObj->exists()) {
        $articleObj->setAuthor($authorObj);
    }

    $articleObj->setIsPublic(true);
    if ($publication_id > 0) {
        $commentDefault = $publicationObj->commentsArticleDefaultEnabled();
        $articleObj->setCommentsEnabled($commentDefault);
    }

    camp_html_add_msg($translator->trans("Article created.", array(), 'articles'), "ok");
    camp_html_goto_page(camp_html_article_url($articleObj, $f_language_id, "edit.php"), false);
    ArticleIndex::RunIndexer(3, 10, true);
    exit();
} else {
    camp_html_display_error($translator->trans("Could not create article.", array(), 'articles'));
}
