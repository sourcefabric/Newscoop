<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Expires" content="now" />
<title><?php putGS("Edit Context Box"); ?></title>
<script type="text/javascript">



function fnLoadContextList(data) {
	if(data.code == 200) {
	    var items = data.items;
	    for(i = 0; i < items.length; i++) {
	        var item = items[i];
	        appendItemToContextList(item.articleId, item.date, item.title);
	    }
	}
}

function loadContextList() {
	var relatedArticles = $('#context_list').sortable( "serialize");
    var aoData = new Array();
    var items = new Array('1_1','0_0');

    aoData.push("context_box_load_list");
    aoData.push(items);
    aoData.push({
        'articleId': '<?php echo Input::Get('f_article_number', 'int', 1)?>',
    });
    callServer(['ArticleList', 'doAction'], aoData, fnLoadContextList);

}

function appendItemToContextList(article_id, article_date, article_title) {
	//alert(article_id+' '+article_date+' '+article_title);
    $("#context_list").append(
    	    '<tr id="'+article_id+'">'+
    	    '<td>'+
    	    '<div class="context-item">'+
            '<div class="context-drag-topics"><a href="#" title="drag to sort"></a></div>'+
            '<div class="context-item-header">'+
                '<div class="context-item-date">'+article_date+'</div>'+
                '<a href="#" class="view-article" style="display: none" onClick="viewArticle($(this).parent(\'div\').parent(\'div\').parent(\'td\').parent(\'tr\').attr(\'id\'));"><?php echo getGS('View article') ?></a>'+
            '</div>'+
            '<a href="#" class="corner-button" style="display: block" onClick="removeFromContext($(this).parent(\'div\').parent(\'td\').parent(\'tr\').attr(\'id\'));"><span class="ui-icon ui-icon-closethick"></span></a>'+
            '<div class="context-item-summary">'+article_title+'</div>'+
            '</div>'+
    	    '</td>'+
    	    '</tr>'
    	    );
    closeArticle();
}

function deleteContextList() {
	$("#context_list").html('');
}

function removeFromContext(param) {
    $("#"+param).remove();
}

function fnPreviewArticle(data) {

	if(data.code == 200) {

		$("#preview-article-date").val(data.date);
		$("#preview-article-title").html(data.title);
		$("#preview-article-body").html(data.title);
		$(".context-block.context-list").css("display","none");
	    $(".context-block.context-article").css("display","block");
	} else {

	}
}

function viewArticle(param) {
	 //alert(param);

	 var relatedArticles = $('#context_list').sortable( "serialize");
	 var aoData = new Array();
	 var items = new Array('1_1','0_0');

     aoData.push("context_box_preview_article");
     aoData.push(items);
     aoData.push({
         'articleId': param,
     });
    $("#preview-article-id").val(param);
    callServer(['ArticleList', 'doAction'], aoData, fnPreviewArticle);


}

function closeArticle() {
    $(".context-block.context-list").css("display","block");
    $(".context-block.context-article").css("display","none");
}

function popup_close() {
	try {
        if (parent.$.fancybox.reload) {
            parent.$.fancybox.message = '<?php putGS('Locations updated.'); ?>';
        }
        parent.$.fancybox.close();
    }
    catch (e) {window.close();}
}

function popup_save() {
    var relatedArticles = $('#context_list').sortable( "serialize");
    var aoData = new Array();
    var items = new Array('1_1','0_0');

    aoData.push("context_box_update");
    aoData.push(items);
    aoData.push({
        'relatedArticles': relatedArticles,
        'articleId': '<?php echo Input::Get('f_article_number', 'int', 1)?>',
    });
    callServer(['ArticleList', 'doAction'], aoData, fnCallback);
}

function fnCallback() {
	//alert('fnCallback functions4');
}

