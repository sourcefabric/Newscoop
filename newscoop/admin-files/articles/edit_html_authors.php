      <?php
      function drawCombo($id, $pos) {
          $combo = '<select name="f_article_author_type[]" id="article_author_type' . $pos . '"
              class="input_select2 aauthor aaselect" onchange="" style="width:130px;height:100%;float:none">';
          $combo .= drawComboContent($id);
          $combo .= '    </select>   ';
          return $combo;
      }

      function drawComboContent($id = 0) {
          $types = AuthorType::GetAuthorTypes();
          foreach ($types as $xtype) {
              $combo .=  '<option value="' . $xtype->getId() . '"';
              if ($id == $xtype->getId()) {
                  $combo.= ' selected="selected" ';
              }
              $combo .= '>' . $xtype->getName() . '</option>';
          }
          return $combo;
      }
      ?>
      <script type="text/javascript">
      function  addAuthor(){
          var rnumber=Math.floor(Math.random()*9876)
          $('#authorContainer ul').append('<li><label><div style="margin-top:1px"><select name="f_article_author_type[]" id="article_author_type' + rnumber +  '" class="input_select2 aauthor aaselect" onchange="" style="width:130px;height:100%;float:none"><?php echo drawComboContent(); ?></select></div></label><div class="position-helper"><input type="text" style="width:280px" name="f_article_author[]" id="f_article_author' + rnumber + '" size="45" class="input_text aauthor" value="" /><a class="ui-state-default icon-button no-text" href="#" id="removeauthor' + rnumber + '" onclick="deleteAuthor(\'' + rnumber + '\');" /><span class="ui-icon ui-icon-closethick"></span></a></div></li>');
      }

      function deleteAuthor(id, empty){
          $('#f_article_author' + id).remove();
          $('#article_author_type' + id).remove();
          $('#removeauthor' + id).remove();
          $('#article-main').addClass('changed');
      }
      </script>
      <?php
      // Get the list of authors
      $authors = ArticleAuthor::GetAuthorsByArticle($articleObj->getArticleNumber(), $articleObj->getLanguageId());
      if ($inEditMode) {
      ?>
      <div id="authorAutoComplete">
      <ul>
        <li>
          <label><?php putGS('Authors'); ?></label>
          <div id="authorContainer">
          <ul>
      <?php
          if (!empty($authors)) {
              $i = 0;
              foreach ((array) $authors as $author) {
      ?>
            <li>
              <label>
                <div id="<?php p('author_type'.$i); ?>" style="margin-top:1px"><?php echo drawCombo($author->getAuthorType()->getId(), $i); ?></div>
              </label>
              <div class="position-helper">
                 <input type="text" name="f_article_author[]" style="width:280px"
                   id="f_article_author<?php echo $i; ?>" size="45" class="input_text aauthor" value="<?php print htmlspecialchars($author->getName()); ?>" autocomplete="off" />
             <?php if ($i == 0) { ?>
                 <a class="ui-state-default icon-button no-text" href="#"
                   onclick="addAuthor();"><span
                   class="ui-icon ui-icon-plusthick"></span></a>
             <?php } else { ?> 
                 <a class="ui-state-default icon-button no-text" href="#"
                   id="removeauthor<?php echo $i;?>" onclick="deleteAuthor('<?php echo $i;?>');"><span
                   class="ui-icon ui-icon-closethick"></span></a>
              <?php } ?>
              </div>
            </li>
      <?php
                  $i++;
              }
          }
      ?>
            <li>
              <label>
                <div style="margin-top:1px">
                  <select name="f_article_author_type[]" id="article_author_typexx" class="input_select2 aauthor aaselect" style="width:130px;height:100%;float:none">
                    <?php echo drawComboContent(); ?>
                  </select>
                </div>
              </label>
              <!-- <div id="authorAutoComplete"> //-->
              <div class="position-helper">
                 <input type="text" name="f_article_author[]" style="width:280px"
                   id="f_article_authorxx" size="45" class="input_text aauthor" value="" autocomplete="off" />
                 <a class="ui-state-default icon-button no-text" href="#"
                   id="removeauthorxx" onclick="deleteAuthor('xx');"><span
                   class="ui-icon ui-icon-closethick"></span></a>
              </div>      
            </li>
          </ul>
          </div>
        </li>
      </ul>
      </div>
      <?php } else { ?>
      <ul>
        <li>
          <label><?php putGS('Authors'); ?></label>
          <?php
          if (!empty($authors)) {
              foreach ((array) $authors as $author) {
          ?>
            <div class="text-container left-floated date-published">
              <strong><?php echo (!is_null($author->getAuthorType())) ? ($author->getAuthorType()->getName()) : ''; ?>: </strong>
              <span class="publish_date"><?php p($author->getName()); ?></span>
            </div>
          <?php
              }
          }
          ?>
        </li>
      </ul>
      <?php } ?>
