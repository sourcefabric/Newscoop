<?php 
$translator = \Zend_Registry::get('container')->getService('translator');
?>
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
          <input type="submit" id="search-submit" value="<? echo $translator->trans('Search'); ?>" class="save-button-small">
          <input type="button" value="<? echo $translator->trans('Search options', array(), 'plugin_soundcloud'); ?>" class="button toggle-button">
          </div>
        </div>
        <fieldset class="closeable advanced-search" style="display:none">
        <ul class="form-group">
          <li class="width-1-2"><label><? echo $translator->trans('Filter', array(), 'plugin_soundcloud') ?></label>
                  <select name="filter" class="input_select">
                    <option value="" selected="selected"><? echo $translator->trans('All') ?></option>
                    <option value="private"><? echo $translator->trans('Private', array(), 'plugin_soundcloud') ?></option>
                    <option value="public"><? echo $translator->trans('Public', array(), 'plugin_soundcloud') ?></option>
                    <option value="downloadable"><? echo $translator->trans('Downlodable', array(), 'plugin_soundcloud') ?></option>
                    <option value="streamable"><? echo $translator->trans('Streamable', array(), 'plugin_soundcloud') ?></option>
                  </select>
          </li>
              <li class="last"><label><? echo $translator->trans('Creation date', array(), 'plugin_soundcloud') ?><span><? echo $translator->trans('select range', array(), 'plugin_soundcloud') ?></span></label>
              <input type="text" value="" class="date" size="17" name="created_at[from]">
              <input type="text" value="" class="date" size="17" name="created_at[to]">
          </li>
          </ul>
        <ul class="form-group">
              <li class="width-1-2"><label><? echo $translator->trans('Tags', array(), 'plugin_soundcloud') ?><span><? echo $translator->trans('a comma separated list of tags', array(), 'plugin_soundcloud') ?></span></label>
              <input type="text" autocomplete="off" value="" class="input_text" size="45" name="tags">
             </li>
              <li class="width-1-2 last"><label for="type"><? echo $translator->trans('Track type', array(), 'plugin_soundcloud') ?></label>
                  <select name="types" class="input_select">
                  <? camp_html_select_option('', '', $translator->trans('Select type', array(), 'plugin_soundcloud'));
                      foreach ($trackTypes as $key => $value):
                        camp_html_select_option($key, '', $value);
                      endforeach ?>
                  </select>
          </li>
          </ul>
          <ul class="form-group">
              <li class="width-1-2"><label><? echo $translator->trans('Genres', array(), 'plugin_soundcloud') ?><span><? echo $translator->trans('a comma separated list of genres', array(), 'plugin_soundcloud') ?></span></label>
              <input type="text" autocomplete="off" value="" class="input_text" size="45" name="genres">
              </li>
              <li class="width-1-2 last"><label><? echo $translator->trans('License', array(), 'plugin_soundcloud') ?></label>
                    <select name="license" class="input_select">
                      <? camp_html_select_option('', '', $translator->trans('Select license type', array(), 'plugin_soundcloud'));
                          foreach ($licenseTypes as $key => $value):
                            camp_html_select_option($key, '', $value);
                          endforeach ?>
                    </select>
          </li>
          </ul>
              <input type="reset" class="button right-floated" value="<? echo $translator->trans('Reset', array(), 'plugin_soundcloud') ?>" name="reset">
        </fieldset>
            <div id="track-list">
            <?php include 'tracklist.php'; ?>
            </div>
          </div>
        </form>

