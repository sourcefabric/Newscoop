    <h3>{{ if $gimme->language->english_name == "English" }}Categories{{ /if }}{{ if $gimme->language->english_name == "Spanish" }}Categorías{{ /if }}{{ if $gimme->language->english_name == "Polish" }}Kategorie{{ /if }}{{ if $gimme->language->english_name == "Russian" }}Категории{{ /if }}</h3>
        <select name="cat" id="cat" class="postform">
          <option selected="selected" value="-1">{{ if $gimme->language->english_name == "English" }}Choose category{{ /if }}{{ if $gimme->language->english_name == "Spanish" }}Elije una categoría{{ /if }}{{ if $gimme->language->english_name == "Polish" }}Wybierz kategorię{{ /if }}{{ if $gimme->language->english_name == "Russian" }}Выбрать категорию{{ /if }}</option>
          
{{ set_topic name="categories:en" }}
{{ list_subtopics }}           
          <option class="level-0" value="{{ $gimme->topic->identifier }}">{{ $gimme->topic->name }}&nbsp;&nbsp;({{ list_articles length="1" ignore_issue="true" ignore_section="true" }}{{ $gimme->current_articles_list->count }}{{ /list_articles }})</option>
{{ /list_subtopics }}
{{ unset_topic }}          
        </select>

        <script type="text/javascript">
        /* <![CDATA[ */
      var dropdown = document.getElementById("cat");
      function onCatChange() {
        if ( dropdown.options[dropdown.selectedIndex].value > 0 ) {
          location.href = "http://{{ $gimme->publication->site }}/{{ $gimme->language->code }}/?tpid="+dropdown.options[dropdown.selectedIndex].value;
        }
      }
      dropdown.onchange = onCatChange;
        /* ]]> */
        </script>
