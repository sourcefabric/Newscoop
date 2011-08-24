    <div class="post hentry">
        <h2 class="entry-title"><a href="">{{ if $gimme->section->number == "30" }}[{{ $gimme->section->name }}] {{ /if }}{{ $gimme->article->name }}</a></h2>
        <p class="comments"><a href="{{ uri options="article" }}#comments">{{ if $gimme->article->comment_count gt 0 }}{{ $gimme->article->comment_count }} comment(s){{ else }}{{ if $gimme->language->english_name == "English" }}leave a comment{{ /if }}{{ if $gimme->language->english_name == "Spanish" }}dejar un comentario{{ /if }}{{ if $gimme->language->english_name == "Polish" }}dodaj komentarz{{ /if }}{{ if $gimme->language->english_name == "Russian" }}оставить комментарий{{ /if }}{{ /if }}</a></p>

        <div class="main entry-content group">
          {{ if $gimme->section->number == "20" }}<img class="minib" alt="miniblog" src="{{ uri static_file='_img/miniblog-150x150.jpg' }}" />{{ /if }}
          {{ if $gimme->section->number == "30" }}
            {{ list_article_images }}
              <div class="cs_img"><img class="" alt="{{ $gimme->article->image->description }}" src="{{ uri options="image width 700" }}" /></div>
            {{ /list_article_images }}
          {{ /if }}
          <p style="text-align: justify;">{{ $gimme->article->excerpt }}</p>
          <p style="text-align: justify;">{{ $gimme->article->body_text }}</p>
        </div><!-- /.main entry-content group -->

            <div class="meta group">
                <div class="signature">
                    <p class="author vcard">{{ if $gimme->language->english_name == "English" }}Written by{{ /if }}{{ if $gimme->language->english_name == "Spanish" }}Escrito por{{ /if }}{{ if $gimme->language->english_name == "Polish" }}Opublikowane przez{{ /if }}{{ if $gimme->language->english_name == "Russian" }}Написано{{ /if }} <span class="fn">{{ $gimme->article->author->name }}</span></p>
                    <p><abbr class="updated" title="{{ $gimme->article->publish_date|camp_date_format:"%Y-%m-%eT%H:%i:%S" }}">{{ $gimme->article->publish_date|camp_date_format:"%M %D, %Y @ %H:%i" }}</abbr></p>
                </div>  
                <div class="tags">
                    <p>{{ if $gimme->language->english_name == "English" }}Posted in{{ /if }}{{ if $gimme->language->english_name == "Spanish" }}Publicado en {{ /if }}{{ if $gimme->language->english_name == "Polish" }}Opublikowane  w {{ /if }}{{ if $gimme->language->english_name == "Russian" }}Опубликовано {{ /if }} {{ list_article_topics root="categories:en" }}<a href="{{ uri options="template index.tpl" }}" title="View all posts in category '{{ $gimme->topic->name }}'" rel="category tag">{{ $gimme->topic->name }}</a>{{ if !$gimme->current_list->at_end }}, {{ /if }}{{ /list_article_topics }}</p>
                    <p>{{ if $gimme->language->english_name == "English" }}Tagged with{{ /if }}{{ if $gimme->language->english_name == "Spanish" }}Etiquetado con{{ /if }}{{ if $gimme->language->english_name == "Polish" }}Tagi: {{ /if }}{{ if $gimme->language->english_name == "Russian" }}C тегом: {{ /if }} {{ list_article_topics root="tags:en" }}<a href="{{ uri options="template archive-tags.tpl" }}" title="View all posts with tag '{{ $gimme->topic->name }}'" rel="tag">{{ $gimme->topic->name }}</a>{{ if !$gimme->current_list->at_end }}, {{ /if }}{{ /list_article_topics }}</p>
                </div>
            </div><!-- /.meta group -->
        
    </div><!-- END .hentry -->
    
<div class="navigation group">
    {{ assign var="artdate" value=$gimme->article->publish_date|camp_date_format:"%Y-%m-%e" }}
    {{ local }}
    {{ list_articles length="1" ignore_issue="true" ignore_section="true" order="bypublishdate desc" constraints="publish_date smaller $artdate" }}
    <div class="alignleft">&laquo; <a href="{{ uri }}" rel="prev">{{ $gimme->article->name }}</a></div>
    {{ /list_articles }}
    {{ list_articles length="1" ignore_issue="true" ignore_section="true" order="bypublishdate asc" constraints="publish_date greater $artdate" }}    
    <div class="alignright"><a href="{{ uri }}" rel="next">{{ $gimme->article->name }}</a> &raquo;</div>
    {{ /list_articles }}
    {{ /local }}
</div>
