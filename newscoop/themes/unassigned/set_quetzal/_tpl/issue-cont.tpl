<div class="span8 section-articles">
{{ list_sections }}  
{{ list_articles }}
{{ if $gimme->current_articles_list->at_beginning }}
    <section class="archive-block">
        <h1 class="block-title">{{ $gimme->section->name}}</h1>
        <hr>
{{ /if }}    
        <article class="section-article archive-entry">
            {{ include file='_tpl/img/img_130x70.tpl' }}
                <header>
                    <h2><a href="{{ uri options="article" }}">{{ $gimme->article->name }}</a></h2>
                </header>
                <span class="article-date"><time datetime="{{ $gimme->article->publish_date|date_format:"%Y-%m-%dT%H:%MZ" }}">{{ $gimme->article->publish_date|camp_date_format:"%M %e, %Y" }}</time></span><br>
                <span>{{ $gimme->article->comment_count }} {{ #comments# }}</span>
                                       
            <div class="clearfix"></div>
        </article>
{{ if $gimme->current_articles_list->at_end }}

    </section>

{{ /if }}
{{ /list_articles }}    
{{ /list_sections }}
</div>
