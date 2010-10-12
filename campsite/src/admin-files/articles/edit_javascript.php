  
<!-- YUI code //-->
<script>
var resp = document.getElementById('yui-connection-container');
var mesg = document.getElementById('yui-connection-message');
var emsg = document.getElementById('yui-connection-error');
var saved = document.getElementById('yui-connection-saved');

YAHOO.namespace("example.container");

YAHOO.example.init = function () {

    // "click" event handler for each Button instance
    function onButtonClick(p_oEvent) {
        var fieldPrefix = "save_";
    var buttonId = this.get("id");
    var field = buttonId.substr(fieldPrefix.length);

    makeRequest(field);
    }

    // "contentready" event handler for the "pushbuttonsfrommarkup" <fieldset>
    YAHOO.util.Event.onContentReady("pushbuttonsfrommarkup", function () {
    // Create Buttons using existing <input> elements as a data source
    var oSaveArticleTitleButton = new YAHOO.widget.Button("save_f_article_title", {
            onclick: { fn: onButtonClick },
            disabled: true
    });
    var oSaveArticleAuthorButton = new YAHOO.widget.Button("save_f_article_author", {
            onclick: { fn: onButtonClick },
            disabled: true
    });
    var oSaveKeywordsButton = new YAHOO.widget.Button("save_f_keywords", {
            onclick: { fn: onButtonClick },
            disabled: true
    });
<?php
    foreach ($saveButtons as $saveButton) {
      print($saveButton);
    }
?>
    });
} ();

function buttonEnable(buttonId) {
    var oPushButton = YAHOO.widget.Button.getButton(buttonId);
    oPushButton.set("disabled", false);
    oPushButton.set("label", "<?php putGS('Save'); ?>");
}

function buttonDisable(buttonId) {
    var oPushButton = YAHOO.widget.Button.getButton(buttonId);
    oPushButton.set("disabled", true);
    oPushButton.set("label", "<?php putGS('Saved'); ?>");
}

var handleSuccess = function(o){
    if(o.responseText !== undefined){
        mesg.style.display = 'inline';
        document.getElementById('yui-saved').style.display = 'none';
        var savedTime = makeSavedTime();
        saved.innerHTML = '<?php putGS("Saved:"); ?> ' + savedTime;
        mesg.innerHTML = '<?php putGS("Article Saved"); ?>';
        emsg.style.display = 'none' ;
        $("#dialogBox").dialog('close');
    }
};

var handleFailure = function(o){
    if(o.status == 0 || o.status == -1) {
        mesg.style.display = 'none';
        emsg.style.display = 'inline';
        emsg.innerHTML = '<?php putGS("Unable to reach Campsite. Please check your internet connection."); ?>';
        $("#dialogBox").dialog('close');
    }

};

var callback =
{
    success: handleSuccess,
    failure: handleFailure
};


var sUrl = "<?php echo $Campsite['WEBSITE_URL']; ?>/admin/articles/yui-assets/post.php";


var dialog=  $("#dialogBox").dialog({
    autoOpen: false,
    modal: true,disabled: true,
    title :'<?php putGS('Saving, please wait...'); ?>',
    close : function(event,ui){
        $(this).hide();
    },
    open: function(event, ui) { 
        $(this).parent().children().children('.ui-dialog-titlebar-close').hide();
        }           
    });

