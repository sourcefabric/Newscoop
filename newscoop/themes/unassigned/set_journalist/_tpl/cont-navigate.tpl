  {{ if $gimme->current_articles_list->at_end }}
        <div class="navigation group">
        {{ if $gimme->current_list->has_next_elements }}
          <div class="alignleft"><a href="{{ uripath options="issue" }}?{{ urlparameters options="next_items" }}">« {{ if $gimme->language->english_name == "English" }}Older Entries{{ /if }}{{ if $gimme->language->english_name == "Spanish" }}Artículos anteriores{{ /if }}{{ if $gimme->language->english_name == "Polish" }}Starsze wpisy{{ /if }}{{ if $gimme->language->english_name == "Russian" }}Старые записи{{ /if }}</a></div>
        {{ /if }}
        {{ if $gimme->current_list->has_previous_elements }}
          <div class="alignright"><a href="{{ uripath options="issue" }}?{{ urlparameters options="previous_items" }}">{{ if $gimme->language->english_name == "English" }}Newer Entries{{ /if }}{{ if $gimme->language->english_name == "Spanish" }}Artículos más recientes{{ /if }}{{ if $gimme->language->english_name == "Polish" }}Nowsze wpisy{{ /if }}{{ if $gimme->language->english_name == "Russian" }}Новые записи{{ /if }} »</a></div>
        {{ /if }}
        </div>
   {{ /if }}         