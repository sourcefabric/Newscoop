<?php
require_once($GLOBALS['g_campsiteDir'] . '/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'] . '/classes/Image.php');
require_once($GLOBALS['g_campsiteDir'] . '/classes/ImageSearch.php');
require_once($GLOBALS['g_campsiteDir'] . '/classes/Log.php');
$first_name = "";
$last_name = "";
$aliases = "";
$type = "";
$skype = "";
$jabber = "";
$aim = "";
$networks_names = "";
$network_links = "";
$email = "";
$biography = "";
$aliases = null;
$lang_first_name = "";
$lang_last_name = "";
$id = Input::Get("id", "int", 0);
if ($id > 0) {
    $getBio = Input::Get("getBio", "int", 0);
    $author = new Author($id);
    if ($getBio == 1) {
        $json = '{
                        "first_name":"%s",
                        "last_name":"%s",
                        "biography":"%s"
                    }';
        $language = Input::Get("language", "int", 0);
        $bio = $author->getBiography($language);
        $json = sprintf($json, addslashes($bio[0]["first_name"]), addslashes($bio[0]["last_name"]), addslashes($bio[0]["biography"]));
        echo $json;
        exit();
    }
    if ($getNames == 1) {

    }
    $first_name = $author->getFirstName();
    $last_name = $author->getLastName();
    $type = $author->getType();
    $skype = $author->getSkype();
    $jabber = $author->getJabber();
    $aim = $author->getAim();
    $email = $author->getEmail();
    $aliases = $author->getAliases();
}
?><a href="#" class="addButtonText marginBttmSmall" onclick="getRow(0)">Add new Author</a>
<form method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?php echo $id; ?>"/>
<div class="floatBox bigBox">
    <ul class="tabs">
        <li><a href="#">General</a></li>
        <li><a href="#" class="current">Biography</a></li>
    </ul>
    <!--Pane 1-->
    <div class="pane">
        <div class="formBlock firstBlock firstBlock">
            <ul>
                <li>
                    <label>First name:</label>
                    <input type="text" name="first_name" class="input_text" size="41" value="<?php echo $first_name; ?>">
                </li>
                <li>
                    <label>Last name:</label>
                    <input type="text" name="last_name"  class="input_text" size="41" spellcheck="false" value="<?php echo $last_name; ?>">
                </li>
                <li>
                    <label>Aliases:</label>
                    <span id="aliases">
<?php
if (isset($aliases) && is_array($aliases)) {
    foreach ($aliases as $alias) {
        $input = '<input type="text" name="alias[]" class="input_text" size="41" spellcheck="false" style="width:322px;" value="%s">';
        echo sprintf($input, $alias['alias']);
    }
}?>                 <input type="text" name="alias[]" value="" class="input_text" size="41" spellcheck="false" style="width:322px;">
                    </span><span onclick="addAlias()"><a href="#" class="addButton"></a></span>
                </li>
                <li>
                    <label>Type:</label>
                    <select name="type" class="input_select2" onchange="" style="width:324px;">
                        <option value="0" <?php if ($type==0) echo ' selected="selected"'; ?>>Chose Author Type</option>
                        <option value="1" <?php if ($type==1) echo ' selected="selected"'; ?>>Author</option>
                        <option value="2" <?php if ($type==2) echo ' selected="selected"'; ?>>Photographer</option>
                        <option value="3" <?php if ($type==3) echo ' selected="selected"'; ?>>Editor</option>
                    </select>
                  
                </li>
            </ul>
        </div>
        <h2>Contacts</h2>
        <div class="formBlock">
            <ul>
                <li>
                    <label>Skype:</label>
                    <input type="text" name="skype"  class="input_text" size="41" value="<?php echo $skype; ?>">
                </li>
                <li>
                    <label>Jabber:</label>
                    <input type="text" name="jabber"  class="input_text" size="41" spellcheck="false" value="<?php echo $jabber; ?>">
                </li>
                <li>
                    <label>AIM:</label>
                    <input type="text" name="aim"  class="input_text" size="41" spellcheck="false" value="<?php echo $aim; ?>">
                </li>
                <li>
                    <label>Email:</label>
                    <input type="text" name="email" class="input_text" size="41" spellcheck="false" value="<?php echo $email; ?>">
                </li>

            </ul>
        </div>
        <div class="formBlock lastBlock">
            <ul>
                <li>
                    <label>&nbsp;</label>
                    <input type="reset" name="reset" id="reset" value="Reset" class="button" onclick="" />
                </li>
            </ul>
        </div>
    </div>
    <!--Pane 2-->
    <div class="pane">
        <div class="formBlock firstBlock">
            <ul>
                <li><label class="smaller">Languages:</label>
                    <select name="lang"  class="input_select" style="width:120px;"  id="lang" onchange="changeBio(<?php echo $id ?>)">
