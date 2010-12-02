
<!-- CSS file (default YUI Sam Skin) -->
<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/yui/build/autocomplete/assets/skins/sam/autocomplete.css" />

<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/yui/build/button/assets/skins/sam/button.css" />

<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/yui/build/container/assets/container.css" />

<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/admin/articles/yui-assets/styles.css" />

<!-- It looks that OpenLayers library may have problems when loaded slowly, thus trying to preload it herein -->
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/geocoding/openlayers/OpenLayers.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/geocoding/location_chooser.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/geocoding/geonames/search.js"></script>


<?php
// If the article is locked.
if ($articleObj->userCanModify($g_user) && $locked && ($f_edit_mode == "edit")) {
	$saveButtonNames = array();
?>
<p>
<table border="0" cellspacing="0" cellpadding="6" class="table_input">
<tr>
  <td colspan="2">
    <b><?php putGS("Article is locked"); ?></b>
    <hr noshade size="1" color="black" />
  </td>
</tr>
<tr>
  <td colspan="2" align="center">
    <blockquote>
    <?php
        $timeDiff = camp_time_diff_str($articleObj->getLockTime());
        if ($timeDiff['hours'] > 0) {
            putGS('The article has been locked by $1 ($2) $3 hour(s) and $4 minute(s) ago.',
                  '<B>'.htmlspecialchars($lockUserObj->getRealName()),
                  htmlspecialchars($lockUserObj->getUserName()).'</B>',
                  $timeDiff['hours'], $timeDiff['minutes']);
        } else {
            putGS('The article has been locked by $1 ($2) $3 minute(s) ago.',
                  '<B>'.htmlspecialchars($lockUserObj->getRealName()),
                  htmlspecialchars($lockUserObj->getUserName()).'</B>',
                  $timeDiff['minutes']);
        }
    ?>
    <br/>
    </blockquote>
  </td>
</tr>
<tr>
  <td colspan="2">
    <div align="center">
      <input type="button" name="Yes" value="<?php putGS('Unlock'); ?>" class="button" onclick="location.href='<?php echo camp_html_article_url($articleObj, $f_language_id, "do_unlock.php", '', null, true); ?>'" />
      <input type="button" name="Yes" value="<?php putGS('View'); ?>" class="button" onclick="location.href='<?php echo camp_html_article_url($articleObj, $f_language_id, "edit.php", "", "&f_edit_mode=view"); ?>'" />
      <input type="button" name="No" value="<?php putGS('Cancel'); ?>" class="button" onclick="location.href='/<?php echo $ADMIN; ?>/articles/?f_publication_id=<?php p($f_publication_id); ?>&f_issue_number=<?php p($f_issue_number); ?>&f_language_id=<?php p($f_language_id); ?>&f_section_number=<?php p($f_section_number); ?>'" />
    </div>
  </td>
</tr>
</table>
<p>
<?php
   return;
}
?>

<?php if ($f_publication_id > 0) { ?>
<table border="0" cellspacing="0" cellpadding="1" class="action_buttons" style="padding-top: 5px;">
<tr>
  <td><a href="<?php echo "/$ADMIN/articles/?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_language_id=$f_language_id"; ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" border="0" /></a></td>
  <td><a href="<?php echo "/$ADMIN/articles/?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_language_id=$f_language_id"; ?>"><b><?php  putGS("Article List"); ?></b></a></td>

  <?php if ($g_user->hasPermission('AddArticle')) { ?>
  <td style="padding-left: 20px;"><a href="add.php?f_publication_id=<?php p($f_publication_id); ?>&f_issue_number=<?php p($f_issue_number); ?>&f_section_number=<?php p($f_section_number); ?>&f_language_id=<?php p($f_language_id); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" border="0" /></a></td>
  <td><a href="add.php?f_publication_id=<?php p($f_publication_id); ?>&f_issue_number=<?php p($f_issue_number); ?>&f_section_number=<?php p($f_section_number); ?>&f_language_id=<?php p($f_language_id); ?>"><b><?php putGS("Add new article"); ?></b></a></td>
<?php } ?>
</tr>
</table>
<?php } ?>

<?php camp_html_display_msgs("0.25em", "0.25em"); ?>

<div id="yui-connection-container"></div>
<div id="yui-connection-message"></div>
<div id="yui-connection-error"></div>