function leClick() {

	var aoData = new Array();

	aoData.push({
        'name': 'iColumns',
        'value': '20',
    });
	aoData.push({
        'name': 'cacat',
        'value': '20',
    });

	callServer(['ArticleList', 'doData'], aoData, fnCallback);
}

   </script>


<?php
$f_context_box = 1;
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/html_head.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/SystemPref.php');


require_once LIBS_DIR . '/ContextList/ContextList.php';

camp_load_translation_strings('articles');

$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 1);
if (isset($_SESSION['f_language_selected'])) {
	$f_old_language_selected = (int)$_SESSION['f_language_selected'];
} else {
	$f_old_language_selected = 0;
}
$f_language_selected = (int)camp_session_get('f_language_selected', 0);
?>


</head>
<body onLoad="return false;">




<div class="content">
<div id="context-box" class="block-shadow">
<div class="toolbar">
<div class="save-button-bar"><input type="submit" name="cancel"
	value="Cancel" class="default-button" onclick="popup_close();"
	id="context_button_close"> <input type="submit" name="save"
	value="Save" class="save-button-small" onclick="popup_save();"
	id="context_button_save"></div>
<h2>Context Box</h2>
</div>
<div class="context-content">
<div class="context-block context-search"><?php

$contextlist = new ContextList();
$contextlist->setSearch(TRUE);
$contextlist->setOrder(TRUE);

//$contextlist->renderFilters();
$contextlist->render();


?></div>
<script>
                 $(function(){
                        $(".dataTables_filter input").attr("placeholder", "Search").addClass("context-search search");
                        //$("#table-<?php echo $contextlist->getId();?>_filter").css("border","0px");
                        $(".fg-toolbar .ui-toolbar .ui-widget-header .ui-corner-tl .ui-corner-tr .ui-helper-clearfix").css("border","none");
                        $(".fg-toolbar .ui-toolbar .ui-widget-header .ui-corner-bl .ui-corner-br .ui-helper-clearfix").css("background-color","#CCCCCC");
                 });
                 $(function(){
                     $('#table-<?php echo $contextlist->getId(); ?> tbody').sortable({
                         connectWith: "#context_list",
                         receive: function(event, ui) {
                             $(ui.item).find(".corner-button").css("display","none");
                             $(ui.item).find(".view-article").css("display","block");
                         }
                     }).disableSelection();
                     $('#context_list').sortable({
                         connectWith: "#table-<?php echo $contextlist->getId(); ?> tbody",
                         receive: function(event, ui) {
                            $(ui.item).find(".corner-button").css("display","block");
                            $(ui.item).find(".view-article").css("display","none");
                         }
                     }).disableSelection();
                     loadContextList();
                 });

                 </script>
<div class="context-block context-list">

<h3>Related Articles</h3>
<div class="context-list-results">
<div class="save-button-bar" style="display: block;"><input
	type="submit" name="delete-all" value="Delete all"
	class="default-button" onclick="deleteContextList()" id="context_button_delete_all"></div>
<div style="display: block; float: left">
<div id="context_list"
	style="display: block; min-height: 500px; min-width: 500px;"></div>
</div>
</div>
</div>

<div class="context-block context-article" style="display: none">
<div class="save-button-bar"><input type="submit"
	name="add-this-article" value="Add this article"
	class="save-button-small" onclick="appendItemToContextList($('#preview-article-id').val(), $('#preview-article-date').val(), $('#preview-article-title').html());" id="context_button_add"> <input
	type="submit" name="close" value="Close" class="default-button"
	onclick="closeArticle();" id="context_button_close_article"></div>
<div class="context-article-preview">

<input id="preview-article-date" type="hidden" />
<input id="preview-article-id" type="hidden" />
<h3 id="preview-article-title"></h3>
<div id="preview-article-body"></div>
</div>
</div>


</div>
</div>
</div>

<p>&nbsp;</p>

<div id="mata" style="border: 1px solid #CCCCCC"></div>



</body>
</html>




