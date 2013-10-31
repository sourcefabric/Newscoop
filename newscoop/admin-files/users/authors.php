<?php
/**
 * @package Newscoop
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Image.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ImageSearch.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');

$translator = \Zend_Registry::get('container')->getService('translator');

// TODO: permissions
if (!is_writable($Campsite['IMAGE_DIRECTORY'])) {
    camp_html_add_msg($translator->trans('Unable to add new image, target directory is not writable.', array(), 'users'));
    camp_html_add_msg(camp_get_error_message(CAMP_ERROR_WRITE_DIR, $Campsite['IMAGE_DIRECTORY']));
    camp_html_goto_page("/$ADMIN/");
    exit;
}
if (!$g_user->hasPermission('EditAuthors')) {
    camp_html_display_error($translator->trans('You do not have the permission to change authors.', array(), 'users'));
    exit;
}

$id = Input::Get('id', 'int', -1);

// Delete author
$del_id = Input::Get('del_id', 'int', -1);
if ($del_id > -1) {
    $author = new Author($del_id);
    if ($author->delete()) {
        camp_html_add_msg($translator->trans('Author deleted.', array(), 'users'), 'ok');
    }
}

// Add new author type
$add_author_type = Input::Get('add_author', 'string', null);
if ($add_author_type !== null) {
    $authorTypeObj = new AuthorType();
    if ($authorTypeObj->create($add_author_type) === true) {
        camp_html_add_msg($translator->trans('Author type added.', array(), 'users'), 'ok');
    } else {
        camp_html_add_msg($translator->trans('Cannot add author type, this type already exists.', array(), 'users'));
    }
}

// Delete author type
$del_id_type = Input::Get('del_id_type', 'int', -1);
if ($del_id_type > -1) {
    $authorTypeObj = new AuthorType($del_id_type);
    if ($authorTypeObj->delete()) {
        camp_html_add_msg($translator->trans('Author type removed.', array(), 'users'), 'ok');
    } else {
        camp_html_add_msg($translator->trans('Cannot remove author type.', array(), 'users'));
    }
}

// Delete author alias
$del_id_alias = Input::Get('del_id_alias', 'int', -1);
if ($del_id_alias > -1) {
    $authorAliasObj = new AuthorAlias($del_id_alias);
    if ($authorAliasObj->delete()) {
        camp_html_add_msg($translator->trans('Author alias removed.', array(), 'users'), 'ok');
    } else {
        camp_html_add_msg($translator->trans('Cannot remove author alias.', array(), 'users'));
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
    $types = Input::Get('type', 'array', array());
    AuthorAssignedType::ResetAuthorAssignedTypes($author->getId());
    foreach ($types as $type) {
        $author->setType($type);
    }

    $author->setSkype(Input::Get('skype'));
    $author->setJabber(Input::Get('jabber'));
    $author->setAim(Input::Get('aim'));
    $author->setEmail(Input::Get('email'));

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
        } else {
            $author->setImage($image->getImageId());
        }
    }

    $aliases = Input::Get("alias", "array");
    if (!empty($aliases)) {
        $author->setAliases($aliases);
    }

    camp_html_add_msg($translator->trans("Author saved.", array(), 'users'),"ok");
} elseif ($del_id_alias < 1 && $id > -1 && !$can_save) {
    camp_html_add_msg($translator->trans("Please fill at least first name and last name.", array(), 'users'));
}

if (!$id || $id == -1) {
    $author = new Author(1);
    if ($id == -1) {
        $id = 0;
    }
}

$crumbs = array();
$crumbs[] = array($translator->trans("Configure"), "");
$crumbs[] = array($translator->trans("Authors"), "");
$breadcrumbs = camp_html_breadcrumbs($crumbs);
echo $breadcrumbs;
?>

<?php include dirname(__FILE__) . '/../javascript_common.php'; ?>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/campsite-checkbox.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $('.filter-button').click(function() {
        $('.container').toggle('fast');
        $(this).toggleClass("close");
        return false;
    }).next().hide();

    $(function(){
        $(".search-box input").attr("placeholder", "Search authors").addClass("context-search search");
    });

});
</script>
<?php camp_html_display_msgs('1em', '0'); ?>
<div class="wrapper">
  <div class="info-bar"> <span class="info-text"></span> </div>
  <!--left column-->
  <div class="column-one">
    <div class="author-list ui-widget-content big-block block-shadow padded-strong">
      <fieldset class="plain">
        <div class="search-box">
          <input type="text" id="form_search" onchange="doSearch()" onkeyup="doSearch()" class="input-transparent" size="45" style="width:220px;" />
          <a href="#" class="filter-button"><?php echo $translator->trans('Filters', array(), 'users'); ?></a>
        </div>
        <div class="container">
          <ul class="check-list padded">
            <li>
              <input type="checkbox" name="all_authors" id="all_authors" class="input_checkbox"  checked="checked" onclick="typeFilter(0)" />
              <label for="all_authors"><?php echo $translator->trans('All Author Types', array(), 'users'); ?></label>
            </li>
            <?php
            $types = AuthorType::GetAuthorTypes();
            foreach ($types as $type) {
                echo '<li>
                    <input type="checkbox" name="One" value="' . $type->getName() . '" id="author_' . $type->getId() . '" class="input_checkbox checkbox_filter" onclick="typeFilter(' . $type->getId() . ')" />
                    <label for="One">' . $type->getName() . '</label>';
                echo '<a href="?del_id_type=' . $type->getId() . '" onclick="return deleteAuthorType(' . $type->getId() . ')" style="float:right"><img
                  src="' . $Campsite['ADMIN_STYLE_URL'] . '/images/delete.png" border="0" alt="' . $translator->trans('Delete author type', array(), 'users') . '" title="' . $translator->trans('Delete author type', array(), 'users') . '" /></a>';
                echo '</li>';
            }
            ?>
            <li><?php echo $translator->trans('Add author type', array(), 'users'); ?>:</li>
            <li>
              <form onsubmit="return addAuthorType()" method="post">
                <input type="text" style="width: 60%; margin-right: 6px" name="add_author" id="add_author" class="input_text" maxlength="35" />
                <input type="submit" class="default-button" value="<?php echo $translator->trans('Add'); ?>" id="save" name="save" />
              </form>
            </li>
          </ul>
        </div>
      </fieldset>
      <fieldset class="plain" style="margin-top:16px;">
        <a onclick="getRow(0)" class="ui-state-default icon-button right-floated" href="#"><span class="ui-icon ui-icon-plusthick"></span><?php echo $translator->trans('Add new Author', array(), 'users'); ?></a>
        <div class="clear"></div>
        <div id="gridtable" style="margin-top:8px;"></div>
      </fieldset>
    </div>
  </div>
  <!--END left column-->
  <!--right column-->
  <div id="detailtable" class="column-two"><?php echo $translator->trans('Loading Data', array(), 'users'); ?>...</div>
</div>
<script type="text/javascript">
$('.icon-button').hover(
   function() { $(this).addClass('ui-state-hover'); },
   function() { $(this).removeClass('ui-state-hover'); }
);
$('.text-button').hover(
   function() { $(this).addClass('ui-state-hover'); },
   function() { $(this).removeClass('ui-state-hover'); }
);

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
                    'aTargets': [1, 2]
                }
            ],
            'fnDrawCallback': function() {
                $('#gridx tbody tr').click(function(event) {
                    $(event.target).removeClass('selected');
                    $(event.target).addClass('selected');
                });
            }
        });
        $("#gridx_filter").html('');
    });
    getRow(<?php echo $id; ?>);
});

function addAlias() {
    $("#aliases").append('<div class="authorAliasItem"><input type="text" class="input_text authorAlias" name="alias[]" size="41" spellcheck="false" style="width:322px;margin-left:127px"></div>');
}

function addAuthorType() {
    var val= $('#add_author').val();
    val = jQuery.trim(val);
    if (val.length < 3) {
        alert("<?php echo $translator->trans("Author type must be at least three characters long.", array(), 'users') ?>");
        return false;
    }
}

function deleteAuthorType(id) {
    if (!confirm('<?php echo $translator->trans('Are you sure you want to delete this author type?', array(), 'users'); ?>')) {
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
    if (!confirm('<?php echo $translator->trans('Are you sure you want to delete this author alias?', array(), 'users')?>')) {
        return false;
    }
    $.post('?id=' + authorId + '&del_id_alias=' + id, function(data) {
        window.location.replace("?id=" + authorId);
    });
}

function deleteAuthor(id) {
    if (!confirm('<?php echo $translator->trans('Are you sure you want to delete this author?', array(), 'users')?>')) {
        return false;
    }

    $.post('?del_id=' + id, function(data, textStatus, jqXHR) {
        window.location.replace("?");
    });

    return false;
}

function getRow(id) {
    $.get('authors_ajax/detail.php?id=' + id, function(data) {
        $('#detailtable').hide();
        $("#detailtable").html(data);
        $(function() {
            $(".tabs").tabs({ selected: 0 });
        });
        $('#detailtable').show();
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
    if (id == 0 && $("#all_authors").attr('checked')) {
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
