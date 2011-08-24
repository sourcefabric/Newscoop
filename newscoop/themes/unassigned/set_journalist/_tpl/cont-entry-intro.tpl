        <div class="post hentry">
            <h2 class="entry-title"><a href="{{ uri options="article" }}">{{ if $gimme->section->number gt 10 }}[ {{ $gimme->section->name }} ] {{ /if }}{{ $gimme->article->name }}</a></h2>
            <p class="comments"><a href="{{ uri options="article" }}#comments">{{ if $gimme->article->comment_count gt 0 }}{{ $gimme->article->comment_count }} comment(s){{ else }}{{ if $gimme->language->english_name == "English" }}leave a comment{{ /if }}{{ if $gimme->language->english_name == "Spanish" }}dejar un comentario{{ /if }}{{ if $gimme->language->english_name == "Polish" }}dodaj komentarz{{ /if }}{{ if $gimme->language->english_name == "Russian" }}оставить комментарий{{ /if }}{{ /if }}</a></p>

            <div class="main entry-content group">            
                <p style="text-align: justify;">{{ $gimme->article->excerpt }}</p>
                <p style="text-align: justify;"> <a href="{{ uri options="article" }}" class="more-link">{{ if $gimme->language->english_name == "English" }}Read the rest of this entry{{ /if }}{{ if $gimme->language->english_name == "Polish" }}Przeczytaj cały artykuł{{ /if }}{{ if $gimme->language->english_name == "Russian" }}читать далее{{ /if }}{{ if $gimme->language->english_name == "Spanish" }}Leer el resto de esta entrada{{ /if }} »</a></p>
            </div>

            <div class="meta group">
                <div class="signature">
                    <p class="author vcard">{{ if $gimme->language->english_name == "English" }}Written by{{ /if }}{{ if $gimme->language->english_name == "Spanish" }}Escrito por{{ /if }}{{ if $gimme->language->english_name == "Polish" }}Opublikowane przez{{ /if }}{{ if $gimme->language->english_name == "Russian" }}Написано{{ /if }} <span class="fn">{{ $gimme->article->author->name }}</span></p>
                    <p><abbr class="updated" title="{{ $gimme->article->publish_date|camp_date_format:"%Y-%m-%eT%H:%i:%S" }}">{{ $gimme->article->publish_date|camp_date_format:"%M %D, %Y @ %H:%i" }}</abbr></p>
                </div>  
                <div class="tags">
                    <p>{{ if $gimme->language->english_name == "English" }}Posted in{{ /if }}{{ if $gimme->language->english_name == "Spanish" }}Publicado en {{ /if }}{{ if $gimme->language->english_name == "Polish" }}Opublikowane  w {{ /if }}{{ if $gimme->language->english_name == "Russian" }}Опубликовано {{ /if }} {{ list_article_topics root="categories:en" }}<a href="{{ uri options="template index.tpl" }}" title="View all posts in category '{{ $gimme->topic->name }}'" rel="category tag">{{ $gimme->topic->name }}</a>{{ if !$gimme->current_list->at_end }}, {{ /if }}{{ /list_article_topics }}</p>
                    <p>{{ if $gimme->language->english_name == "English" }}Tagged with{{ /if }}{{ if $gimme->language->english_name == "Spanish" }}Etiquetado con{{ /if }}{{ if $gimme->language->english_name == "Polish" }}Tagi: {{ /if }}{{ if $gimme->language->english_name == "Russian" }}C тегом: {{ /if }} {{ list_article_topics root="tags:en" }}<a href="http://{{ $gimme->publication->site }}/{{ $gimme->language->code }}/?tpl=8&tpid={{ $gimme->topic->identifier }}" title="View all posts with tag '{{ $gimme->topic->name }}'" rel="tag">{{ $gimme->topic->name }}</a>{{ if !$gimme->current_list->at_end }}, {{ /if }}{{ /list_article_topics }}</p>
                </div>
            </div><!-- /.meta group -->
            
        </div><!-- END .hentry -->
