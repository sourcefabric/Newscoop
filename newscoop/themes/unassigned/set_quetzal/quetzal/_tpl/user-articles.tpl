{{ if $user->isAuthor() }}
{{ $escapedName=str_replace(" ", "\ ", $user->author->name) }}
{{/if}}
<div class="span8 section-articles profile-articles community-articles">
    <section class="archive-block">
        <div class="block-title">{{ #articlesBy# }} <span class="link-color">{{ $user->first_name}} {{ $user->last_name}}</span></div>
        <hr>
        {{ list_articles length="5" ignore_publication="true" ignore_issue="true" ignore_section="true" constraints="author is $escapedName type is news" order="bypublishdate desc" }}
        <article class="section-article archive-entry">
            {{ include file='_tpl/img/img_130x70.tpl'}}
            <header>
                <h2><a href="{{ $gimme->article->url }}">{{ $gimme->article->name }}</a></h2>
            </header>
            <span class="article-date">{{ $gimme->article->publish_date|camp_date_format:"%M %e, %Y" }}</span>
            {{ if !$gimme->article->content_accessible }}
            <span class="label label-important normal-weight">{{ #premium# }}</span>
            {{ /if }}
            <br>
            <span>{{ $gimme->article->comment_count }} {{ #comments# }}</span>
            <div class="clearfix"></div>
        </article>

        {{ if $gimme->current_list->at_end }}            

        {{* PAGINATION *}}
        {{ $pages=ceil($gimme->current_list->count/5) }}
        {{ $curpage=intval($gimme->url->get_parameter($gimme->current_list_id())) }}
        {{ if $pages gt 1 }}
        <nav class="span8">
            <div class="pagination">
                <ul>
                    {{ if $gimme->current_list->has_previous_elements }}<li class="prev"><a href="{{ uripath options="section" }}?{{ urlparameters options="previous_items" }}">&laquo;</a></li>{{ /if }}
                    {{ for $i=0 to $pages - 1 }}
                        {{ $curlistid=$i*5 }}
                        {{ $gimme->url->set_parameter($gimme->current_list_id(),$curlistid) }}
                        {{ if $curlistid != $curpage }}
                    <li><a href="{{ $view->url(['username' => $user->uname], 'user') }}?{{ urlparameters }}">{{ $i+1 }}</a></li>
                        {{ else }}
                    <li class="active disable"><a href="{{ $view->url(['username' => $user->uname], 'user') }}?{{ urlparameters }}">{{ $i+1 }}</a></li>
                        {{ $remi=$i+1 }}
                        {{ /if }}
                    {{ /for }}
                    {{ if $gimme->current_list->has_next_elements }}<li class="next"><a href="{{ uripath options="section" }}?{{ urlparameters options="next_items" }}">&raquo;</a></li>{{ /if }}
                </ul>
            </div>
        </nav>
        {{ $gimme->url->set_parameter($gimme->current_list_id(),$curpage) }}
        {{ /if }}

        {{ /if }}

        {{/list_articles}}
    </section>
    
</div>
