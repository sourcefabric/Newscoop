        <div class="clear"></div>
        <input type="hidden" name="id" value="<?= $track['id'] ?>" />
        <input id="edit-attach" type="hidden" name="attach" value="" />
        <input type="hidden" name="action" value="save" />
        <input type="hidden" name="article" value="<?= $article ?>" />
        <div class="buttons">
            <a id="edit-play" href="" class="ui-state-default icon-button"><span class="ui-icon"></span><? putGS('Play') ?></a>
        </div>
        <div class="clear"></div>
        <div id="edit-player" style="display:none;margin-top:10px">
        <object height="81" width="100%"><param name="movie" value="http://player.soundcloud.com/player.swf?url=<?= urlencode($track['secret_uri']) ?>&amp;show_comments=true&amp;auto_play=true&amp;color=08597d"></param>
        <param name="allowscriptaccess" value="always"></param>
        <embed allowscriptaccess="always" height="81" src="http://player.soundcloud.com/player.swf?url=<?= urlencode($track['secret_uri']) ?>&amp;show_comments=true&amp;auto_play=true&amp;color=08597d" type="application/x-shockwave-flash" width="100%"></embed>
        </object>
        </div>
        <fieldset class="upload-field clearfix">
           <ul class="form-group">
              <li class="width-1-1"><label><? putGS('Title') ?></label>
              <input type="text" autocomplete="off" value="<?= @$track['title'] ?>" class="input_text" size="45" name="title" />
              </li>
          </ul>
        </fieldset>
            <ul class="form-group">
             <li class="sc_image"><label><? putGS('Image') ?></label>
                <div class="sc_image_holder">
                <? if (@$track['artwork_url']): ?>
                    <img alt="" src="<?= @$track['artwork_url'] ?>">
                <? endif ?>
                </div>
                <input type="file" name="artwork_data" class="input_text" size="40"/>
                </li>
              <li class="sc_description"><label><? putGS('Description') ?></label>
                <textarea class="input_text_area" rows="5" name="description"><?= @$track['description'] ?></textarea>
              </li>
              <li class="group-holder_260">
              <ul class="vertical-group">
                <li><label for="type"><? putGS('Type') ?></label>
                  <select name="track_type" class="input_select">
                  <? camp_html_select_option('', @$track['track_type'], getGS('Select type'));
                      foreach ((array)$trackTypes as $key => $value):
                        camp_html_select_option($key, @$track['track_type'], $value);
                      endforeach ?>
                  </select>
                </li>
                <li><label><? putGS('Genre') ?></label>
                <input type="text" autocomplete="off" name="genre" class="input_text" value="<?= @$track['genre'] ?>" />
                </li>
                  <li><label><? putGS('License') ?></label>
                    <select name="license" class="input_select">
                      <? camp_html_select_option('', @$track['license'], getGS('Select license type'));
                          foreach ((array)$licenseTypes as $key => $value):
                            camp_html_select_option($key, @$track['license'], $value);
                          endforeach ?>
                    </select>
                  </li>
              </ul>
              </li>
          </ul>
            <ul class="form-group">
              <li class="width-1-1"><label><? putGS('Tags') ?><span><? putGS('A space separated list of tags') ?></span></label>
              <input type="text" autocomplete="off" value="<?= @$track['tag_list'] ?>" class="input_text" size="45" name="tag_list">
              </li>
           </ul>
          <div class="clear" style="margin-top:16px;"></div>
          <a id="edit-link-more-options" class="toggle-link" href="#"><span class="text"><? putGS('More options') ?></span></a>
          <div id="edit-div-more-options" class="upload-more-options clearfix">
                <ul class="form-group">
                  <li class="width-1-3_fixed"><label><? putGS('Label') ?></label>
                    <input type="text" value="<?= @$track['label_name'] ?>" class="input_text" size="45" name="label_name">
                  </li>
                  <li><label><? putGS('Release date') ?></label>
                    <input type="text" value="<?= @$track['release_date'] ?>" class="input_text1 date" size="25" name="release_date">
                  </li>
               </ul>
                <ul class="form-group">
                  <li class="width-1-3_fixed"><label><? putGS('Release/catalogue number') ?></label>
                    <input type="text" value="<?= @$track['release'] ?>" class="input_text" size="45" name="release">
                  </li>
                  <li class="width-1-3_fixed"><label><? putGS('ISRC') ?></label>
                    <input type="text" value="<?= @$track['isrc'] ?>" class="input_text" size="45" name="isrc">
                  </li>
                  <li class="width-1-3_fixed"><label><? putGS('Bpm') ?></label>
                    <input type="text" value="<?= @$track['bpm'] ?>" class="input_text" size="45" name="bpm">
                  </li>
                  <li class="width-1-3_fixed"><label><? putGS('Key signature') ?></label>
                    <select class="input_select" name="key_signature">
                      <? camp_html_select_option('', @$track['key_signature'], getGS('Select key'));
                          foreach ((array)$keyTypes as $value):
                            camp_html_select_option($value, @$track['key_signature'], $value);
                          endforeach ?>
                    </select>
                </li>
               </ul>
               <ul class="form-group">
                  <li class="width-2-3_fixed"><label><? putGS('Buy link') ?><span><? putGS('Find out how to sell your tracks on other platforms easily using SoundCloud in our App Gallery') ?></span></label>
                    <input type="text" value="<?= @$track['purchase_url'] ?>" class="input_text" size="45" name="purchase_url">
                  </li>
                  <li class="width-2-3_fixed"><label><? putGS('Video link') ?><span><? putGS('Youtube, Vimeo, Dailymotion and Viddler videos will appear in an on-site player') ?></span></label>
                    <input type="text" value="<?= @$track['video_url'] ?>" class="input_text" size="45" name="video_url">
                  </li>
               </ul>
          </div>
          <h3 class="separate"><? putGS('Settings') ?></h3>
        <div class="sharing-control">
            <div id="div-edit-public-sharing" class="button-public privacy-level-button <?= @$track['sharing']=='public' ? 'selected' : '' ?>">
                <input id="edit-sharing" type="radio" value="public" name="sharing" <?= @$track['sharing']=='public' ? 'checked="checked"' : '' ?> autocomplete="off">
                <span class="description"><? putGS('Public') ?>
                <span class="expl"><span class="default"><? putGS('Click to make this track available to everyone') ?></span>
                <span class="active"><? putGS('This track is currently available to everyone') ?></span></span></span>
            </div>
            <div id="div-edit-private-sharing" class="button-private privacy-level-button <?= @$track['sharing']=='private' ? 'selected' : '' ?>">
                <input id="edit-sharing" type="radio" value="private" name="sharing" <?= @$track['sharing']=='private' ? 'checked="checked"' : '' ?> autocomplete="off">
                <span class="description"><? putGS('Private') ?>
                <span class="expl"><span class="default"><? putGS('Click to make this track private') ?></span>
                <span data-sc-default-text="Only you have access" class="active"><? putGS('Only you have access') ?></span></span></span>
            </div>
        </div>
            <ul class="form-group">
              <li class="width-1-1 advanced-settings-group"><label><? putGS('Advanced settings') ?></label>
                  <dl class="settings-list clearfix">
                    <dt><? putGS('Downloadable') ?>:</dt>
                        <dd><input name="downloadable" type="radio" value="1" <?= @$track['downloadable'] ? 'checked="checked"' : '' ?> /><label><? putGS('Yes') ?></label>
                            <input name="downloadable" type="radio" value="0" <?= !@$track['downloadable'] ? 'checked="checked"' : '' ?> /><label><? putGS('No') ?></label></dd>
                    <dt><? putGS('Streamable') ?>:</dt>
                        <dd><input name="streamable" type="radio" value="1" <?= @$track['streamable'] ? 'checked="checked"' : '' ?> /><label><? putGS('Yes') ?></label>
                            <input name="streamable" type="radio" value="0" <?= !@$track['streamable'] ? 'checked="checked"' : '' ?> /><label><? putGS('No') ?></label></dd>
                </dl>
              </li>
          </ul>
          <div class="clear"></div>
          <div class="button-bar">
          <? if ($attachement): ?>
              <input type="button" id="edit-attach" class="save-button-small right-floated" value="<? putGS('Attach') ?>" name="attach">
          <? endif ?>
              <input  id="edit-submit" type="submit" class="save-button-small right-floated" value="<? putGS('Save') ?>" name="save">
              <input  id="edit-cancel" type="button" class="button right-floated" value="<? putGS('Cancel') ?>" name="cancel">
          </div>