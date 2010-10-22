<table border="0" cellspacing="0" cellpadding="0" width="100%" class="breadcrumbHolder"><tr><td class="breadcrumbTD"><span><span class="breadcrumb">Configure</span></span><span class="breadcrumb_separator">&nbsp;</span></td></tr><tr><td class="activeSection" ><span class='breadcrumb_intra_separator'><span class="breadcrumb_active">Manage Authors</span></span><span>&nbsp;</span></td></tr></table>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/campsite.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.config.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.core.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.lang-enUS.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.validators.js"></script>

<!--Content-->
<div class="floatBox" style="margin-top:5px">
<?php
    require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
    require_once($GLOBALS['g_campsiteDir'].'/classes/Image.php');
    require_once($GLOBALS['g_campsiteDir'].'/classes/ImageSearch.php');
    require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');

    // TODO: permissions
    if (!is_writable($Campsite['IMAGE_DIRECTORY'])) {
        camp_html_add_msg(getGS("Unable to add new image, target directory is not writable."));
        camp_html_add_msg(camp_get_error_message(CAMP_ERROR_WRITE_DIR, $Campsite['IMAGE_DIRECTORY']));
        camp_html_goto_page("/$ADMIN/users/authors.php");
        exit;
    }
    if (!$g_user->hasPermission('EditAuthors')) {
        camp_html_display_error(getGS("You do not have the permission to change authors."));
        exit;
    }

    $id=Input::Get("id","int",-1);
    $del_id = Input::Get("del_id","int".-1);
    if ($del_id>-1)
    {
        $author = new Author($del_id);
        $author->delete();
        $logtext = getGS('Author id "$1" deleted.', $del_id);
        Log::Message($logtext, $g_user->getUserId(), 173);
        camp_html_add_msg(getGS("Author deleted.","ok"));
    }

    $add_author_type = Input::Get("add_author","string",null);
    if ($add_author_type!==null){
        if (Author::addAuthorType($add_author_type)===true){
            camp_html_add_msg(getGS("Author type added."), "ok");
        } else{
            camp_html_add_msg(getGS("Cannot add author type, this type already exists."));
        }
    }

    $del_id_type = Input::Get("del_id_type","int".-1);
    if ($del_id_type>-1)
    {
        Author::removeAuthorType($del_id_type);
        camp_html_add_msg(getGS("Author type removed."), "ok");
    }

    $first_name =Input::Get("first_name");
    $last_name = Input::Get("last_name");
    $can_save=false;
    if ($id>-1 && strlen($first_name)>0 && strlen($last_name) > 0 ) $can_save = true;
    if ($id>-1 && $can_save)
    {
        $author = new Author();
        if ($id>0)
        {
            $author = new  Author($id);
        } else
        {
            $author->create(array('first_name'=>Input::Get("first_name"),'last_name'=>Input::Get("last_name")));
        }
        
        $uploadFileSpecified = isset($_FILES['file'])
        && isset($_FILES['file']['name'])
        && !empty($_FILES['file']['name']);
        

        $author->setFirstName(Input::Get("first_name"));
        $author->setLastName(Input::Get("last_name"));
        $author->commit();

        $types = Input::Get("type","array");
        $author->resetTypes();
        foreach ($types as $type)
        {
            $author->setType($type);
        }
        
        $author->setSkype(Input::Get("skype"));
        $author->setJabber(Input::Get("jabber"));
        $author->setAim(Input::Get("aim"));
        $author->setEmail(Input::Get("email"));
        
        $author->setBiography(Input::Get("biography"),Input::Get("lang","int",0), Input::Get("lang_first_name"), Input::Get("lang_last_name"));
        if ($uploadFileSpecified)
        {
            $attributes = array();
            $image = Image::OnImageUpload($_FILES['file'], $attributes);
            if (PEAR::isError($image)) {
                camp_html_add_msg($image->getMessage());
            }
            $author->setImage($image->getImageId());
        }
        
        $aliases = Input::Get("alias","array");
        if (!empty($aliases))
        {
            $author->setAliases($aliases);
        }
        
        $logtext = getGS('Author information has been changed for "$1"', $author->getName());
        Log::Message($logtext, $g_user->getUserId(), 172);
        camp_html_add_msg(getGS("Author saved."),"ok");
    } else if ($id>-1 && !$can_save)
    {
        camp_html_add_msg(getGS("Please fill at least first name and last name."));
    }
    $author = new Author(1);
    if ($id==-1) $id=0;
    camp_html_display_msgs();
?>


    <div class="editBox">

<div class="formBlock formBlockSolo ">
              <input type="text" id="form_search" onchange="doSearch()" onkeyup="doSearch()" class="input_text" size="41" style="width:370px;"><a href="#" class="arrowButton"></a>
      </div>
        <div class="formBlock formBlockSolo">
