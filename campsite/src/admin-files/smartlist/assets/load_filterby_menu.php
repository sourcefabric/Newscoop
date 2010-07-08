<?php

$publication_id = isset($_REQUEST['publication']) ? (int) $_REQUEST['publication'] : 0;
$issue_nr = isset($_REQUEST['issue']) ? (int) $_REQUEST['issue'] : 0;
$section_nr = isset($_REQUEST['section']) ? (int) $_REQUEST['section'] : 0;

$list = array();
$action = $_POST['action'];
switch($action) {
    case 'content':
        if ($issue_nr) {
            $list[0] = getGS('All Sections');
            $sections = Section::GetSections($publication_id, $issue_nr);
            if (is_array($sections) && sizeof($sections) > 0) {
                foreach($sections as $section) {
                    $list[$section->getSectionNumber()] = $section->getName();
                }
            }
        }

        if (empty($list) && $publication_id) {
            $list[0] = getGS('All Issues');
            $issues = Issue::GetIssues($publication_id);
            if (is_array($issues) && sizeof($issues) > 0) {
                foreach($issues as $issue) {
                    $list[$issue->getIssueNumber()] = $issue->getName();
                }
            }
        }
        break;
    case 'filterby':
        $filterBy = $_POST['filterby'];
        switch($filterBy) {
            case 'author':
                $authors = Author::GetAuthors();
                if (!is_array($authors)) $authors = array();
                foreach($authors as $author) {
                    $list[$author->getName()] = $author->getName();
                }
                break;
            case 'iduser':
                $users = User::GetUsers();
                foreach($users as $user) {
                    $list[$user->getUserId()] = $user->getRealName();
                }
                break;
            case 'type':
                $articleTypes = ArticleType::GetArticleTypes();
                foreach($articleTypes as $key => $value) {
                    $tmpArticleType = new ArticleType($value);
                    $list[$tmpArticleType->getTypeName()] = $tmpArticleType->getDisplayName();
                }
                break;
            case 'workflow_status':
                $list = array('published' => 'Published', 'new' => 'New', 'submitted' => 'Submitted');
                break;
        }
        break;
}

$quoteStringFn = create_function('&$value, $key',
    '$value = camp_javascriptspecialchars($value);');
array_walk($list, $quoteStringFn);
foreach($list as $key => $value) {
    if (empty($list[$key])) {
        unset($list[$key]);
        continue;
    }
    $list[$key] = $key . '|' . $value;
}
$jsArray = implode(",", $list);

echo $jsArray;
?>