<table border="0" cellspacing="1" cellpadding="0" class="table_input" width="900px" style="margin-top: 5px;">
<tr>
  <td width="700px" style="border-bottom: 1px solid #8baed1;" colspan="2">
    <!-- for the left side of the article edit screen -->
    <table cellpadding="0" cellspacing="0">
    <tr>
      <td width="100%" valign="middle">
        <!-- BEGIN the article control bar -->
        <form name="article_actions" action="do_article_action.php" method="POST">
        <?php echo SecurityToken::FormParameter(); ?>
        <input type="hidden" name="f_publication_id" id="f_publication_id" value="<?php  p($f_publication_id); ?>" />
        <input type="hidden" name="f_issue_number" id="f_issue_number" value="<?php  p($f_issue_number); ?>" />
        <input type="hidden" name="f_section_number" id="f_section_number" value="<?php  p($f_section_number); ?>" />
        <input type="hidden" name="f_language_id" id="f_language_id" value="<?php  p($f_language_id); ?>" />
        <input type="hidden" name="f_language_selected" id="f_language_selected" value="<?php  p($f_language_selected); ?>" />
        <input type="hidden" name="f_article_number" id="f_article_number" value="<?php  p($f_article_number); ?>" />
        <table border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td style="padding-left: 1em;">
          <script type="text/javascript">
          function action_selected(dropdownElement) {
              // Get the index of the "delete" option.
              deleteOptionIndex = -1;
              for (var index = 0; index < dropdownElement.options.length; index++) {
                  if (dropdownElement.options[index].value == "delete") {
                      deleteOptionIndex = index;
                  }
              }

              // if the user has selected the "delete" option
              if (dropdownElement.selectedIndex == deleteOptionIndex) {
                  ok = confirm("<?php putGS("Are you sure you want to delete this article?"); ?>");
                  if (!ok) {
                      dropdownElement.options[0].selected = true;
                      return;
                  }
              }

              // do the action if it isnt the first or second option
              if ((dropdownElement.selectedIndex != 0) &&  (dropdownElement.selectedIndex != 1)) {
                  dropdownElement.form.submit();
              }
          }
          </script>
            <select name="f_action" class="input_select" onchange="action_selected(this);">
              <option value=""><?php putGS("Actions"); ?>...</option>
              <option value="">-----------</option>
              <?php if ($articleObj->userCanModify($g_user) && $articleObj->isLocked()) { ?>
              <option value="unlock"><?php putGS("Unlock"); ?></option>
              <?php } ?>

              <?php if ($g_user->hasPermission('DeleteArticle')) { ?>
              <option value="delete"><?php putGS("Delete"); ?></option>
              <?php } ?>

              <?php if ($g_user->hasPermission('AddArticle')) { ?>
              <option value="copy"><?php putGS("Duplicate"); ?></option>
              <?php } ?>

              <?php if ($g_user->hasPermission('TranslateArticle')) { ?>
              <option value="translate"><?php putGS("Translate"); ?></option>
              <?php } ?>

              <?php if ($g_user->hasPermission('MoveArticle')) { ?>
              <option value="move"><?php putGS("Move"); ?></option>
              <?php } ?>
            </select>
          </td>

          <!-- BEGIN Workflow -->
          <td style="padding-left: 1em;">
          <?php
          // Show a different menu depending on the rights of the user.
          if ($g_user->hasPermission("Publish")) { ?>
            <select name="f_action_workflow" class="input_select" onchange="this.form.submit();">
            <?php
            if (isset($issueObj) && $issueObj->isPublished()) {
                camp_html_select_option("Y", $articleObj->getWorkflowStatus(), getGS("Status") . ': ' . getGS("Published"));
            } else {
                camp_html_select_option("M", $articleObj->getWorkflowStatus(), getGS("Status") . ': ' . getGS("Publish with issue"));
            }
            camp_html_select_option("S", $articleObj->getWorkflowStatus(), getGS("Status") . ': ' . getGS("Submitted"));
            camp_html_select_option("N", $articleObj->getWorkflowStatus(), getGS("Status") . ': ' . getGS("New"));
            ?>
            </select>
          <?php } elseif ($articleObj->userCanModify($g_user) && ($articleObj->getWorkflowStatus() != 'Y')) { ?>
            <select name="f_action_workflow" class="input_select" onchange="this.form.submit();">
              <?php
                  camp_html_select_option("S", $articleObj->getWorkflowStatus(), getGS("Status") . ': ' . getGS("Submitted"));
                  camp_html_select_option("N", $articleObj->getWorkflowStatus(), getGS("Status") . ': ' . getGS("New"));
              ?>
            </select>
          <?php } else {
              switch ($articleObj->getWorkflowStatus()) {
              case 'Y':
                  echo getGS("Status") . ': ' . getGS("Published");
                  break;
              case 'M':
                  echo getGS("Status") . ': ' . getGS("Publish with issue");
                  break;
              case 'S':
                  echo getGS("Status") . ': ' . getGS("Submitted");
                  break;
              case 'N':
                  echo getGS("Status") . ': ' . getGS("New");
                  break;
              }
          }
          if (count($articleEvents) > 0) {
          ?>
            <img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/automatic_publishing.png" alt="<?php putGS("Scheduled Publishing"); ?>" title="<?php  putGS("Scheduled Publishing"); ?>" border="0" width="22" height="22" align="middle" style="padding-bottom: 1px;" />
          <?php
          }
          ?>
          </td>
          <!-- END Workflow -->

          <td style="padding-left: 1em;">
            <table border="0" cellspacing="0" cellpadding="3">
            <tr>
              <td><?php  putGS('Language'); ?>:</td>
              <td>
              <?php
              if (count($articleLanguages) > 1) {
                  $languageUrl = "edit.php?f_publication_id=$f_publication_id"
                      ."&f_issue_number=$f_issue_number"
                      ."&f_section_number=$f_section_number"
                      ."&f_article_number=$f_article_number"
                      ."&f_language_id=$f_language_id"
                      ."&f_language_selected=";
              ?>
                <select name="f_language_selected" class="input_select" onchange="dest = '<?php p($languageUrl); ?>'+this.options[this.selectedIndex].value; location.href=dest;">
                <?php
                foreach ($articleLanguages as $articleLanguage) {
                    camp_html_select_option($articleLanguage->getLanguageId(), $f_language_selected, htmlspecialchars($articleLanguage->getNativeName()));
                }
                ?>
                </select>
              <?php } else {
                  $articleLanguage = camp_array_peek($articleLanguages);
                  echo '<b>'.htmlspecialchars($articleLanguage->getNativeName()).'</b>';
              }
              ?>
              </td>
            </tr>
            </table>
          </td>
        </tr>
        </table>
        </form>
        <!-- END the article control bar -->
      </td>

            <?php
            if ($articleObj->userCanModify($g_user)) {
            $switchModeUrl = camp_html_article_url($articleObj, $f_language_id, "edit.php")
                ."&f_edit_mode=".( ($f_edit_mode =="edit") ? "view" : "edit");
            ?>
            <td align="right" style="padding-top: 1px;" valign="top">
                 <table cellpadding="0" cellspacing="0" border="0">
                 <tr><td>
                 <input type="button" name="edit" value="<?php putGS("Edit"); ?>" <?php if ($f_edit_mode == "edit") {?> disabled class="button_disabled" <?php } else { ?> onclick="location.href='<?php p($switchModeUrl); ?>';" class="button" <?php } ?> />
                 </td>

                 <td style="padding-left: 5px; padding-right: 10px;">
                 <input type="button" name="edit" value="<?php putGS("View"); ?>" <?php if ($f_edit_mode == "view") {?> disabled class="button_disabled" <?php } else { ?> onclick="location.href='<?php p($switchModeUrl); ?>';" class="button" <?php } ?> />
                 </td>

                 <td nowrap>
                   <div id="yui-saved-box">
                 <div id="yui-connection-saved"></div>
                 <div id="yui-saved">
                                 <script>
                    var dateTime = '<?php if ($savedToday) { p(date("H:i:s", $lastModified)); } else { p(date("Y-m-d H:i", $lastModified)); } ?>';
                        if (document.getElementById('yui-connection-saved').value == undefined) {
                        document.write('<?php putGS("Saved:"); ?> ' + dateTime);
                    }
                 </script>
                                 </div>
                               </div>
                 </td>
                 </tr></table>
            </td>
            <?php } ?>
        </tr>
        </table>
    </td>
