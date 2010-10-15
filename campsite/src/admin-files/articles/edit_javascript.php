<!-- YUI code //-->
<script>
var newQS = "";

function onButtonClick(buttonId) {
    makeRequest(buttonId);
}

<?php
    foreach ($saveButtonNames as $name) {
      ?>$("#<?php echo $name;?>").button({disabled:true}).click(function() { onButtonClick(this.id) }); <?php echo"\n";
    }
?>


function buttonEnable(buttonId) {
    $("#" + buttonId).button( "option", "disabled", false );
    $("#" + buttonId).button( "option", "label", "<?php putGS('Save'); ?>" );
}

function buttonDisable(buttonId) {
    $("#" + buttonId).button( "option", "disabled", true );
    $("#" + buttonId).button( "option", "label", "<?php putGS('Saved');?>" );
}

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
    $("#dialogBox").dialog("open");

    var query_string = ''; 
    $("input[type='text'][name='f_article_author[]']").each( 
        function() 
        {
            query_string += "&f_article_author[]=" + this.value;
        }
    );
    $(".aaselect").each(
        function()
        {
            query_string += "&f_article_author_type[]=" + this.value;
        }
    );

    if (query_string.length==0) query_string='f_article_author[]=';
    if (a.length>5) var postAction = '&f_save=' + a.substr(5);
    else var postAction = '&f_save=' + a;

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
      newQS = $("#mainForm").find("input,textarea,select,hidden").not(".aauthor").serialize();
      newQS = newQS + postCustomFieldsData + postCustomSwitchesData + postCustomTextareasData;

    $.ajax({
        type: 'POST',
        url: sUrl,
        data: newQS + postAction +query_string,
        success: function(data,status,p){
            if(p.responseText !== undefined){
                $("#yui-connection-message").show().html('<?php putGS("Article Saved"); ?>');
                $('#yui-saved','#yui-connection-error').hide();
                $("#dialogBox").dialog('close');
            }
      },
      error: function (rq, status, error){
        if(status == 0 || status == -1) {
            $("#yui-connection-message").hide();
            $("#yui-connection-error").show().html('<?php putGS("Unable to reach Campsite. Please check your internet connection."); ?>');
            $("#dialogBox").dialog('close');
        }
      }
    });


    if (a == "all") {
        <?php
            foreach ($saveButtonNames as $saveButtonName) {
        ?>
                buttonDisable("<?php print($saveButtonName); ?>");
        <?php
            }
        ?>
    } else {
        buttonDisable(a);
    }
}
$(function() {
    var authorsList = [
        <?php
        $allAuthors = Author::GetAllExistingNames();
        $quoteStringFn = create_function('&$value, $key',
        '$value = "\"" . camp_javascriptspecialchars($value) . "\"";');
        array_walk($allAuthors, $quoteStringFn);
        echo implode(",\n", $allAuthors);
        ?>
    ];
    $( ".aauthor" ).autocomplete({
    source: authorsList
    });
});
</script>
<!-- END YUI code //-->
