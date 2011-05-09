        <div class="clear"></div>
        <div class="dataTables_wrapper">
        <form id="search-form" method="post" action="">
        <div class="clear" style="margin-top:0px;"></div>
        <div class="fg-toolbar ui-toolbar ui-widget-header ui-corner-tl ui-corner-tr ui-helper-clearfix">
          <div id="" class="dataTables_filter">
          <input id="attachement" type="hidden" name="attachement" value="<?= $attachement ? '1' : '' ?>" />
          <input id="article" type="hidden" name="article" value="<?= $article ?>" />
          <input id="paging-action" type="hidden" name="paging-action" value="" />
          <input type="text" style="width: 65%;" autocomplete="off" value="" class="input_text search" size="45" name="q">
          <input type="submit" id="search-submit" value="<? putGS('Search'); ?>" class="save-button-small">
          <input type="button" value="<? putGS('Search options'); ?>" class="button toggle-button">
          </div>
        </div>
        <fieldset class="closeable advanced-search" style="display:none">
        <ul class="form-group">
          <li class="width-1-2"><label><? putGS('Filter') ?></label>
                  <select name="filter" class="input_select">
                    <option value="" selected="selected"><? putGS('All') ?></option>
                    <option value="private"><? putGS('Private') ?></option>
                    <option value="public"><? putGS('Public') ?></option>
                    <option value="downloadable"><? putGS('Downlodable') ?></option>
                    <option value="streamable"><? putGS('Streamable') ?></option>
                  </select>
          </li>
              <li class="last"><label><? putGS('Creation date') ?><span><? putGS('select range') ?></span></label>
              <input type="text" value="" class="date" size="17" name="created_at[from]">
              <input type="text" value="" class="date" size="17" name="created_at[to]">
          </li>
          </ul>
        <ul class="form-group">
              <li class="width-1-2"><label><? putGS('Tags') ?><span><? putGS('a comma separated list of tags') ?></span></label>
              <input type="text" autocomplete="off" value="" class="input_text" size="45" name="tags">
             </li>
              <li class="width-1-2 last"><label for="type"><? putGS('Track type') ?></label>
                  <select name="types" class="input_select">
                  <? camp_html_select_option('', '', getGS('Select type'));
                      foreach ($trackTypes as $key => $value):
                        camp_html_select_option($key, '', $value);
                      endforeach ?>
                  </select>
          </li>
          </ul>
          <ul class="form-group">
              <li class="width-1-2"><label><? putGS('Genres') ?><span><? putGS('a comma separated list of genres') ?></span></label>
              <input type="text" autocomplete="off" value="" class="input_text" size="45" name="genres">
              </li>
              <li class="width-1-2 last"><label>License</label>
                    <select name="license" class="input_select">
                      <? camp_html_select_option('', '', getGS('Select license type'));
                          foreach ($licenseTypes as $key => $value):
                            camp_html_select_option($key, '', $value);
                          endforeach ?>
                    </select>
          </li>
          </ul>
              <input type="reset" class="button right-floated" value="<? putGS('Reset') ?>" name="reset">
        </fieldset>
            <div id="track-list">
            <?php include 'tracklist.php'; ?>
            </div>
          </div>
        </form>