</tr>

<tr>
    <td valign="top">
    <!-- BEGIN article content -->
    <form name="article_edit" action="do_edit.php" method="POST" id="mainForm">
    <?php echo SecurityToken::FormParameter(); ?>
    <fieldset id="pushbuttonsfrommarkup" class=" yui-skin-sam">
    <input type="hidden" name="f_publication_id" value="<?php  p($f_publication_id); ?>" />
    <input type="hidden" name="f_issue_number" value="<?php  p($f_issue_number); ?>" />
    <input type="hidden" name="f_section_number" value="<?php  p($f_section_number); ?>" />
    <input type="hidden" name="f_language_id" value="<?php  p($f_language_id); ?>" />
    <input type="hidden" name="f_language_selected" value="<?php  p($f_language_selected); ?>" />
    <input type="hidden" name="f_article_number" value="<?php  p($f_article_number); ?>" />
    <input type="hidden" name="f_message" id="f_message" value="" />
    <table width="100%">
    <tr>
        <td style="padding-top: 3px;">
            <?php if ($f_edit_mode == "edit") { ?>
            <table width="100%" style="border-bottom: 1px solid #8baed1; padding: 0px;">
            <tr>
                <td align="center">
                    <?php if ($f_publication_id > 0) { ?>
                    <!-- Preview Link -->
                    <input type="submit" name="preview" value="<?php putGS('Preview'); ?>" class="button" onclick="window.open('/<?php echo $ADMIN; ?>/articles/preview.php?f_publication_id=<?php p($f_publication_id); ?>&amp;f_issue_number=<?php p($f_issue_number); ?>&amp;f_section_number=<?php p($f_section_number); ?>&amp;f_article_number=<?php p($f_article_number); ?>&amp;f_language_id=<?php p($f_language_id); ?>&amp;f_language_selected=<?php p($f_language_selected); ?>', 'fpreview', 'resizable=yes, menubar=no, toolbar=no, width=680, height=560'); return false">
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <?php } ?>
                    <input type="button" name="save" id="save" value="<?php putGS('Save All'); ?>" class="button" onClick="makeRequest('all');" />
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="submit" name="save_and_close" id="save_and_close" value="<?php putGS('Save and Close'); ?>" class="button" />
                </td>
            </tr>
            </table>
            <?php } ?>
            <table width="100%" style="border-bottom: 1px solid #8baed1; padding-bottom: 3px;">
            <tr>
                      <td align="left" valign="top" style="padding-right: 5px;">
                      <?php if ($f_edit_mode == "edit") { ?>
                            <input type="button" id="save_f_article_title" name="button4" value="<?php putGS('Saved'); ?>">
              <?php } ?>
                          </td>
                          <td align="right" valign="top"><b><?php  putGS("Name"); ?>:</b></td>
                          <td align="left" valign="top" colspan="2">
                          <?php if ($f_edit_mode == "edit") { ?>
                            <input type="text" name="f_article_title" id="f_article_title" size="55" class="input_text" value="<?php  print htmlspecialchars($articleObj->getTitle()); ?>" onkeyup="buttonEnable('save_f_article_title');" <?php print $spellcheck ?> />
                          <?php } else {
                              print wordwrap(htmlspecialchars($articleObj->getTitle()), 60, "<br>");
                                }
                          ?>
                          </td>
            </tr>
            <tr>
                <td align="left" valign="top" style="padding-right: 5px;">
                <?php if ($f_edit_mode == "edit") { ?>
                    <input type="button" id="save_f_article_author" name="button5" value="<?php putGS('Saved'); ?>">
                <?php } ?>
                </td>
                <script language="Javascript">
                      function  addAuthor(){
                          var rnumber=Math.floor(Math.random()*9876)
                          $('#author_type').append('<select onchange="buttonEnable(\'save_f_article_author\');" name="f_article_author_type[]" id="article_author_type' +rnumber +  '" class="input_select2 aauthor aaselect" onchange="" style="width:130px;height:100%;float:none"><?php echo drawComboContent(); ?></select>');
                          $('#authorContainer').append('<input type="text" style="width:280px" name="f_article_author[]" id="f_article_author' + rnumber + '" size="45" class="input_text aauthor" value="" onkeyup="buttonEnable(\'save_f_article_author\');" />');
                          $('#authorContainer').append('<img border="0" src="./../../css/unlink.png" id="removeauthor' + rnumber + '" onclick="deleteAuthor(\'' + rnumber + '\');" />');

                      }
                      function deleteAuthor(id, empty){
                          $('#f_article_author' + id).remove();
                          $('#article_author_type' + id).remove();
                          $('#removeauthor' + id).remove();
                          buttonEnable('save_f_article_author');
                       }
                </script>
                <?php
                function drawCombo($types, $id, $pos){
                    $combo='<select  onchange="buttonEnable(\'save_f_article_author\');" name="f_article_author_type[]" id="article_author_type' . $pos . '" class="input_select2 aauthor aaselect" onchange="" style="width:130px;height:100%;float:none">';
                    $combo .= drawComboContent($id);
                    $combo .='    </select>    ';
                    return $combo;
                }

                function drawComboContent($id=0){
                    $types = (array) Author::getTypes();
                    foreach ($types as $xtype){
                                  $combo .=  '<option value="' . $xtype['id'] . '"';
                                  if ($id==$xtype['id']) $combo.= ' selected="selected" ';
                                  $combo.='>' . $xtype['type'] . '</option>';
                                }
                                return $combo;
                }


                    $types =Author::getTypes();
                    $authors = ArticleAuthor::getArticleAuthorList($articleObj->getArticleNumber(), $articleObj->getLanguageId());

                    if (!empty($authors))
                    {
                        $i=0;
                        $author_list=  array();
                        $types_list= array();
                        foreach ($authors as $author)
                        { ob_start();?><div><?php //echo $combo ?>
                         <input type="text" name="f_article_author[]" style="width:280px" id="f_article_author<?php echo $i; ?>" size="45" class="input_text aauthor"  value="<?php print  htmlspecialchars($author['first_name']); echo " "; print  htmlspecialchars($author['last_name']); ?>" onkeyup="buttonEnable('save_f_article_author');" />
                         <img border="0" src="./../../css/unlink.png" id="removeauthor<?php echo $i;?>" onclick="deleteAuthor('<?php echo $i;?>');">
                            </div>
                          <?php  $author_list[] = ob_get_clean();
                          $types_list[] = $author['fk_type_id'];
                          $i++;
                        }
                    }
                ?>

                <td align="right" valign="top" id="author_type">
                    <?php $i=0;
                    foreach ((array) $types_list as $type){
                        echo "<div id=\"author_type$i\" style=\"margin-top:1px\">" . drawCombo($types,$type,$i) . "</div>";
                        $i++;
                    }
                    ?>
                <select name="article_author_type[]" id="article_author_typexx" class="input_select2 aauthor aaselect" onchange="buttonEnable('save_f_article_author');" style="width:130px;height:100%;float:none">
                    <?php echo drawComboContent(); ?></select>
                </td>
                <td align="left" valign="top" class="yui-skin-sam">
                    <?php if ($f_edit_mode == "edit") {  ?>
                    <div id="authorAutoComplete">
                    <?php
                        foreach ((array) $author_list as $author){
                        echo $author;
                    }?>


                        <div id="authorContainer">
                            <input type="text" style="width:280px" name="f_article_author[]" id="f_article_authorxx" size="45" class="input_text aauthor"  onkeyup="buttonEnable('save_f_article_author');" /><img border="0" src="./../../css/unlink.png" id="removeauthorxx" onclick="deleteAuthor('xx');">
                        </div>
                        <img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" border="0" onclick="addAuthor()">
                    </div>
                    <?php } else {
                        $ath='';
                        $authors = ArticleAuthor::getArticleAuthorList($articleObj->getArticleNumber(), $articleObj->getLanguageId());
                        foreach ($authors as $author)
                        {
                            if (strlen($ath)>0) $ath.=", ";
                            $ath .=$author['first_name'] . ' ' . $author['last_name'];
                        }
                            print wordwrap(htmlspecialchars($ath), 60, "<br>");
                          }
                    ?>
                </td>
                <td align="right" valign="top" style="padding-right: 0.5em;"><b><?php  putGS("Created by"); ?>:</b> <?php p(htmlspecialchars($articleCreator->getRealName())); ?></td>
            </tr>
            </table>

            <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td align="left" valign="top">
                    <!-- Left-hand column underneath article title -->
                    <table>
                    <tr>
                        <td align="right" valign="top" style="padding-left: 1em;"><b><?php putGS("Reads"); ?>:</b></td>
                        <td align="left" valign="top">
                        <?php
                          if ($articleObj->isPublished()) {
                              $requestObject = new RequestObject($articleObj->getProperty('object_id'));
                              if ($requestObject->exists()) {
                                  echo $requestObject->getRequestCount();
                              } else {
                                  echo "0";
                              }
                          } else {
                              putGS("N/A");
                          }
                        ?>
                        </td>
                    </tr>

                    <!-- Type -->
                    <tr>
                        <td align="right" valign="top" style="padding-left: 1em;"><b><?php  putGS("Type"); ?>:</b></td>
                        <td align="left" valign="top">
                    <?php print htmlspecialchars($articleType->getDisplayName()); ?>
                        </td>
                    </tr>

                    <!-- Number -->
                    <tr>
                        <td align="right" valign="top" nowrap><b><?php putGS("Number"); ?>:</b></td>
                        <td align="left" valign="top"  style="padding-top: 2px; padding-left: 4px;">
                            <?php
                            p($articleObj->getArticleNumber());
                            if (isset($publicationObj) && $publicationObj->getUrlTypeId() == 2 && $articleObj->isPublished()) {
                                $url = ShortURL::GetURL($publicationObj->getPublicationId(), $articleObj->getLanguageId(), null, null, $articleObj->getArticleNumber());
                                if (PEAR::isError($url)) {
                                    echo $url->getMessage();
                                } else {
                                    echo '&nbsp;(<a href="' . $url . '">' . getGS("Link to public page") . '</a>)';
                                }
                            }
                            ?></td>
                    </tr>

                    <!-- Creation Date -->
                    <tr>
                        <td align="right" valign="top" style="padding-left: 1em;"><b><nobr><?php  putGS("Creation date"); ?>:</nobr></b></td>
                        <td align="left" valign="top" nowrap>
                            <?php if ($f_edit_mode == "edit") { ?>
                            <table cellpadding="0" cellspacing="2"><tr>
                                <td><span id="show_c_date"></span></td>
                                <td valign="top" align="left">
                                    <input type="hidden" name="f_creation_date" value="<?php p($articleObj->getCreationDate()); ?>" id="f_creation_date" class="datetime" />
                                    <script type="text/javascript">
                                        $(document).ready(function() {
                                            $('input[name=f_creation_date]').change(function() {
                                                $('#show_c_date').text($(this).val());
                                            }).change();
                                        });
                                    </script>
                                </td>
                            </tr></table>
                            <?php } else { ?>
                            <?php print htmlspecialchars($articleObj->getCreationDate()); ?>
                            <?php } ?>
                        </td>
                    </tr>
                    <!-- End creation date -->

                    <tr>
                        <td align="right" valign="top" style="padding-left: 1em;"><b><?php  putGS("Publish date"); ?>:</b></td>
                        <td align="left" valign="top">
                            <?php if ($f_edit_mode == "edit" && $articleObj->isPublished()) { ?>
                            <table cellpadding="0" cellspacing="2"><tr>
                                <td><span id="show_p_date"></span></td>
                                <td valign="top" align="left">
                                    <input type="hidden" name="f_publish_date" value="<?php p($articleObj->getPublishDate()); ?>" id="f_publish_date" class="datetime" />
                                    <script type="text/javascript">
                                        $(document).ready(function() {
                                            $('input[name=f_publish_date]').change(function() {
                                                $('#show_p_date').text($(this).val());
                                            }).change();
                                        });
                                    </script>
                                </td>
                            </tr></table>
                            <?php } elseif ($articleObj->isPublished()) { ?>
                            <?php print htmlspecialchars($articleObj->getPublishDate()); ?>
                            <?php } else { ?>
                            <input type="hidden" name="f_publish_date" value="<?php p($articleObj->getPublishDate()); ?>" id="f_publish_date" />
                            <?php putGS('N/A'); } ?>
                        </td>
                    </tr>
                    </table>
                </td>

                <!-- Right-hand column underneath article title -->
                <td valign="top" align="right" style="padding-right: 2em; padding-top: 0.25em;">
                    <table border="0" cellpadding="0" cellspacing="1">

                    <!-- Show article on front page -->
                    <tr>
                        <td align="right" valign="top"><input type="CHECKBOX" name="f_on_front_page" id="f_on_front_page" class="input_checkbox" <?php  if ($articleObj->onFrontPage()) { ?> CHECKED<?php  } ?> <?php if ($f_edit_mode == "view") { ?>disabled<?php }?> /></td>
                        <td align="left" valign="top" style="padding-top: 0.1em;">
                        <?php  putGS('Show article on front page'); ?>
                        </td>
                    </tr>

                    <!-- Show article on section page -->
                    <tr>
                        <td align="right" valign="top"><input type="CHECKBOX" name="f_on_section_page" id="f_on_section_page" class="input_checkbox" <?php  if ($articleObj->onSectionPage()) { ?> CHECKED<?php  } ?> <?php if ($f_edit_mode == "view") { ?>disabled<?php }?> /></td>
                        <td align="left" valign="top"  style="padding-top: 0.1em;">
                            <?php  putGS('Show article on section page'); ?>
                        </td>
                    </tr>

                    <!-- Article viewable by public -->
                    <tr>
                        <td align="right" valign="top"><input type="CHECKBOX" name="f_is_public" id="f_is_public" class="input_checkbox" <?php  if ($articleObj->isPublic()) { ?> CHECKED<?php  } ?> <?php if ($f_edit_mode == "view") { ?>disabled<?php }?> /></td>
                        <td align="left" valign="top" style="padding-top: 0.1em;">
                            <?php putGS('Visible to non-subscribers'); ?>
                        </td>
                    </tr>

                    <!-- Comments enabled -->
                    <?php
                    if ($showCommentControls) {
                    ?>
                    <tr>
                        <td align="left" colspan="2" style="padding-top: 0.25em;">
                            <?php putGS("Comments"); ?>:
                            <select name="f_comment_status" id="f_comment_status" class="input_select" <?php if ($f_edit_mode == "view") { ?>disabled<?php } ?>>
                            <?php
                            if ($articleObj->commentsEnabled()) {
                                if ($articleObj->commentsLocked()) {
                                    $commentStatus = 'locked';
                                } else {
                                    $commentStatus = 'enabled';
                                }
                            } else {
                                $commentStatus = 'disabled';
                            }
                            camp_html_select_option('disabled', $commentStatus, getGS("Disabled"));
                            camp_html_select_option('locked', $commentStatus, getGS("Locked"));
                            camp_html_select_option('enabled', $commentStatus, getGS("Enabled"));
                            ?>
                            </select>
                        </td>
                    </tr>
                    <?php } // end if comments enabled ?>
                    </table>
                </td>
            </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style="border-top: 1px solid #8baed1; padding-top: 3px;">
            <table>
            <tr>
                <td align="left" style="padding-right: 5px;">
                <?php if ($f_edit_mode == "edit") { ?>
                    <input type="button" id="save_f_keywords" name="button6" value="<?php putGS('Saved'); ?>">
                <?php } ?>
                </td>
                <td align="right" ><?php  putGS("Keywords"); ?>:</td>
                <td>
                    <?php if ($f_edit_mode == "edit") { ?>
                    <input type="TEXT" name="f_keywords" id="f_keywords" value="<?php print htmlspecialchars($articleObj->getKeywords()); ?>" class="input_text" size="50" maxlength="255" onkeyup="buttonEnable('save_f_keywords');" <?php print $spellcheck ?> />
                    <?php } else {
                        print htmlspecialchars($articleObj->getKeywords());
                    }
                    ?>
                </td>
            </tr>

            <?php
            $fCustomFields = array();
                        $fCustomSwitches = array();
                        $fCustomTextareas = array();
                        $saveButtons = array();
                        $saveButtonNames = array('save_f_article_title','save_f_article_author','save_f_keywords');
            // Display the article type fields.
            foreach ($dbColumns as $dbColumn) {
                if ($dbColumn->getType() == ArticleTypeField::TYPE_TEXT) {
                    // Single line text fields
            ?>
            <tr>
                <td align="left" style="padding-right: 5px;">
                    <?php if ($f_edit_mode == "edit") {
                                    $saveButtonNames[] = 'save_' . $dbColumn->getName();
                                    ?>
                    <input type="button" id="save_<?php p($dbColumn->getName()); ?>" value="<?php putGS('Saved'); ?>">
                    <?php } ?>
                </td>
                <td align="right">
                    <?php echo htmlspecialchars($dbColumn->getDisplayName()); ?>:
                </td>
                <td>
                <?php
                if ($f_edit_mode == "edit") {
                    $fCustomFields[] = $dbColumn->getName();
                ?>
                <input name="<?php echo $dbColumn->getName(); ?>"
                       id="<?php echo $dbColumn->getName(); ?>"
                       type="TEXT"
                       value="<?php print htmlspecialchars($articleData->getProperty($dbColumn->getName())); ?>"
                       class="input_text"
                       size="50"
                       maxlength="255"
                       onkeyup="buttonEnable('save_<?php p($dbColumn->getName()); ?>');"
                       <?php print $spellcheck ?> />
                <?php } else {
                    print htmlspecialchars($articleData->getProperty($dbColumn->getName()));
                }
                ?>
                </td>
            </tr>
            <?php
            } elseif ($dbColumn->getType() == ArticleTypeField::TYPE_DATE) {
                // Date fields
                if ($articleData->getProperty($dbColumn->getName()) == "0000-00-00") {
                    $articleData->setProperty($dbColumn->getName(), "CURDATE()", true, true);
                }
            ?>
            <tr>
                <td align="left" style="padding-right: 5px;">
                    <?php if ($f_edit_mode == "edit") { ?>
                                        <input type="button" id="save_<?php p($dbColumn->getName()); ?>" value="<?php putGS('Saved'); ?>">
                    <?php } ?>
                </td>
                <td align="right">
                    <?php echo htmlspecialchars($dbColumn->getDisplayName()); ?>:
                </td>
                <td>
                <?php
                    if ($f_edit_mode == "edit") {
                        $fCustomFields[] = $dbColumn->getName();
                    $saveButtonNames[] = 'save_' . $dbColumn->getName();
                ?>
                <input name="<?php echo $dbColumn->getName(); ?>"
                           id="<?php echo $dbColumn->getName(); ?>"
                       type="TEXT"
                       value="<?php echo htmlspecialchars($articleData->getProperty($dbColumn->getName())); ?>"
                       class="input_text datepicker"
                       size="11"
                       maxlength="10"
                                           onkeyup="buttonEnable('save_<?php p($dbColumn->getName()); ?>');" />
                <?php } else { ?>
                    <span style="padding-left: 4px; padding-right: 4px; padding-top: 1px; padding-bottom: 1px; border: 1px solid #888; margin-right: 5px; background-color: #EEEEEE;"><?php echo htmlspecialchars($articleData->getProperty($dbColumn->getName())); ?></span>
                    <?php
                }
                ?>
                <?php putGS('YYYY-MM-DD'); ?>
                </td>
            </tr>
            <?php
            } elseif ($dbColumn->getType() == ArticleTypeField::TYPE_BODY) {
                // Multiline text fields
                // Transform Campsite-specific tags into editor-friendly tags.
                $text = $articleData->getProperty($dbColumn->getName());

                // Subheads
                $text = preg_replace("/<!\*\*\s*Title\s*>/i", "<span class=\"campsite_subhead\">", $text);
                $text = preg_replace("/<!\*\*\s*EndTitle\s*>/i", "</span>", $text);

                // Internal Links with targets
                $text = preg_replace("/<!\*\*\s*Link\s*Internal\s*([\w=&]*)\s*target[\s\"]*([\w_]*)[\s\"]*>/i", '<a href="/campsite/campsite_internal_link?$1" target="$2">', $text);

                // Internal Links without targets
                $text = preg_replace("/<!\*\*\s*Link\s*Internal\s*([\w=&]*)\s*>/i", '<a href="/campsite/campsite_internal_link?$1">', $text);

                // External Links (old style 2.1) with targets
                $text = preg_replace("/<!\*\*\s*Link\s*External[\s\"]*([^\s\"]*)[\s\"]*target[\s\"]*([\w_]*)[\s\"]*>/i", '<a href="$1" target="$2">', $text);

                // External Links (old style 2.1) without targets
                $text = preg_replace("/<!\*\*\s*Link\s*External[\s\"]*([^\s\"]*)[\s\"]*>/i", '<a href="$1">', $text);

                // End link
                $text = preg_replace("/<!\*\*\s*EndLink\s*>/i", "</a>", $text);
                // Images
                preg_match_all("/<!\*\*\s*Image\s*([\d]*)\s*/i",$text, $imageMatches);

                preg_match_all("/\s*sub=\"(.*?)\"/", $text, $titles);

                preg_match_all("/<!\*\*\s*Image\s*([\d]*)\s*(.*?)\s*ratio=\"(.*?)\"/", $text, $ratios);

                if (isset($imageMatches[1][0])) {
                    if (isset($titles) && sizeof($titles) > 0) {
                        for($x = 0; $x < sizeof($titles[0]); $x++) {
                            $text = preg_replace("/\s*".preg_replace('~\/~', '\/',
                            $titles[0][$x])."/", ' title="'.$titles[1][$x].'"', $text);
                        }
                    }
                    $formattingErrors = false;
                    foreach ($imageMatches[1] as $templateId) {
                        // Get the image URL
                        $articleImage = new ArticleImage($f_article_number, null, $templateId);
                        if (!$articleImage->exists()) {
                            ArticleImage::RemoveImageTagsFromArticleText($f_article_number, $templateId);
                            $formattingErrors = true;
                            continue;
                        }
                        $image = new Image($articleImage->getImageId());
                        $imageUrl = $image->getImageUrl();
                        unset($fakeTemplateId);
                        if (isset($ratios) && sizeof($ratios) > 0) {
                            $n = 0;
                            foreach ($ratios[3] as $ratio) {
                                if ($ratios[1][$n++] == $templateId) {
                                $fakeTemplateId = $templateId.'_'.$ratio;
                            }
                            }
                        }
                        if (!isset($fakeTemplateId)) {
                            $fakeTemplateId = $templateId;
                        }
                        $text = preg_replace("/<!\*\*\s*Image\s*".$templateId."\s*/i", '<img src="'.$imageUrl.'" id="'.$fakeTemplateId.'" ', $text);
                    }
                    if ($formattingErrors) {
                        ?>
<script type="text/javascript">
window.location.reload();
</script>
                        <?php
                    }
                }
            ?>
            <tr>
            <td align="right" valign="top" style="padding-top: 8px; padding-right: 5px;">
                <?php if ($f_edit_mode == "edit") {
                                $saveButtonNames[] = 'save_' . $dbColumn->getName();
                                ?>
                    <input type="button" id="save_<?php p($dbColumn->getName()); ?>" value="<?php putGS('Saved'); ?>">
                <?php } ?>
            </td>
            <td align="right" valign="top" style="padding-top: 8px;">
                <?php echo htmlspecialchars($dbColumn->getDisplayName()); ?>:
            </td>
            <td align="left" valign="top">
                <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <?php
                                if ($f_edit_mode == "edit") {
                            $textAreaId = $dbColumn->getName() . '_' . $f_article_number;
                        $fCustomTextareas[] = $textAreaId;
                        ?>
                    <td><textarea name="<?php print($textAreaId); ?>"
                                  id="<?php print($textAreaId); ?>" class="tinymce"
                                  rows="20" cols="70" onkeyup="buttonEnable('save_<?php p($dbColumn->getName()); ?>');"><?php print $text; ?></textarea>
                    </td>
                    <?php } else { ?>
                    <td align="left" style="padding: 5px; <?php if (!empty($text)) {?>border: 1px solid #888; margin-right: 5px;<?php } ?>" <?php if (!empty($text)) {?>bgcolor="#EEEEEE"<?php } ?>><?php p($text); ?></td>
                    <?php } ?>
                </tr>
                </table>
            </td>
            </tr>
            <?php
            } elseif ($dbColumn->getType() == ArticleTypeField::TYPE_TOPIC) {
                $articleTypeField = new ArticleTypeField($articleObj->getType(),
                                                         substr($dbColumn->getName(), 1));
                $rootTopicId = $articleTypeField->getTopicTypeRootElement();
                $rootTopic = new Topic($rootTopicId);
                $subtopics = Topic::GetTree($rootTopicId);
                $articleTopicId = $articleData->getProperty($dbColumn->getName());
            ?>
            <tr>
            <td align="left" style="padding-right: 5px;">
                <?php if ($f_edit_mode == "edit") {
                    $fCustomFields[] = $dbColumn->getName();
                                    $saveButtonNames[] = 'save_' . $dbColumn->getName();
                                ?>
                <input type="button" id="save_<?php p($dbColumn->getName()); ?>" value="<?php putGS('Saved'); ?>">
                <?php } ?>
            </td>
            <td align="right">
                <?php echo $articleTypeField->getDisplayName(); ?>:
            </td>
            <td>
                <?php if (count($subtopics) == 0) { ?>
                No subtopics available.
                <?php } else { ?>
                    <select class="input_select" name="<?php echo $dbColumn->getName(); ?>" id="<?php echo $dbColumn->getName(); ?>" <?php if ($f_edit_mode != "edit") { ?>disabled<?php } ?> onchange="buttonEnable('save_<?php p($dbColumn->getName()); ?>');">
                    <option value="0"></option>
                    <?php
                    $TOL_Language = camp_session_get('TOL_Language', 'en');
                    $currentLanguage = new Language($TOL_Language);
                    $currentLanguageId = $currentLanguage->getLanguageId();
                    foreach ($subtopics as $topicPath) {
                        $printTopic = array();
                        foreach ($topicPath as $topicId => $topic) {
                            $translations = $topic->getTranslations();
                            if (array_key_exists($currentLanguageId, $translations)) {
                                $currentTopic = $translations[$currentLanguageId];
                            } elseif ( ($currentLanguageId != 1) && array_key_exists(1, $translations)) {
                                $currentTopic = $translations[1];
                            } else {
                                $currentTopic = end($translations);
                            }
                            $printTopic[] = $currentTopic;
                        }
                        camp_html_select_option($topicId, $articleTopicId, implode(" / ", $printTopic));
                    }
                    ?>
                    </select>
            <?php } ?>
            </td>
            </tr>
            <?php
            } elseif ($dbColumn->getType() == ArticleTypeField::TYPE_SWITCH) {
                $checked = $articleData->getFieldValue($dbColumn->getPrintName()) ? 'checked' : '';
            ?>
            <tr>
            <td align="left" style="padding-right: 5px;">
                <?php if ($f_edit_mode == "edit") {
                    $fCustomSwitches[] = $dbColumn->getName();
                                    $saveButtonNames[] = 'save_' . $dbColumn->getName();
                                ?>
                <input type="button" id="save_<?php p($dbColumn->getName()); ?>" value="<?php putGS('Saved'); ?>">
                <?php } ?>
            </td>
            <td align="right">
                <?php echo $dbColumn->getDisplayName(); ?>:
            </td>
            <td>
            <input type="checkbox" name="<?php echo $dbColumn->getName(); ?>" id="<?php echo $dbColumn->getName(); ?>" <?php if ($f_edit_mode != "edit") { ?>disabled<?php } ?> onchange="buttonEnable('save_<?php p($dbColumn->getName()); ?>');" class="input_checkbox" <?php echo $checked; ?> />
            </td>
            </tr>
            <?php
            } elseif ($dbColumn->getType() == ArticleTypeField::TYPE_NUMERIC) {
            ?>
            <tr>
            <td align="left" style="padding-right: 5px;">
                <?php if ($f_edit_mode == "edit") {
                    $fCustomFields[] = $dbColumn->getName();
                                    $saveButtonNames[] = 'save_' . $dbColumn->getName();
                                ?>
                <input type="button" id="save_<?php p($dbColumn->getName()); ?>" value="<?php putGS('Saved'); ?>">
                <?php } ?>
            </td>
            <td align="right">
                <?php echo $dbColumn->getDisplayName(); ?>:
            </td>
            <td>
            <input type="text" class="input_text" size="20" maxlength="20" <?php print $spellcheck ?>
                   name="<?php echo $dbColumn->getName(); ?>"
                   value="<?php echo htmlspecialchars($articleData->getProperty($dbColumn->getName())); ?>"
                   id="<?php echo $dbColumn->getName(); ?>"
                   <?php if ($f_edit_mode != "edit") { ?>disabled<?php } ?>
                   onkeyup="buttonEnable('save_<?php p($dbColumn->getName()); ?>');" />
            </td>
            </tr>
            <?php
            }
        } // foreach ($dbColumns as $dbColumn)
        ?>
            </table>
        </td>
    </tr>

    <?php if ($f_edit_mode == "edit") { ?>
    <tr>
        <td colspan="2" align="center">
            <?php if ($f_publication_id > 0) { ?>
            <!-- Preview Link -->
            <input type="submit" name="preview" value="<?php putGS('Preview'); ?>" class="button" onclick="window.open('/<?php echo $ADMIN; ?>/articles/preview.php?f_publication_id=<?php p($f_publication_id); ?>&amp;f_issue_number=<?php p($f_issue_number); ?>&amp;f_section_number=<?php p($f_section_number); ?>&amp;f_article_number=<?php p($f_article_number); ?>&amp;f_language_id=<?php p($f_language_id); ?>&amp;f_language_selected=<?php p($f_language_selected); ?>', 'fpreview', 'resizable=yes, menubar=no, toolbar=no, width=680, height=560'); return false">
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?php } ?>
            <input type="button" name="save" value="<?php putGS('Save All'); ?>" class="button" onClick="makeRequest('all');" />
            &nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" name="save_and_close" value="<?php putGS('Save and Close'); ?>" class="button" />
        </td>
    </tr>
    <?php } ?>
    </table>
    </fieldset>
    </form>
    <!-- END Article Content -->
