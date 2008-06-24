<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleAudioclip.php');

$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_sortlist_name = Input::Get('f_sortlist_name', 'string', null, true);
$f_sortlist_order = Input::Get('f_sortlist_order', 'string', null, true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()));
	exit;
}

$articleObj = new Article($f_language_selected, $f_article_number);
if (!$articleObj->exists()) {
	camp_html_display_error(getGS('No such article.'));
    exit;
}

parse_str($f_sortlist_order, $inputArray);
$inputArray = $inputArray[$f_sortlist_name];
$orderArray = array();
for ($i = 0; $i < count($inputArray); $i++) {
    $orderArray[] = array('element' => $inputArray[$i],
                          'order' => $i + 1
                          );
}

foreach ($orderArray as $item) {
    $articleAudioclip = new ArticleAudioclip($f_article_number, $item['element']);
    $articleAudioclip->setOrder($item['order']);
}

camp_html_add_msg(getGS("Audioclips order saved."), "ok");
camp_html_goto_page(camp_html_article_url($articleObj, $f_language_id, "edit.php", "", ""));

?>