<?php
                    $publications = Issue::GetIssues();
                    $languages = array();
                    foreach ($publications as $publication) {
                        $languages[] = $publication->getLanguageId();
                    }
                    $languages = array_unique($languages);
                    $defaultLanguage = 0;
                    $combo = "";
                    foreach ($languages as $language) {
                        $lang = new Language($language);
                        if ($defaultLanguage == 0)
                            $defaultLanguage = $language;
                        $combo .= '<option value="' . $language . '">' . $lang->getName() . "</option>";
                    }
                    $biography = "";
                    if ($id > 0) {
                        $biography = $author->getBiography($defaultLanguage);
                        $lang_first_name = $biography[0]["first_name"];
                        $lang_last_name = $biography[0]["last_name"];
                        if (strlen($lang_first_name) == 0) {
                            $lang_first_name = $first_name;
                        }
                        if (strlen($lang_last_name) == 0) {
                            $lang_last_name = $last_name;
                        }
                        $biography = $biography[0]["biography"];
                    }
                    echo $combo;
?>
                    </select></li>
                <li><label class="smaller">Translate from:</label>
                    <select name="translate" id="lang_trans" class="input_select" style="width:120px;" onchange="changeTranslation(<?php echo $id ?>)">
<?php echo $combo ?>
                    </select></li>
            </ul>
        </div>
        <div class="formBlock">
            <br />
            <div class="authorThumb">
                                    <?php
                    if (is_object($author) && is_numeric($author->getImage())) {
                        $image = new Image($author->getImage());
                        echo '<img src="' . $image->getThumbnailUrl() . '"/>';
                    } else {
                        echo '<img src="../../temp_img/author_1.jpg" width="100" height="120" alt="1" />';
                    }
                    ?>
                </div>
            <ul class="nameList">
                <li><label>First name:</label>
                    <input type="text" name="lang_first_name" id="lang_first_name" maxlength="35" class="input_text" value="<?php echo $lang_first_name; ?>" emsg="Please enter the first name" style="width:170px;" /></li>
                <li><label>Last name:</label>
                    <input type="text" name="lang_last_name" id="lang_last_name" maxlength="35" class="input_text" value="<?php echo $lang_last_name; ?>" emsg="Please enter the last name" style="width:170px;" /></li>
                <li>&nbsp;</li>
                <li>  <input type="file" name="file" size="32" class="input_file"></li>
            </ul>
        </div>
        <div class="formBlock">
            <div class="textHolder">
                <textarea name="langbio" id="transArea" rows="20" readonly="readonly"><?php echo $biography; ?></textarea></div>

                <textarea name="biography" id="bioArea" rows="20"><?php echo $biography; ?></textarea></div>
            <br style="clear:both;" />
        </div>
        <div class="formBlock lastBlock">
            <ul>
                <li>
                    <input type="submit" name="save" id="save" value="Save All" class="buttonStrong"  />
                </li>
            </ul>
        </div>
    </div>
    <!--Pane 3-->




<?php echo SecurityToken::FormParameter(); ?>

</form>

