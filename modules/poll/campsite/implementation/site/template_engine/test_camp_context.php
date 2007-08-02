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
require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/SyntaxError.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/CampContext.php');

// Meta classes
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaLanguage.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaPublication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaIssue.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaSection.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaArticle.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaImage.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaAttachment.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaAudioclip.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaComment.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaTopic.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaUser.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaTemplate.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaSubscription.php');

// Campsite template class (Smarty extended)
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/CampTemplate.php');

$camp = new CampContext();

//
$camp->publication = new MetaPublication(1);
//
$camp->article = new MetaArticle(1, 14);
//
$camp->language = new MetaLanguage(2);
//
$camp->issue = new MetaIssue(1, 1, 1);
//
$camp->image = new MetaImage(6);

var_dump($camp->publication);
echo '<br /><br />';
var_dump($camp->issue);
echo '<br /><br />';
var_dump($camp->article);
echo '<br /><br />';
var_dump($camp->language);
echo '<br /><br />';
var_dump($camp->image);
echo '<br /><br />';
var_dump($camp->unknown);


?>