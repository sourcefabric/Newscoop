<div class="title">
  <h2>{{ #searchResults# }}</h2>
</div>  

{{ list_search_results length="9" columns="3" }}

{{ if $gimme->current_list->column == 1 }}            
            <section class="grid-3">
{{ /if }}    
        
              <article>
                    {{ include file="_tpl/img/img_onethird.tpl" }}
                    <small><a href="{{ url options="section" }}">{{ $gimme->section->name }}</a> {{ list_article_topics }}{{ if $gimme->current_list->at_beginning }}<em>/ {{ /if }}<a href="{{ url options="template topic.tpl" }}">{{ $gimme->topic->name }}</a>{{ if $gimme->current_list->at_end }}</em>{{ else }}, {{ /if }}{{ /list_article_topics }}</small>
                    <h4><a href="{{ url options="article" }}">{{ $gimme->article->name }}</a></h4>
                    <span class="date">{{ $gimme->article->publish_date|camp_date_format:"%M %d, %Y" }} / {{ #by# }} {{ list_article_authors }}{{ if $gimme->author->user->defined }}<a href="{{ $view->url(['username' => $gimme->author->user->uname], 'user') }}">{{ /if }}{{ $gimme->author->name }}{{ if $gimme->author->user->defined }}</a>{{ /if }} ({{ $gimme->author->type|lower }}){{ if !$gimme->current_list->at_end }}, {{ /if }}{{ /list_article_authors }}</span>
                    <p>{{ include file="_tpl/_edit-article.tpl" }}{{ $gimme->article->deck }}</p>
                    <span class="more"><a href="{{ url options="article" }}">+  {{ #readMore# }}</a> or <a href="{{ url options="article" }}#comments">{{ #addComment# }} ({{ $gimme->article->comment_count }})</a></span>
                </article>
                
{{ if ($gimme->current_list->column == 3) || $gimme->current_list->at_end }}              
            </section><!-- / 3 articles grid -->  
            
{{ if $gimme->current_list->at_end }} 
{{* PAGINATION *}}
{{ $pages=ceil($gimme->current_list->count/9) }}
{{ $curpage=intval($gimme->url->get_parameter($gimme->current_list_id())) }}
{{ if $pages gt 1 }}
<div class="paging-holder">
    {{ if $gimme->current_list->has_previous_elements }}<a href="{{ uripath options="section" }}?{{ urlparameters options="previous_items" }}" class="prev"><span>+ {{ #previous# }}</span> {{ #page# }}</span></a>{{ /if }}
    <span class="paging">
    {{ for $i=0 to $pages - 1 }}
        {{ $curlistid=$i*9 }}
        {{ $gimme->url->set_parameter($gimme->current_list_id(),$curlistid) }}
        <a{{ if $curlistid == $curpage }} class="active"{{ /if }} href="{{ uripath options="section" }}?{{ urlparameters }}">{{ $i+1 }}</a></li>
        {{ $remi=$i+1 }}
    {{ /for }}
    </span>
    {{ if $gimme->current_list->has_next_elements }}<a href="{{ uripath options="section" }}?{{ urlparameters options="next_items" }}"  class="next"><span>{{ #next# }}</span> {{ #page# }} <span>+</span></a>{{ /if }}
</div><!-- / Pagination -->
{{ $gimme->url->set_parameter($gimme->current_list_id(),$curpage) }}
{{ /if }}
{{ /if }}

{{ /if }}
            
{{ /list_search_results }}     

{{ if $gimme->prev_list_empty }}
<section class="grid-3">       
<p>{{ #noSearchResults# }}</p>
{{ assign var="incl-all-sec" value=1 }}
</section>
{{ /if }}