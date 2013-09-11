      <?php
      $translator = \Zend_Registry::get('container')->getService('translator');
      function drawCombo($id, $pos) {
          $combo = '<select name="f_article_author_type[]" id="article_author_type' . $pos . '"
              class="input_select aaselect" onchange="" style="width:130px;height:100%;margin-bottom:2px;float:none">';
          $combo .= drawComboContent($id);
          $combo .= '    </select>   ';
          return $combo;
      }

      function drawComboContent($id = 0) {
          $combo = '';
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
          $('#authorContainer ul').append('<li id="author_li' + rnumber + '"><div class="item_alt"><div><span id="author_type' + rnumber + '" style="margin-top:1px; margin-left: 20px;"><select name="f_article_author_type[]" id="article_author_typexx" class="input_select aaselect" style="width:130px;height:100%;margin-bottom:2px;float:none"><?php echo drawComboContent(); ?></select></span><input type="text" name="f_article_author[]" id="f_article_authorxx" size="45" class="input_text_alt aauthor" style="margin-left: 20px;" value="" autocomplete="off" /><a class="icon delete" href="#" id="removeauthorxx" onclick="deleteAuthor(' + rnumber + ');"><span class="ui-icon ui-icon-closethick"></span></a></div></div></li>');
      }

      function deleteAuthor(id, empty){
          $('#f_article_author' + id).remove();
          $('#article_author_type' + id).remove();
          $('#removeauthor' + id).remove();
          $('#author_li' + id).remove();
          $('#article-main').addClass('changed');
      }
      
      $(document).ready(function() {
          var orderChanges = {};
          $('.sortable').sortable({
                revert: 100,
                distance: 5,
                start: function(event, ui) {
                    $(this).data( 'is-sorting', true );
                    sorting = true;
                    //ui.item.addClass('move');
                },
                stop: function(event, ui) {
                    $(this).data( 'is-sorting', false );
                    sorting = false;
                    //ui.item.removeClass('move');
                },
                update: function(event, ui) {
                    $('#article-main').addClass('changed');
                },
            });
      }); // /document.ready
      
      
      </script>
      <?php
      // Get the list of authors
      $authors = ArticleAuthor::GetAuthorsByArticle($articleObj->getArticleNumber(), $articleObj->getLanguageId());
      if ($inEditMode) {
      ?>
      <div id="authorAutoComplete">
      <ul>
        <li>
          <label><?php echo $translator->trans('Authors', array(), 'articles'); ?></label>
          <div id="authorContainer">
          <ul class="tree sortable">
      <?php
          if (!empty($authors)) {
              $i = 0;
              foreach ((array) $authors as $author) {
      ?>
            <li id="<?php p('author_li'.$i); ?>">
              <div class="item_alt">
                <div>
                    <span id="<?php p('author_type'.$i); ?>" style="margin-top:1px; margin-left: 20px;"><?php echo drawCombo($author->getAuthorType()->getId(), $i); ?></span>
                    <input type="text" name="f_article_author[]" id="f_article_author<?php echo $i; ?>" size="45" class="input_text_alt aauthor" style="margin-left: 20px;" value="<?php print htmlspecialchars($author->getName()); ?>" autocomplete="off" />
                    <a class="icon delete" href="#" id="removeauthor<?php echo $i;?>" onclick="deleteAuthor('<?php echo $i; ?>');">
                        <span class="ui-icon ui-icon-closethick"></span>
                    </a>
                </div>
              </div>
            </li>
      <?php
                  $i++;
              }
          }
      ?>
            <li id="author_lixx">
              <div class="item_alt">
                <div>
                    <span id="author_typexx" style="margin-top:1px; margin-left: 20px;">
                        <select name="f_article_author_type[]" id="article_author_typexx" class="input_select aaselect" style="width:130px;height:100%;margin-bottom:2px;float:none">
                        <?php echo drawComboContent(); ?>
                        </select>
                    </span>
                    
                    <input type="text" name="f_article_author[]" id="f_article_authorxx" size="45" class="input_text_alt aauthor" style="margin-left: 20px;" value="" autocomplete="off" />
                    <a class="icon delete" href="#" id="removeauthorxx" onclick="deleteAuthor('xx');">
                        <span class="ui-icon ui-icon-closethick"></span>
                    </a>
                </div>
              </div>
            </li>
            
          </ul>
          </div>
        </li>
        <li>
          <ul>
            <li>
              <a class="ui-state-default icon-button left-floated"
                href="#" onclick="addAuthor();"><span
                class="ui-icon ui-icon-plusthick"></span><?php echo $translator->trans('Add another author', array(), 'articles'); ?></a>
            </li>
          </ul>
        </li>
      </ul>
      </div>
      <?php } else { ?>
      <ul>
        <li>
          <label><?php echo $translator->trans('Authors', array(), 'articles'); ?></label>
          <?php
          if (!empty($authors)) {
              foreach ((array) $authors as $author) {
          ?>
            <div class="text-container authorlist">
              <span class="author-type-label"><?php echo (!is_null($author->getAuthorType())) ? ($author->getAuthorType()->getName()) : ''; ?>:</span>
              <span class="publish_date"><?php p($author->getName()); ?></span>
            </div>
          <?php
              }
          }
          ?>
        </li>
      </ul>
      <?php } ?>
