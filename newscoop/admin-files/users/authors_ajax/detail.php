<?php
/**
 * @package Newscoop
 */
require_once($GLOBALS['g_campsiteDir'] . '/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'] . '/classes/Image.php');
require_once($GLOBALS['g_campsiteDir'] . '/classes/ImageSearch.php');
require_once($GLOBALS['g_campsiteDir'] . '/classes/Log.php');

$translator = \Zend_Registry::get('container')->getService('translator');

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
        $json = '{"first_name":"","last_name":"","biography":""}';
        $language = Input::Get("language", "int", 0);
        $bioObj = new AuthorBiography($author->getId(), $language);
        if ($bioObj->exists()) {
            $json = '{"first_name":"'.addslashes($bioObj->getFirstName())
                .'","last_name":"'.addslashes($bioObj->getLastName())
                .'","biography":"'.addslashes($bioObj->getBiography())
                .'"}';
        }
        echo $json;
        exit();
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
<div class="tabs authors ui-tabs ui-widget ui-widget-content ui-corner-all block-shadow author-details">
  <ul>
    <li><a href="#generalContainer"><?php echo $translator->trans('General', array(), 'authors'); ?></a></li>
    <li><a href="#biographyContainer"><?php echo $translator->trans('Biography', array(), 'authors'); ?></a></li>
    <?php if ( isset($author) && is_object($author)) { ?>
    <li><a href="#contentContainer"><?php echo $translator->trans('Content', array(), 'authors'); ?></a></li>
    <?php } ?>
  </ul>
  <div id="generalContainer">
    <div class="space-box"></div>
    <fieldset class="frame">
      <ul>
        <li>
          <label><?php echo $translator->trans('First name', array(), 'authors'); ?>:</label>
          <input type="text" name="first_name" class="input_text" style="width: 360px;" size="41" value="<?php echo $first_name; ?>" />
        </li>
        <li>
          <label><?php echo $translator->trans('Last name', array(), 'authors'); ?>:</label>
          <input type="text" name="last_name" class="input_text" style="width: 360px;" size="41" spellcheck="false" value="<?php echo $last_name; ?>" />
        </li>
        <li>
          <label><?php echo $translator->trans('Aliases', array(), 'authors'); ?>:</label>
          <div id="aliases" class="aliasContainer clearfix">
          <?php
          $count = 0;
          if (isset($aliases) && is_array($aliases)) {
              foreach ($aliases as $alias) {
                  $count++;
                  echo '<div class="authorAliasItem">';
                  echo '<input type="text" name="alias[]" class="input_text" size="41" spellcheck="false" style="width:325px;" value="' . $alias->getName() . '" />';
                  echo '<a class="ui-state-default icon-button no-text" href="?id=' . $author->getId() . '&del_id_alias=' . $alias->getId() . '" onclick="return deleteAuthorAlias(' . $alias->getId() . ',' . $author->getId() . ')"><span
                      class="ui-icon ui-icon-closethick"></span></a>';
                  echo '</div>';
              }
          }
          ?>
            <div class="authorAliasItem">
              <input type="text" name="alias[]" value="" class="input_text" size="41" spellcheck="false" style="width:325px;" />
              <a class="ui-state-default icon-button no-text" href="#"><span class="ui-icon ui-icon-closethick"></span></a>
            </div>
          </div>
          <span onclick="addAlias();"><a href="#" class="addButton"></a></span>
        </li>
        <li>
          <span id="types">
            <label><?php echo $translator->trans('Type'); ?>:</label>
            <select name="type[]" class="input_select2" onchange="" style="width:362px;height:100%" multiple="multiple">
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
    </fieldset>
    <fieldset class="frame">
      <h3><?php echo $translator->trans('Contacts', array(), 'authors'); ?></h3>
      <ul>
        <li>
          <label><?php echo $translator->trans('Skype', array(), 'authors'); ?>:</label>
          <input type="text" name="skype" style="width: 360px;" class="input_text" size="41" value="<?php echo $skype; ?>" />
        </li>
        <li>
          <label><?php echo $translator->trans('Jabber', array(), 'authors'); ?>:</label>
          <input type="text" name="jabber" style="width: 360px;" class="input_text" size="41" spellcheck="false" value="<?php echo $jabber; ?>" />
        </li>
        <li>
          <label><?php echo $translator->trans('AIM', array(), 'authors'); ?>:</label>
          <input type="text" name="aim" style="width: 360px;" class="input_text" size="41" spellcheck="false" value="<?php echo $aim; ?>" />
        </li>
        <li>
          <label><?php echo $translator->trans('Email', array(), 'authors'); ?>:</label>
          <input type="text" name="email" style="width: 360px;" class="input_text" size="41" spellcheck="false" value="<?php echo $email; ?>" />
        </li>
        <li>
          <label>&nbsp;</label>
          <input type="reset" name="reset" id="reset" value="<?php echo $translator->trans('Reset', array(), 'authors'); ?>" class="button" onclick="" />
        </li>
      </ul>
    </fieldset>
  </div>
  <div id="biographyContainer">
    <div class="space-box"></div>
    <fieldset class="frame">
      <ul>
        <li>
          <label class="smaller"><?php echo $translator->trans('Languages'); ?>:</label>
          <select name="lang" class="input_select" style="width:160px;" id="lang" onchange="changeBio(<?php echo $id ?>)">
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
          <label class="smaller"><?php echo $translator->trans('Translate from', array(), 'authors'); ?>:</label>
          <select name="translate" id="lang_trans" class="input_select" style="width:120px;" onchange="changeTranslation(<?php echo $id ?>)">
          <?php echo $combo ?>
          </select>
        </li>
      </ul>
    </fieldset>
    <fieldset class="frame">
      <div class="authorThumb">
      <?php
      if ( isset($author) && is_object($author) && is_numeric($author->getImage())) {
          $image = new Image($author->getImage());
          echo '<img src="' . $image->getThumbnailUrl() . '"/>';
      } else {
          echo '<img src="" width="100" height="120" alt="" />';
      }
      ?>
      </div>
      <ul class="nameList">
        <li>
          <label><?php echo $translator->trans('First name', array(), 'authors'); ?>:</label>
          <input type="text" name="lang_first_name" id="lang_first_name" maxlength="35" class="input_text" value="<?php echo $lang_first_name; ?>" emsg="<?php echo $translator->trans('Please enter the first name', array(), 'authors'); ?>" style="width:170px;" />
        </li>
        <li>
          <label><?php echo $translator->trans('Last name', array(), 'authors'); ?>:</label>
          <input type="text" name="lang_last_name" id="lang_last_name" maxlength="35" class="input_text" value="<?php echo $lang_last_name; ?>" emsg="<?php echo $translator->trans('Please enter the last name', array(), 'authors'); ?>" style="width:170px;" />
        </li>
        <li>&nbsp;</li>
        <li>
          <input type="file" name="file" size="32" class="input_file" />
        </li>
      </ul>
    </fieldset>
    <fieldset class="plain">
      <div class="textHolder">
        <textarea name="langbio" id="transArea" rows="20" readonly="readonly" class="input_text_area"><?php echo $biography; ?></textarea>
      </div>
      <div class="textHolder omega">
        <textarea name="txt_biography" id="txt_biography" rows="30" class="tinymce input_text_area"><?php echo $biography; ?></textarea>
      </div>
    </fieldset>
    <div class="clear"></div>
  </div>

  <?php
  if ( isset($author) && is_object($author)) {
  ?>
  <div id="contentContainer">
    <div class="space-box"></div>
    <fieldset class="frame">
      <ul>
    <?php
    $authoringList = ArticleAuthor::GetArticlesByAuthor($author->getId());
    $authoringCount = sizeof($authoringList);
    if ($authoringCount > 0) {
    ?>
        <li>
          <label><?php echo $translator->trans('Total articles', array(), 'authors'); ?>:</label> <span><?php echo $authoringCount; ?></span></li>
    <?php
        foreach ($authoringList as $authoringItem) {
            $articleUrl = $Campsite['WEBSITE_URL'] . '/' . $ADMIN . '/articles/edit.php';
            $articleUrl .= '?f_publication_id=' . $authoringItem['article']->getPublicationId()
                . '&f_issue_number=' . $authoringItem['article']->getIssueNumber()
                . '&f_section_number=' . $authoringItem['article']->getSectionNumber()
                . '&f_article_number=' . $authoringItem['article']->getArticleNumber()
                . '&f_language_id=' . $authoringItem['article']->getLanguageId()
                . '&f_language_selected=' . $authoringItem['article']->getLanguageId();
    ?>
        <li>
          <label><?php echo $authoringItem['type']->getName(); ?></label>
          <a href="<?php echo $articleUrl; ?>" style="font-size:0.8em;"><?php echo $authoringItem['article']->getName(); ?></a>
        </li>
    <?php
        }
    } else { ?>
        <li>
          <label><?php echo $translator->trans('No records found', array(), 'authors'); ?></label>
        </li>
    <?php } ?>
      </ul>
    </fieldset>
  </div>
  <?php } ?>

  <fieldset class="frame" style="margin-bottom:0;">
    <ul>
      <li>
        <input type="submit" name="save" id="save" value="<?php echo $translator->trans('Save All', array(), 'authors'); ?>" class="save-button right-floated" />
      </li>
    </ul>
  </fieldset>
</div>

<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/users/load_tinymce.php");
$languageObj = new Language( isset($Language) ? $Language : 1 );
//if (!is_object($languageObj)) {
//    $languageObj = new Language(1);
//}

$editorLanguage = !empty($_COOKIE['TOL_Language']) ? $_COOKIE['TOL_Language'] : $languageObj->getCode();
editor_load_tinymce('txt_biography', $g_user, 0, $editorLanguage, 'authorbiography');
?>
</form>