<div class="scrollHolder"  style="height:100%">
              <div id="pane2" class="scroll-pane">
                <ul>
                  <li>
                    <input type="checkbox" name="all_authors" id="all_authors" class="input_checkbox"  checked="checked" onclick="typeFilter(0)" />
                    <label for="all_authors">All Author Types</label>
                  </li>
                       <?php
                            $types = Author::getTypes();
                              foreach ($types as $type){
                                    echo '<li>
                                                <input type="checkbox" name="One" value="' . $type['type'] . '" id="author_' . $type['id'] . '" class="input_checkbox checkbox_filter" onclick="typeFilter(' . $type['id'] . ')"/>
                                                <label for="One">' . $type['type'] . '</label>';
                                                //<a href="?del_id_type=' . $type['id'] . '" onclick="" style="float:right"><img src="../../css/delete.png" border="0" alt="Delete author" title="Delete author" /></a>
                                    echo '<a href="?del_id_type=' . $type['id'] . '" onclick="return deleteType(' . $type['id'] . ')" style="float:right"><img src="../../css/delete.png" border="0" alt="Delete author" title="Delete author" /></a>';
                                            echo '</li>';
                                }
                      ?>
                  <li>Add author type:</li>
                  <li><form method="post" onsubmit="return addAuthorType()"><input type="text" maxlength="35" class="input_text" id="add_author"  name="add_author" style="width:70%;" /><input type="submit" name="save" id="save" value="Add" class="buttonStrong"  /></form></li>
                </ul>
              </div>
            </div>
        </div>
        <div class="formBlock formBlockSolo lastBlock">

<div id="gridtable" style="float:left" class="box_table"></div>



      </div>
    </div>
</div>






<div id="leftcolumn" style="float:left">
<div id="detailtable" class="" >Loading Data...</div>
</div>
<script type="text/javascript" charset="utf-8">
    var oTable;
    $(document).ready(function() {
        $.get('authors_ajax/grid.php',function (data){
            $("#gridtable").html(data);
            oTable=$('#gridx').dataTable( {
                "bLengthChange": false,
                "bFilter": true, 'bJQueryUI':true} );
            $("#gridx_filter").html('');
        });
        getRow(<?php echo $id; ?>);
    } );

    function addAlias(){
        $("#aliases").append('<input type="text" class="input_text" name="alias[]" size="41" spellcheck="false" style="width:322px;margin-left:127px">');
    }

    function addAuthorType(){
        var val= $('#add_author').val();
        val = jQuery.trim(val);
        if (val.length<3) {
            alert("<?php echo putGS("Author type must be at least three characters long.") ?>");
            return false;
        }
    }
function deleteType(id) {
if (!confirm('<?php echo getGS('Are you sure you want to delete this author type?'); ?>'))
    return false;
    $.post('?del_id_type=' + id, function(data) {
    $.get('authors_ajax/grid.php',function (data){
            $("#gridtable").html(data);
            oTable=$('#gridx').dataTable( {
                "bLengthChange": false,
                "bFilter": true, 'bJQueryUI':true} );
            $("#gridx_filter").html('');
        });
});
return false;
}

function deleteAuthor(id){
    if (!confirm('<?php echo getGS('Are you sure you want to delete this author;')?>')) return false;
    $.post('?del_id=' + id, function(data) {
        window.location.replace("?");
    });
}
    function getRow(id){
        $.get('authors_ajax/detail.php?id=' + id, function(data)
        {
            $("#detailtable").html(data);
           $(function() {
		$(".tabs").tabs({ selected: 1 });
            });
        });
    }

    function changeBio(id){
        $.getJSON('authors_ajax/detail.php?id=' + id + '&getBio=1&language=' + $("#lang").val(), function(data) {
            $("#biography").html(data.biography);
            $("#lang_first_name").val(data.first_name);
            $("#lang_last_name").val(data.last_name);
          });
    }

    function changeTranslation(id){
            $.getJSON('authors_ajax/detail.php?id=' + id + '&getBio=1&language=' + $("#lang_trans").val(), function(data)
            {
                $("#transArea").html(data.biography);
            }
        )
    }

    function doSearch(){
        oTable.fnFilter( $("#form_search").val(),0 );
    }

    function typeFilter(id)
    {
        if (id==0 && $("#all_authors").attr('checked')){
            $(".checkbox_filter").removeAttr('checked');
            oTable.fnFilter( '',1 );
            return;
        }
        var str="";
        var multiple=false;
        var is_checked=false;
        if (id>0){
            $("input[type=checkbox][checked][:not('#all_authors')]").not('#all_authors').each(
                function() {
                    is_checked=true;
                    if (multiple) str = str + "|";
                    str = str +$("#" + this.id).val();
                    multiple=true;
                }
            );
        }
        if (is_checked) $("#all_authors").removeAttr('checked')
        else
            $("#all_authors").attr('checked','checked');

        oTable.fnFilter(str,1 ,true,true);
    }

</script>

<?php camp_html_copyright_notice(); ?>
