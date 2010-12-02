<?php
/**
 * Campsite
 */
require_once($GLOBALS['g_campsiteDir'] . '/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'] . '/classes/Image.php');
require_once($GLOBALS['g_campsiteDir'] . '/classes/ImageSearch.php');
require_once($GLOBALS['g_campsiteDir'] . '/classes/Log.php');

$first_name = "";
$last_name = "";
$aliases = "";
$type = array();
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
        $bioObj = new AuthorBiography($author->getId(), $language);
        if ($bioObj->exists()) {
            $json = sprintf($json, addslashes($bioObj->getFirstName()), addslashes($bioObj->getLastName()), addslashes($bioObj->getBiography()));
        }
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
?>

<form method="post" enctype="multipart/form-data">
<?php echo SecurityToken::FormParameter(); ?>
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<div class="tabs authors">
  <ul>
    <li><a href="#generalContainer"><?php putGS('General'); ?></a></li>
    <li><a href="#biographyContainer"><?php putGS('Biography'); ?></a></li>
  </ul>
  <div id="generalContainer">
    <div class="formBlock firstBlock firstBlock">
      <ul>
        <li>
          <label><?php putGS('First name'); ?>:</label>
          <input type="text" name="first_name" class="input_text" size="41" value="<?php echo $first_name; ?>" />
        </li>
        <li>
          <label><?php putGS('Last name'); ?>:</label>
          <input type="text" name="last_name"  class="input_text" size="41" spellcheck="false" value="<?php echo $last_name; ?>" />
        </li>
        <li>
          <label>Aliases:</label>
          <span id="aliases">
          <?php
          $count = 0;
          if (isset($aliases) && is_array($aliases)) {
              foreach ($aliases as $alias) {
                  $count++;
                  $input = '<div class="author_alias_item">';
                  $input.= '<input type="text" name="alias[]" class="input_text" size="41" spellcheck="false" style="width:322px;margin-left:127px" value="%s" />';
                  $input.= '</div>';
                  echo sprintf($input, $alias->getName());
                  echo '<a href="?id=' . $author->getId() . '&del_id_alias=' . $alias->getId() . '" onclick="return deleteAuthorAlias(' . $alias->getId() . ',' . $author->getId() . ')" style="float:right"><img
                      src="../../css/delete.png" border="0" alt="' . getGS('Delete author alias') . '" title="' . getGS('Delete author alias') . '" /></a>';
              }
          }
          //if ($count==0) {
          ?>
            <div class="author_alias_item">
              <input type="text" name="alias[]" value="" class="input_text" size="41" spellcheck="false" style="width:322px;margin-left:127px" />
            </div>
          <?php // } else { ?>
            <!-- <div class="author_alias_item">
              <input type="text" name="alias[]" class="input_text" size="41" spellcheck="false" style="width:322px;" value="" />
            </div> -->
          <?php // } ?>
          </span>
          <span onclick="addAlias()"><a href="#" class="addButton"></a></span>
        </li>
        <li>
          <span id="types">
            <label><?php putGS('Type'); ?>:</label>
            <select name="type[]" class="input_select2" onchange="" style="width:324px;height:100%" multiple="multiple">
            <?php
            $types = AuthorType::GetAuthorTypes();
            foreach ($types as $xtype) {
                $str =  '<option value="' . $xtype->getId() . '"';
                if (is_array($type) && in_array(array('fk_type_id' => $xtype->getId()), $type)) {
                    $str .= ' selected="selected"';
                }
                $str .= '>' . $xtype->getName() . '</option>';
                echo $str;
            }
            ?>
            </select>
          </span>
        </li>
      </ul>
    </div>
    <h2><?php putGS('Contacts'); ?></h2>
    <div class="formBlock">
      <ul>
        <li>
          <label><?php putGS('Skype'); ?>:</label>
          <input type="text" name="skype"  class="input_text" size="41" value="<?php echo $skype; ?>" />
        </li>
        <li>
          <label><?php putGS('Jabber'); ?>:</label>
          <input type="text" name="jabber"  class="input_text" size="41" spellcheck="false" value="<?php echo $jabber; ?>" />
        </li>
        <li>
          <label><?php putGS('AIM'); ?>:</label>
          <input type="text" name="aim"  class="input_text" size="41" spellcheck="false" value="<?php echo $aim; ?>" />
        </li>
        <li>
          <label><?php putGS('Email'); ?>:</label>
          <input type="text" name="email" class="input_text" size="41" spellcheck="false" value="<?php echo $email; ?>" />
        </li>
      </ul>
    </div>
    <div class="formBlock lastBlock">
      <ul>
        <li>
          <label>&nbsp;</label>
          <input type="reset" name="reset" id="reset" value="<?php putGS('Reset'); ?>" class="button" onclick="" />
        </li>
      </ul>
    </div>
  </div>

  <div id="biographyContainer">
    <div class="formBlock firstBlock">
      <ul>
        <li>
          <label class="smaller"><?php putGS('Languages'); ?>:</label>
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
              if ($defaultLanguage == 0) {
                  $defaultLanguage = $language;
              }
              $combo .= '<option value="' . $language . '">' . $lang->getName() . "</option>";
          }
          $biography = "";
          if ($id > 0) {
              $bioObj = new AuthorBiography($id, $defaultLanguage);
              //$biography = $author->getBiography($defaultLanguage);
              //$lang_first_name = $biography[0]["first_name"];
              //$lang_last_name = $biography[0]["last_name"];
              $lang_first_name = $bioObj->getFirstName();
              $lang_last_name = $bioObj->getLastName();
              if (strlen($lang_first_name) == 0) {
                  $lang_first_name = $first_name;
              }
              if (strlen($lang_last_name) == 0) {
                  $lang_last_name = $last_name;
              }
              //$biography = $biography[0]["biography"];
              $biography = $bioObj->getBiography();
          }
          echo $combo;
          ?>
          </select>
        </li>
        <li>
          <label class="smaller"><?php putGS('Translate from'); ?>:</label>
          <select name="translate" id="lang_trans" class="input_select" style="width:120px;" onchange="changeTranslation(<?php echo $id ?>)">
          <?php echo $combo ?>
          </select>
        </li>
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
        <li>
          <label><?php putGS('First name'); ?>:</label>
          <input type="text" name="lang_first_name" id="lang_first_name" maxlength="35" class="input_text" value="<?php echo $lang_first_name; ?>" emsg="<?php putGS('Please enter the first name'); ?>" style="width:170px;" />
        </li>
        <li>
          <label><?php putGS('Last name'); ?>:</label>
          <input type="text" name="lang_last_name" id="lang_last_name" maxlength="35" class="input_text" value="<?php echo $lang_last_name; ?>" emsg="<?php putGS('Please enter the last name'); ?>" style="width:170px;" />
        </li>
        <li>&nbsp;</li>
        <li>
          <input type="file" name="file" size="32" class="input_file" />
        </li>
      </ul>
    </div>
    <div class="formBlock">
      <div class="textHolder">
        <textarea name="langbio" id="transArea" rows="20" readonly="readonly"><?php echo $biography; ?></textarea>
      </div>
      <div>
        <textarea name="txt_biography" id="txt_biography" class="tinymce" rows="20"><?php echo $biography; ?></textarea>
      </div>
    </div>
    <br style="clear:both;" />
  </div>
  <div class="formBlock lastBlock">
    <ul>
      <li>
        <input type="submit" name="save" id="save" value="<?php putGS('Save All'); ?>" class="buttonStrong" />
      </li>
    </ul>
  </div>
</div>

<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/articles/editor_load_tinymce.php");
$languageObj = new Language($Language);
if (!is_object($languageObj)) {
    $languageObj = new Language(1);
}
$editorLanguage = camp_session_get('TOL_Language', $languageObj->getCode());
editor_load_tinymce('txt_biography', $g_user, 0, $editorLanguage, 'authorbiography');
?>
</form>