function makeRequest(a){
    // Initialize the temporary Panel to display while waiting
    // for article saving
    $("#dialogBox").dialog("open");

    var query_string = ''; 
    $("input[type='text'][name='f_article_author[]']").each( 
        function() 
        {
            query_string += "&f_article_author[]=" + this.value; 
    });
    if (query_string.length==0) query_string='f_article_author[]=';

    YAHOO.example.container.wait =
        new YAHOO.widget.Panel("wait",
                                        { width:"240px",
                      fixedcenter:true,
                      close:false,
                      draggable:false,
                      zindex: 4,
                      modal:true,
                      visible:false
                    }
                   );

      

  /*  YAHOO.example.container.wait.setHeader("<?php putGS('Saving, please wait...'); ?>");
    YAHOO.example.container.wait.setBody("<img src=\"http://us.i1.yimg.com/us.yimg.com/i/us/per/gr/gp/rel_interstitial_loading.gif\"/>");
    YAHOO.example.container.wait.render(document.body);*/

    var postAction = '&f_save=' + a;

    var ycaFArticleTitle = document.getElementById('f_article_title').value;
//    var ycaFArticleAuthor = document.getElementById('f_article_author').value;
    var ycaFOnFrontPage = document.getElementById('f_on_front_page').checked;
    var ycaFOnSectionPage = document.getElementById('f_on_section_page').checked;
    var ycaFCreationDate = document.getElementById('f_creation_date').value;
    var ycaFPublishDate = document.getElementById('f_publish_date').value;
    var ycaFIsPublic = document.getElementById('f_is_public').checked;
    <?php if ($showCommentControls) { ?>
    var ycaFCommentStatus = document.getElementById('f_comment_status').value;
    <?php } ?>
    var ycaFKeywords = document.getElementById('f_keywords').value;
    var ycaFPublicationId = document.getElementById('f_publication_id').value;
    var ycaFIssueNumber = document.getElementById('f_issue_number').value;
    var ycaFSectionNumber = document.getElementById('f_section_number').value;
    var ycaFLanguageId = document.getElementById('f_language_id').value;
    var ycaFLanguageSelected = document.getElementById('f_language_selected').value;
    var ycaFArticleNumber = document.getElementById('f_article_number').value;
    var ycaFMessage = document.getElementById('f_message').value;

    var textFields = [<?php print($jsArrayFieldsStr); ?>];
    var textSwitches = [<?php print($jsArraySwitchesStr); ?>];
    var textAreas = [<?php print($jsArrayTextareasStr); ?>];
    var postCustomFieldsData = '';
    var postCustomSwitchesData = '';
    var postCustomTextareasData = '';

    for (i = 0; i < textFields.length; i++) {
        postCustomFieldsData += '&' + textFields[i] + '=' + encodeURIComponent(document.getElementById(textFields[i]).value);
    }

    for (i = 0; i < textSwitches.length; i++) {
        if (document.getElementById(textSwitches[i]).checked == true)
        postCustomSwitchesData += '&' + textSwitches[i] + '=on';
    else
        postCustomSwitchesData += '&' + textSwitches[i] + '=';
    }

    for (i = 0; i < textAreas.length; i++) {
        var ed = tinyMCE.get(textAreas[i]);
        postCustomTextareasData += '&' + textAreas[i] + '=' + encodeURIComponent(ed.getContent());
    }

    if (ycaFOnFrontPage == true)
        ycaFOnFrontPage = 'on';
    else
        ycaFOnFrontPage = '';
    if (ycaFOnSectionPage == true)
        ycaFOnSectionPage = 'on';
    else
        ycaFOnSectionPage = '';
    if (ycaFIsPublic == true)
        ycaFIsPublic = 'on';
    else
        ycaFIsPublic = '';

    var postData = "f_article_title=" + encodeURIComponent(ycaFArticleTitle)
    //  + "&f_article_author=" + ycaFArticleAuthor
      + query_string
      + "&f_on_front_page=" + ycaFOnFrontPage
      + "&f_on_section_page=" + ycaFOnSectionPage
      + "&f_creation_date=" + ycaFCreationDate
      + "&f_publish_date=" + ycaFPublishDate
      + "&f_is_public=" + ycaFIsPublic
    <?php if ($showCommentControls) { ?>
      + "&f_comment_status=" + ycaFCommentStatus
    <?php } ?>
      + "&f_keywords=" + ycaFKeywords
      + "&f_publication_id=" + ycaFPublicationId
      + "&f_issue_number=" + ycaFIssueNumber
      + "&f_section_number=" + ycaFSectionNumber
      + "&f_language_id=" + ycaFLanguageId
      + "&f_language_selected=" + ycaFLanguageSelected
      + "&f_article_number=" + ycaFArticleNumber
      + "&f_message=" + encodeURIComponent(ycaFMessage)
      + postCustomFieldsData + postCustomSwitchesData
      + postCustomTextareasData + postAction
      + "&<?php echo SecurityToken::URLParameter(); ?>";

    // Show the saving panel
  //  YAHOO.example.container.wait.show();

    var request = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback, postData);
    setTimeout(function() { YAHOO.util.Connect.abort(request, callback) }, 30000);

    if (a == "all") {
        <?php
            foreach ($saveButtonNames as $saveButtonName) {
        ?>
                buttonDisable("<?php print($saveButtonName); ?>");
        <?php
            }
        ?>
    } else {
        buttonDisable("save_" + a);
    }
}

function makeSavedTime() {
    var dt = new Date();
    var hours = dt.getHours();
    var minutes = dt.getMinutes();
    var seconds = dt.getSeconds();

    if (minutes < 10){ minutes = "0" + minutes }
    if (seconds < 10){ seconds = "0" + seconds }

    return hours + ':' + minutes + ':' + seconds;
}

authorsData = {
        arrayAuthors: [
<?php
$allAuthors = Author::GetAllExistingNames();
$quoteStringFn = create_function('&$value, $key',
                 '$value = "\"" . camp_javascriptspecialchars($value) . "\"";');
array_walk($allAuthors, $quoteStringFn);
echo implode(",\n", $allAuthors);
?>
        ]
};


</script>
<!-- END YUI code //-->
