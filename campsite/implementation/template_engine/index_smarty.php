<?php

header("Content-type: text/html; charset=UTF-8");

global $_SERVER;
global $Campsite;
global $DEBUG;

// initialize needed global variables
$_SERVER['DOCUMENT_ROOT'] = getenv("DOCUMENT_ROOT");

require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/parser_utils.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');

// Meta classes
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/MetaPublication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/MetaIssue.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/MetaSection.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/MetaArticle.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/MetaAttachment.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/MetaUser.php');

// Campsite template class (Smarty extended)
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/CampTemplate.php');

// Smarty instance
$tpl = new CampTemplate();


/*** Publication test ***/
$pubObj = new MetaPublication(1);
if ($pubObj->defined()) {
    $publication['name'] = $pubObj->Name;
    $publication['identifier'] = $pubObj->Id;

	$tpl->assign('publication', $publication);
}


/**** Issue test ****/
$issueObj = new MetaIssue(1, 1, 1);
if ($issueObj->defined()) {
    $issue['name'] = $issueObj->Name;
    $issue['number'] = $issueObj->Number;

	$tpl->assign('issue', $issue);
}


/**** Section test ****/
$sectionObj = new MetaSection(1, 1, 1, 50);
if ($sectionObj->defined()) {
    $section['name'] = $sectionObj->Name;
    $section['number'] = $sectionObj->Number;

	$tpl->assign('section', $section);
}


/**** Article test ****/
$article = array();
$articleObj = new MetaArticle(1, 8);

if ($articleObj->defined()) {
    // load article base fields
    $article['number'] = $articleObj->Number;
    $article['name'] = $articleObj->Name;
    $article['type'] = $articleObj->Type;
    $article['keywords'] = $articleObj->Keywords;
    $article['publish_date'] = $articleObj->PublishDate;
    $article['publishDate'] = &$article['publish_date']; 
    $article['upload_date'] = $articleObj->UploadDate;
    $article['uploadDate'] = &$article['upload_date'];
    // load article customized fields
    if ($articleObj->Type == 'Article') {
        foreach($articleObj->m_customFields as $customField) {
            $article[strtolower($customField)] = $articleObj->$customField;
        }
    } else {
        foreach($articleObj->m_customFields as $customField) {
            $article[strtolower($article['type'])][strtolower($customField)] = $articleObj->$customField;
        }
    }

    $tpl->assign('article', $article);
}


/*** Attachment test ***/
$attachObj = new MetaAttachment(1);
if ($attachObj->defined()) {
    $article['attachment']['filename'] = $attachObj->file_name;
    $article['attachment']['mimetype'] = $attachObj->mime_type;
    $article['attachment']['extension'] = $attachObj->extension;
    $article['attachment']['sizeb'] = $attachObj->size_in_bytes;
    $article['attachment']['sizekb'] = $attachObj->size_in_bytes / 1024;
    $article['attachment']['sizemb'] = $attachObj->size_in_bytes / 1048576;

	$tpl->assign('article', $article);
}


/***** User test *****/
$userObj = new MetaUser(1);
if ($userObj->defined()) {
    $user['identifier'] = $userObj->Id;
    $user['name'] = $userObj->Name;
    $user['uname'] = $userObj->UName;
    $user['email'] = $userObj->EMail;
    $user['city'] = $userObj->City;
    $user['straddress'] = $userObj->StrAddress;
    $user['state'] = $userObj->State;
    $user['country'] = $userObj->CountryCode;
    $user['phone'] = $userObj->Phone;
    $user['fax'] = $userObj->Fax;
    $user['employer'] = $userObj->Employer;

	$tpl->assign('user', $user);
}


/**** Exception test ****/
try {
    $articleObj->Name = 'holman';
} catch (Exception $e) {
    echo $e->getMessage();
}


$tpl->display('camp_index.tpl');

?>