</td>
    <!-- END left side of article screen -->

    <!-- BEGIN right side of article screen -->
    <td valign="top" style="border-left: 1px solid #8baed1;" width="200px">
        <table width="100%">

        <tr><td>
             <?php require('edit_locations_box.php'); ?>
        </td></tr>

        <?php if ($articleObj->getWorkflowStatus() != 'N') { ?>
        <tr><td>
            <!-- BEGIN Scheduled Publishing table -->
            <?php require('edit_schedule_box.php'); ?>
            <!-- END Scheduled Publishing table -->
        </td></tr>
        <?php } ?>


        <?php if ($showComments) { ?>
        <tr><td>
            <!-- BEGIN Comments table -->
            <?php require('edit_comments_box.php'); ?>
            <!-- END Comments table -->
        </td></tr>
        <?php } ?>


        <tr><td>
            <!-- BEGIN Images table -->
            <?php require('edit_images_box.php'); ?>
            <!-- END Images table -->
        </td></tr>


        <tr><td>
            <!-- BEGIN Files table -->
            <?php require('edit_files_box.php'); ?>
            <!-- END Files table -->
        </td></tr>


        <tr><td>
            <!-- BEGIN Topics table -->
            <?php require('edit_topics_box.php'); ?>
            <!-- END Topics table -->
        </td></tr>


        <?php if (SystemPref::Get("UseCampcasterAudioclips") == 'Y') { ?>
        <tr><td>
            <!-- BEGIN Audioclips table -->
            <?php require('edit_audioclips_box.php'); ?>
            <!-- END Audioclips table -->
        </td></tr>
        <?php } ?>

        <?php CampPlugin::PluginAdminHooks(__FILE__); ?>

        </table>
    </td>
</tr>
</table>
<span id="dialogBox" style="display:none">
    <img src="http://us.i1.yimg.com/us.yimg.com/i/us/per/gr/gp/rel_interstitial_loading.gif" />
</span>
<script type="text/javascript">
// datepicker for date
$('.datepicker').datepicker({
    dateFormat: 'yy-mm-dd'
});
</script>
