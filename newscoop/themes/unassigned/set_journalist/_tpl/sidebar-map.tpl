{{ set_default_article }}
{{ if $gimme->article->defined }}
 {{ if $gimme->article->has_map }}
        <h3>{{ if $gimme->language->english_name == "English" }}Map{{ /if }}{{ if $gimme->language->english_name == "Spanish" }}Mapa{{ /if }}{{ if $gimme->language->english_name == "Polish" }}Mapa{{ /if }}<br /></h3>
        <div class="textwidget" style="text-align: center">
{{ map show_locations_list="false" show_reset_link=false width="200" height="250" }}
        </div>
{{ /if }}        
{{ /if }}        