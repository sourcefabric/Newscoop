<?php 
$translator = \Zend_Registry::get('container')->getService('translator');
?>
        <form id="upload-form" name="upload-form" method="post" action="" enctype="multipart/form-data">
        <div class="clear"></div>
        <h2><? echo $translator->trans('Upload Track to SoundCloud', array(), 'plugin_soundcloud'); ?></h2>
        <div class="clear"></div>
        <fieldset class="upload-field clearfix">
           <ul class="form-group">
              <li class="width-1-1"><label><? echo $translator->trans('Title', array(), 'plugin_soundcloud') ?></label>
              <input type="text" value="<?= htmlentities($track['title']) ?>" class="input_text" size="45" name="title" />
              </li>
          </ul>
        <input type="file" name="asset_data" class="input_text" size="70">
        <span><p><? echo $translator->trans('We support AIFF, WAVE, FLAC, OGG, MP2, MP3, AAC files. Please, respect the <a href="http://soundcloud.com/community-guidelines" target="_blank">community guidelines</a> and only upload tracks with permission from the rights holders.', array(), 'plugin_soundcloud') ?></p></span>
        </fieldset>

            <ul class="form-group">
              <li class="width-description_big"><label><? echo $translator->trans('Description') ?></label>
                <textarea class="input_text_area" rows="8" name="description"><?= htmlentities($track['description']) ?></textarea>
              </li>
              <li class="group-holder_260">
              <ul class="vertical-group">
                <li><label for="type"><? echo $translator->trans('Type') ?></label>
                  <select name="track_type" class="input_select">
                  <? camp_html_select_option('', $track['track_type'], $translator->trans('Select type', array(), 'plugin_soundcloud'));
                      foreach ($trackTypes as $key => $value):
                        camp_html_select_option($key, $track['track_type'], $value);
                      endforeach ?>
                  </select>
                </li>
                <li><label for="genre"><? echo $translator->trans('Genre', array(), 'plugin_soundcloud') ?></label>
                <input type="text" name="genre" class="input_text" value="<?= htmlentities($track['genre']) ?>" />
                </li>
              </ul>

              </li>
          </ul>
          <ul class="form-group">
                  <li class="width-description_big"><label for="artwork_data"><? echo $translator->trans('Image file', array(), 'plugin_soundcloud') ?><span><? echo $translator->trans('Specify the artwork file', array(), 'plugin_soundcloud') ?></span></label>
                <input type="file" name="artwork_data" class="input_text" size="40"/>
            </li>
                  <li class="group-holder_260"><label><? echo $translator->trans('License', array(), 'plugin_soundcloud') ?></label>
                    <select name="license" class="input_select">
                      <? camp_html_select_option('', $track['license'], $translator->trans('Select license type', array(), 'plugin_soundcloud'));
                          foreach ($licenseTypes as $key => $value):
                            camp_html_select_option($key, $track['license'], $value);
                          endforeach ?>
                    </select>
                  </li>
        </ul>
            <ul class="form-group">
              <li class="width-1-1"><label><? echo $translator->trans('Tags', array(), 'plugin_soundcloud') ?><span><? echo $translator->trans('A space separated list of tags', array(), 'plugin_soundcloud') ?></span></label>
              <input type="text" value="<?= htmlentities($track['tag_list']) ?>" class="input_text" size="45" name="tag_list">
              </li>
        </ul>
          <div class="clear" style="margin-top:16px;"></div>
          <a id="upload-link-more-options" class="toggle-link" href="#"><span class="text"><? echo $translator->trans('More options', array(), 'plugin_soundcloud') ?></span></a>
          <div id="upload-div-more-options" class="upload-more-options clearfix">
                <ul class="form-group">
                  <li class="width-1-3_fixed"><label><? echo $translator->trans('Label', array(), 'plugin_soundcloud') ?></label>
                    <input type="text" value="<?= htmlentities($track['label_name']) ?>" class="input_text" size="45" name="label_name">
                  </li>
                  <li><label><? echo $translator->trans('Release date', array(), 'plugin_soundcloud') ?></label>
                    <input type="text" value="<?= htmlentities($track['release_date']) ?>" class="input_text1 date" size="25" name="release_date">
                  </li>
               </ul>
                <ul class="form-group">
                  <li class="width-1-3_fixed"><label><? echo $translator->trans('Release/catalogue number', array(), 'plugin_soundcloud') ?></label>
                    <input type="text" value="<?= htmlentities($track['release']) ?>" class="input_text" size="45" name="release">
                  </li>
                  <li class="width-1-3_fixed"><label><? echo $translator->trans('ISRC', array(), 'plugin_soundcloud') ?></label>
                    <input type="text" value="<?= htmlentities($track['isrc']) ?>" class="input_text" size="45" name="isrc">
                  </li>
                  <li class="width-1-3_fixed"><label><? echo $translator->trans('Bpm', array(), 'plugin_soundcloud') ?></label>
                    <input type="text" value="<?= htmlentities($track['bpm']) ?>" class="input_text" size="45" name="bpm">
                  </li>
                  <li class="width-1-3_fixed"><label><? echo $translator->trans('Key signature', array(), 'plugin_soundcloud') ?></label>
                    <select class="input_select" name="key_signature">
                      <? camp_html_select_option('', $track['key_signature'], $translator->trans('Select key', array(), 'plugin_soundcloud'));
                          foreach ($keyTypes as $value):
                            camp_html_select_option($value, $track['key_signature'], $value);
                          endforeach ?>
                    </select>
                </li>
               </ul>
               <ul class="form-group">
                  <li class="width-1-1_fixed"><label><? echo $translator->trans('Buy link', array(), 'plugin_soundcloud') ?><span><? echo $translator->trans('Find out how to sell your tracks on other platforms easily using SoundCloud in our App Gallery', array(), 'plugin_soundcloud') ?></span></label>
                    <input type="text" value="<?= htmlentities($track['purchase_url']) ?>" class="input_text" size="45" name="purchase_url">
                  </li>
                  <li class="width-1-1_fixed"><label><? echo $translator->trans('Video link', array(), 'plugin_soundcloud') ?><span><? echo $translator->trans('Youtube, Vimeo, Dailymotion and Viddler videos will appear in an on-site player', array(), 'plugin_soundcloud') ?></span></label>
                    <input type="text" value="<?= htmlentities($track['video_url']) ?>" class="input_text" size="45" name="video_url">
                  </li>
               </ul>
          </div>
          <h3 class="separate"><? echo $translator->trans('Settings', array(), 'plugin_soundcloud') ?></h3>
        <div class="sharing-control">
            <div id="div-public-sharing" class="button-public privacy-level-button <?= $track['sharing']=='public' ? 'selected' : '' ?>">
                <input id="upload-sharing" type="radio" value="public" name="sharing" <?= $track['sharing']=='public' ? 'checked="checked"' : '' ?> autocomplete="off">
                <span class="description"><? echo $translator->trans('Public', array(), 'plugin_soundcloud') ?>
                <span class="expl"><span class="default"><? echo $translator->trans('Click to make this track available to everyone', array(), 'plugin_soundcloud') ?></span>
                <span class="active"><? echo $translator->trans('This track is currently available to everyone', array(), 'plugin_soundcloud') ?></span></span></span>
            </div>
            <div id="div-private-sharing" class="button-private privacy-level-button <?= $track['sharing']=='private' ? 'selected' : '' ?>">
                <input id="upload-sharing" type="radio" value="private" name="sharing" <?= $track['sharing']=='private' ? 'checked="checked"' : '' ?> autocomplete="off">
                <span class="description"><? echo $translator->trans('Private', array(), 'plugin_soundcloud') ?>
                <span class="expl"><span class="default"><? echo $translator->trans('Click to make this track private', array(), 'plugin_soundcloud') ?></span>
                <span data-sc-default-text="Only you have access" class="active"><? echo $translator->trans('Only you have access', array(), 'plugin_soundcloud') ?></span></span></span>
            </div>
        </div>
            <ul class="form-group">
              <li class="width-1-1 advanced-settings-group"><label><? echo $translator->trans('Advanced settings', array(), 'plugin_soundcloud') ?></label>
                  <dl class="settings-list clearfix">
                    <dt><? echo $translator->trans('Downloadable', array(), 'plugin_soundcloud') ?>:</dt>
                        <dd><input name="downloadable" type="radio" value="1" <?= $track['downloadable'] ? 'checked="checked"' : '' ?> /><label><? echo $translator->trans('Yes') ?></label>
                            <input name="downloadable" type="radio" value="0" <?= !$track['downloadable'] ? 'checked="checked"' : '' ?> /><label><? echo $translator->trans('No') ?></label></dd>
                    <dt><? echo $translator->trans('Streamable', array(), 'plugin_soundcloud') ?>:</dt>
                        <dd><input name="streamable" type="radio" value="1" <?= $track['streamable'] ? 'checked="checked"' : '' ?> /><label><? echo $translator->trans('Yes') ?></label>
                            <input name="streamable" type="radio" value="0" <?= !$track['streamable'] ? 'checked="checked"' : '' ?> /><label><? echo $translator->trans('No') ?></label></dd>
                </dl>
              </li>
              <li class="width-1-1 sharing-note"><label><? echo $translator->trans('Social media sharing message', array(), 'plugin_soundcloud') ?></label>
              <textarea name="sharing_note" rows="2" class="input_text_area"><?= $track['sharing_note'] ?></textarea>
              </li>
          </ul>
          <div class="clear"></div>
          <div class="button-bar">
          <input type="hidden" value="upload" id="upload-action" name="action" />
          <? if ($attachement): ?>
              <input type="button" id="attach-submit" class="save-button-small right-floated" value="<? echo $translator->trans('Attach') ?>" name="attach">
          <? endif ?>
              <input type="submit" id="upload-submit" class="save-button-small right-floated" value="<? echo $translator->trans('Upload', array(), 'plugin_soundcloud') ?>" name="upload">
              <input type="reset" class="button right-floated" value="<? echo $translator->trans('Reset', array(), 'plugin_soundcloud') ?>" name="reset">
          </div>
          </form>
