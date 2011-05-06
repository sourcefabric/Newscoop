        <div class="clear" style="margin-top:12px;"></div>
        <fieldset class="closeable advanced-search">
        <ul class="form-group">
              <li class="width-1-1"><label for="search_title">Title</label>
              <input type="text" autocomplete="off" onkeyup="" value="" class="input_text search" size="45" id="search_title" name="search_title">
          </li>
          </ul>
        <ul class="form-group">
              <li class="width-1-2"><label for="date_range">Creation date<span>select range</span></label>

              <input type="text" autocomplete="off" onkeyup="" value="" class="input_text date" size="45" id="date_from" name="date_from"><input type="text" autocomplete="off" onkeyup="" value="" class="input_text right-floated date" size="45" id="date_to" name="date_to">
          </li>
              <li class="width-1-2 last"><label for="duration">Duration</label>
              <select onchange="" class="input_select" id="duration" name="duration">
                  <option selected="selected" value="1">all durations</option>
                  <option value="2">short</option>
                  <option value="3">medium</option>
                  <option value="4">long</option>
              </select>
          </li>
          </ul>
        <ul class="form-group">
              <li class="width-1-2"><label for="tags_genres">Tags / Genres</label>
              <input type="text" autocomplete="off" onkeyup="" value="" class="input_text" size="45" id="tags_genres" name="tags_genres">
             </li>
              <li class="width-1-2 last"><label for="type">Track/set type</label>
              <select onchange="" class="input_select" id="type" name="type">
                  <option selected="selected" value="">Select type</option>
                  <option value="1">Type 1</option>
                  <option value="2">Type 2</option>
                  <option value="3">Type 3</option>
              </select>
          </li>
          </ul>
          <ul class="form-group">
              <li class="width-1-2"><label for="creator">Creator</label>
              <input type="text" autocomplete="off" onkeyup="" value="" class="input_text" size="45" id="creator" name="creator">
              </li>
              <li class="width-1-2 last"><label for="licence">Licence</label>
            <select onchange="" class="input_select" id="licence" name="licence">
                  <option selected="selected" value="">Select licence type</option>
                  <option value="1">Type 1</option>
                  <option value="2">Type 2</option>
                  <option value="3">Type 3</option>
              </select>
          </li>
          </ul>
          <div class="clear" style="margin-bottom:6px;"></div>
          <input type="button" name="advanced-search" id="advanced-search" value="Search" class="save-button-small right-floated" onclick="">

        </fieldset>
        <div class="closeable-bottom">
            <a href="#" class="toggle-button"><span></span>Advanced search</a>
        </div>
