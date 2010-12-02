<?php
/**
 * @package Campsite
 */
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

$id = Input::Get("id", "int", -1);

// Delete author
$del_id = Input::Get('del_id', 'int', -1);
if ($del_id > -1) {
    $author = new Author($del_id);
    if ($author->delete()) {
        camp_html_add_msg(getGS('Author deleted.', 'ok'));
    } else {
        camp_html_add_msg(getGS(''));
    }
}

// Add new author type
$add_author_type = Input::Get('add_author', 'string', null);
if ($add_author_type !== null) {
    $authorTypeObj = new AuthorType();
    if ($authorTypeObj->create($add_author_type) === true) {
        camp_html_add_msg(getGS('Author type added.'), 'ok');
    } else {
        camp_html_add_msg(getGS('Cannot add author type, this type already exists.'));
    }
}

// Delete author type
$del_id_type = Input::Get('del_id_type', 'int', -1);
if ($del_id_type > -1) {
    $authorTypeObj = new AuthorType($del_id_type);
    if ($authorTypeObj->delete()) {
        camp_html_add_msg(getGS('Author type removed.'), 'ok');
    } else {
        camp_html_add_msg(getGS('Cannot remove author type.'));
    }
}

// Delete author alias
$del_id_alias = Input::Get('del_id_alias', 'int', -1);
if ($del_id_alias > -1) {
    $authorAliasObj = new AuthorAlias($del_id_alias);
    if ($authorAliasObj->delete()) {
        camp_html_add_msg(getGS('Author alias removed.'), 'ok');
    } else {
        camp_html_add_msg(getGS('Cannot remove author alias.'));
    }
}

$first_name =Input::Get('first_name');
$last_name = Input::Get('last_name');
$can_save = false;
if ($id > -1 && strlen($first_name) > 0 && strlen($last_name) > 0) {
    $can_save = true;
}
if ($can_save) {
    $author = new Author();
    if ($id > 0) {
        $author = new Author($id);
        $isNewAuthor = false;
    } else {
        $author->create(array('first_name' => $first_name, 'last_name' => $last_name));
        $isNewAuthor = true;
    }

    $uploadFileSpecified = isset($_FILES['file'])
        && isset($_FILES['file']['name'])
        && !empty($_FILES['file']['name']);

    $author->setFirstName($first_name);
    $author->setLastName($last_name);
    $author->commit();

    // Reset types
    $types = Input::Get("type", "array");
    AuthorAssignedType::ResetAuthorAssignedTypes($author->getId());
    foreach ($types as $type) {
        $author->setType($type);
    }

    $author->setSkype(Input::Get("skype"));
    $author->setJabber(Input::Get("jabber"));
    $author->setAim(Input::Get("aim"));
    $author->setEmail(Input::Get("email"));

    $authorBiography = array();
    $authorBiography['biography'] = Input::Get("txt_biography", "string");
    $authorBiography['language'] = Input::Get("lang", "int", 0);
    $authorBiography['first_name'] = Input::Get("lang_first_name");
    $authorBiography['last_name'] = Input::Get("lang_last_name");
    $author->setBiography($authorBiography);
    if ($uploadFileSpecified) {
        $attributes = array();
        $image = Image::OnImageUpload($_FILES['file'], $attributes);
        if (PEAR::isError($image)) {
            camp_html_add_msg($image->getMessage());
        }
        $author->setImage($image->getImageId());
    }
        
    $aliases = Input::Get("alias", "array");
    if (!empty($aliases)) {
        $author->setAliases($aliases);
    }

    if ($isNewAuthor) {
        $logtext = getGS('New author "$1" ($2) created.',
            $author->getName(), $author->getId());
        Log::Message($logtext, $g_user->getUserId(), 172);
    } else {
        $logtext = getGS('Author information has been changed for "$1" ($2)',
            $author->getName(), $author->getId());
        Log::Message($logtext, $g_user->getUserId(), 173);
    }
    camp_html_add_msg(getGS("Author saved."),"ok");
} elseif (!$del_id_alias && $id > -1 && !$can_save) {
    camp_html_add_msg(getGS("Please fill at least first name and last name."));
}

