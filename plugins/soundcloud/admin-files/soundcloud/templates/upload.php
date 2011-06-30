        <form id="upload-form" name="upload-form" method="post" action="" enctype="multipart/form-data">
        <div class="clear"></div>
        <h2><? putGS('Upload Track to Soundcloud'); ?></h2>
        <div class="clear"></div>
        <fieldset class="upload-field clearfix">
           <ul class="form-group">
              <li class="width-1-1"><label><? putGS('Title') ?></label>
              <input type="text" value="<?= htmlentities($track['title']) ?>" class="input_text" size="45" name="title" />
              </li>
          </ul>
        <input type="file" name="asset_data" class="input_text" size="70">
        <span><p><? putGS('We support AIFF, WAVE, FLAC, OGG, MP2, MP3, AAC files. Please, respect the <a href="http://soundcloud.com/community-guidelines" target="_blank">community guidelines</a> and only upload tracks with permission from the rights holders.') ?></p></span>
        </fieldset>

            <ul class="form-group">
              <li class="width-description_big"><label><? putGS('Description') ?></label>
                <textarea class="input_text_area" rows="8" name="description"><?= htmlentities($track['description']) ?></textarea>
              </li>
              <li class="group-holder_260">
              <ul class="vertical-group">
                <li><label for="type"><? putGS('Type') ?></label>
                  <select name="track_type" class="input_select">
                  <? camp_html_select_option('', $track['track_type'], getGS('Select type'));
                      foreach ($trackTypes as $key => $value):
                        camp_html_select_option($key, $track['track_type'], $value);
                      endforeach ?>
                  </select>
                </li>
                <li><label for="genre"><? putGS('Genre') ?></label>
                <input type="text" name="genre" class="input_text" value="<?= htmlentities($track['genre']) ?>" />
                </li>
              </ul>

              </li>
          </ul>
          <ul class="form-group">
                  <li class="width-description_big"><label for="artwork_data"><? putGS('Image file') ?><span><? putGS('Specify the artwork file') ?></span></label>
                <input type="file" name="artwork_data" class="input_text" size="40"/>
            </li>
                  <li class="group-holder_260"><label><? putGS('License') ?></label>
                    <select name="license" class="input_select">
                      <? camp_html_select_option('', $track['license'], getGS('Select license type'));
                          foreach ($licenseTypes as $key => $value):
                            camp_html_select_option($key, $track['license'], $value);
                          endforeach ?>
                    </select>
                  </li>
        </ul>
            <ul class="form-group">
              <li class="width-1-1"><label><? putGS('Tags') ?><span><? putGS('A space separated list of tags') ?></span></label>
              <input type="text" value="<?= htmlentities($track['tag_list']) ?>" class="input_text" size="45" name="tag_list">
              </li>
        </ul>
          <div class="clear" style="margin-top:16px;"></div>
          <a id="upload-link-more-options" class="toggle-link" href="#"><span class="text"><? putGS('More options') ?></span></a>
          <div id="upload-div-more-options" class="upload-more-options clearfix">
                <ul class="form-group">
                  <li class="width-1-3_fixed"><label><? putGS('Label') ?></label>
                    <input type="text" value="<?= htmlentities($track['label_name']) ?>" class="input_text" size="45" name="label_name">
                  </li>
                  <li><label><? putGS('Release date') ?></label>
                    <input type="text" value="<?= htmlentities($track['release_date']) ?>" class="input_text1 date" size="25" name="release_date">
                  </li>
               </ul>
                <ul class="form-group">
                  <li class="width-1-3_fixed"><label><? putGS('Release/catalogue number') ?></label>
                    <input type="text" value="<?= htmlentities($track['release']) ?>" class="input_text" size="45" name="release">
                  </li>
                  <li class="width-1-3_fixed"><label><? putGS('ISRC') ?></label>
                    <input type="text" value="<?= htmlentities($track['isrc']) ?>" class="input_text" size="45" name="isrc">
                  </li>
                  <li class="width-1-3_fixed"><label><? putGS('Bpm') ?></label>
                    <input type="text" value="<?= htmlentities($track['bpm']) ?>" class="input_text" size="45" name="bpm">
                  </li>
                  <li class="width-1-3_fixed"><label><? putGS('Key signature') ?></label>
                    <select class="input_select" name="key_signature">
                      <? camp_html_select_option('', $track['key_signature'], getGS('Select key'));
                          foreach ($keyTypes as $value):
                            camp_html_select_option($value, $track['key_signature'], $value);
                          endforeach ?>
                    </select>
                </li>
               </ul>
               <ul class="form-group">
                  <li class="width-1-1_fixed"><label><? putGS('Buy link') ?><span><? putGS('Find out how to sell your tracks on other platforms easily using SoundCloud in our App Gallery') ?></span></label>
                    <input type="text" value="<?= htmlentities($track['purchase_url']) ?>" class="input_text" size="45" name="purchase_url">
                  </li>
                  <li class="width-1-1_fixed"><label><? putGS('Video link') ?><span><? putGS('Youtube, Vimeo, Dailymotion and Viddler videos will appear in an on-site player') ?></span></label>
                    <input type="text" value="<?= htmlentities($track['video_url']) ?>" class="input_text" size="45" name="video_url">
                  </li>
               </ul>
          </div>
          <h3 class="separate"><? putGS('Settings') ?></h3>
        <div class="sharing-control">
            <div id="div-public-sharing" class="button-public privacy-level-button <?= $track['sharing']=='public' ? 'selected' : '' ?>">
                <input id="upload-sharing" type="radio" value="public" name="sharing" <?= $track['sharing']=='public' ? 'checked="checked"' : '' ?> autocomplete="off">
                <span class="description"><? putGS('Public') ?>
                <span class="expl"><span class="default"><? putGS('Click to make this track available to everyone') ?></span>
                <span class="active"><? putGS('This track is currently available to everyone') ?></span></span></span>
            </div>
            <div id="div-private-sharing" class="button-private privacy-level-button <?= $track['sharing']=='private' ? 'selected' : '' ?>">
                <input id="upload-sharing" type="radio" value="private" name="sharing" <?= $track['sharing']=='private' ? 'checked="checked"' : '' ?> autocomplete="off">
                <span class="description"><? putGS('Private') ?>
                <span class="expl"><span class="default"><? putGS('Click to make this track private') ?></span>
                <span data-sc-default-text="Only you have access" class="active"><? putGS('Only you have access') ?></span></span></span>
            </div>
        </div>
            <ul class="form-group">
              <li class="width-1-1 advanced-settings-group"><label><? putGS('Advanced settings') ?></label>
                  <dl class="settings-list clearfix">
                    <dt><? putGS('Downloadable') ?>:</dt>
                        <dd><input name="downloadable" type="radio" value="1" <?= $track['downloadable'] ? 'checked="checked"' : '' ?> /><label><? putGS('Yes') ?></label>
                            <input name="downloadable" type="radio" value="0" <?= !$track['downloadable'] ? 'checked="checked"' : '' ?> /><label><? putGS('No') ?></label></dd>
                    <dt><? putGS('Streamable') ?>:</dt>
                        <dd><input name="streamable" type="radio" value="1" <?= $track['streamable'] ? 'checked="checked"' : '' ?> /><label><? putGS('Yes') ?></label>
                            <input name="streamable" type="radio" value="0" <?= !$track['streamable'] ? 'checked="checked"' : '' ?> /><label><? putGS('No') ?></label></dd>
                </dl>
              </li>
              <li class="width-1-1 sharing-note"><label><? putGS('Social media sharing message') ?></label>
              <textarea name="sharing_note" rows="2" class="input_text_area"><?= $track['sharing_note'] ?></textarea>
              </li>
          </ul>
          <div class="clear"></div>
          <div class="button-bar">
          <input type="hidden" value="upload" id="upload-action" name="action" />
          <? if ($attachement): ?>
              <input type="button" id="attach-submit" class="save-button-small right-floated" value="<? putGS('Attach') ?>" name="attach">
          <? endif ?>
              <input type="submit" id="upload-submit" class="save-button-small right-floated" value="<? putGS('Upload') ?>" name="upload">
              <input type="reset" class="button right-floated" value="<? putGS('Reset') ?>" name="reset">
          </div>
          </form>