if (!$id) {
    $author = new Author(1);
    if ($id == -1) {
        $id = 0;
    }
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Manage Authors"), "");
$breadcrumbs = camp_html_breadcrumbs($crumbs);
echo $breadcrumbs;
?>

<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/campsite.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.config.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.core.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.lang-enUS.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.validators.js"></script>

<!--Content-->
<div class="floatBox" style="margin-top:5px">
<?php camp_html_display_msgs(); ?>
  <div class="editBox">
    <div class="formBlock formBlockSolo">
      <input type="text" id="form_search" onchange="doSearch()" onkeyup="doSearch()" class="input_text" size="41" style="width:370px;" /><a href="#" class="arrowButton"></a>
    </div>
    <div class="formBlock formBlockSolo">
      <div class="scrollHolder" style="height:100%">
        <div id="pane2" class="scroll-pane">
          <ul>
            <li>
              <input type="checkbox" name="all_authors" id="all_authors" class="input_checkbox"  checked="checked" onclick="typeFilter(0)" />
              <label for="all_authors"><?php putGS('All Author Types'); ?></label>
            </li>
            <?php
            $types = AuthorType::GetAuthorTypes();
            foreach ($types as $type) {
                echo '<li>
                    <input type="checkbox" name="One" value="' . $type->getName() . '" id="author_' . $type->getId() . '" class="input_checkbox checkbox_filter" onclick="typeFilter(' . $type->getId() . ')" />
                    <label for="One">' . $type->getName() . '</label>';
                echo '<a href="?del_id_type=' . $type->getId() . '" onclick="return deleteAuthorType(' . $type->getId() . ')" style="float:right"><img
                  src="../../css/delete.png" border="0" alt="' . getGS('Delete author type') . '" title="' . getGS('Delete author type') . '" /></a>';
                echo '</li>';
            }
            ?>
            <li><?php putGS('Add author type'); ?>:</li>
            <li>
              <form method="post" onsubmit="return addAuthorType()">
                <input type="text" maxlength="35" class="input_text" id="add_author" name="add_author" style="width:70%;" />
                <input type="submit" name="save" id="save" value="<?php putGS('Add'); ?>" class="buttonStrong" />
              </form>
            </li>
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
  <div id="detailtable" class=""><?php putGS('Loading Data'); ?>...</div>
</div>
<script type="text/javascript" charset="utf-8">
var oTable;
$(document).ready(function() {
    $.get('authors_ajax/grid.php',function (data) {
        $("#gridtable").html(data);
        oTable=$('#gridx').dataTable( {
            'bLengthChange': false,
            'bFilter': true,
            'bJQueryUI':true,
            'aoColumnDefs': [
                { // not sortable
                    'bSortable': false,
                    'aTargets': [1, 2],
                }
            ]
        });
        $("#gridx_filter").html('');
    });
    getRow(<?php echo $id; ?>);
});

function addAlias() {
    $("#aliases").append('<input type="text" class="input_text" name="alias[]" size="41" spellcheck="false" style="width:322px;margin-left:127px">');
}

function addAuthorType() {
    var val= $('#add_author').val();
    val = jQuery.trim(val);
    if (val.length < 3) {
        alert("<?php echo putGS("Author type must be at least three characters long.") ?>");
        return false;
    }
}

function deleteAuthorType(id) {
    if (!confirm('<?php echo getGS('Are you sure you want to delete this author type?'); ?>')) {
        return false;
    }
    $.post('?del_id_type=' + id, function(data) {
        $.get('authors_ajax/grid.php',function (data) {
            $("#gridtable").html(data);
            oTable=$('#gridx').dataTable({
                "bLengthChange": false,
                "bFilter": true,
                'bJQueryUI':true
            });
            $("#gridx_filter").html('');
        });
        window.location.replace("?");
    });
    return false;
}

function deleteAuthorAlias(id, authorId) {
    if (!confirm('<?php echo getGS('Are you sure you want to delete this author alias?')?>')) {
        return false;
    }
    $.post('?id=' + authorId + '&del_id_alias=' + id, function(data) {
        window.location.replace("?id=" + authorId);
    });
}

function deleteAuthor(id) {
    if (!confirm('<?php echo getGS('Are you sure you want to delete this author?')?>')) {
        return false;
    }
    $.post('?del_id=' + id, function(data) {
        window.location.replace("?");
    });
}

function getRow(id) {
    $.get('authors_ajax/detail.php?id=' + id, function(data) {
        $("#detailtable").html(data);
        $(function() {
            $(".tabs").tabs({ selected: 0 });
        });
    });
}

function changeBio(id) {
    $.getJSON('authors_ajax/detail.php?id=' + id + '&getBio=1&language=' + $("#lang").val(), function(data) {
        $("#txt_biography").html(data.biography);
        $("#lang_first_name").val(data.first_name);
        $("#lang_last_name").val(data.last_name);
    });
}

function changeTranslation(id) {
    $.getJSON('authors_ajax/detail.php?id=' + id + '&getBio=1&language=' + $("#lang_trans").val(), function(data) {
        $("#transArea").html(data.biography);
    });
}

function doSearch() {
    oTable.fnFilter( $("#form_search").val(),0);
}

function typeFilter(id) {
    if (id==0 && $("#all_authors").attr('checked')) {
        $(".checkbox_filter").removeAttr('checked');
        oTable.fnFilter( '',1 );
        return;
    }
    var str="";
    var multiple=false;
    var is_checked=false;
    if (id > 0) {
        $("input[type=checkbox][checked][:not('#all_authors')]").not('#all_authors').each(
            function() {
                is_checked=true;
                if (multiple) str = str + "|";
                str = str +$("#" + this.id).val();
                multiple=true;
            }
        );
    }
    if (is_checked) {
        $("#all_authors").removeAttr('checked');
    } else{
        $("#all_authors").attr('checked','checked');
    }

    oTable.fnFilter(str,1 ,true,true);
}
</script>

<?php camp_html_copyright_notice(); ?>