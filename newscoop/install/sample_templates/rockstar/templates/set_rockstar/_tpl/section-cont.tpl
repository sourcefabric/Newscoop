<div class="title">
  <h2>{{ #section# }} <span>{{ $gimme->section->name }}</span></h2>
</div>

{{ list_articles length="8" ignore_issue="true" constraints="type not poll" }}

{{ if $gimme->current_list->at_beginning }}
          <section class="grid-2">       
{{ /if }}     

{{ if $gimme->current_list->index lte 2 }}         
              <article>
                    {{ include file="_tpl/img/img_onehalf.tpl" }}
                    <small><a href="{{ url options="section" }}">{{ $gimme->section->name }}</a> {{ list_article_topics }}{{ if $gimme->current_list->at_beginning }}<em>/ {{ /if }}<a href="{{ url options="template topic.tpl" }}">{{ $gimme->topic->name }}</a>{{ if $gimme->current_list->at_end }}</em>{{ else }}, {{ /if }}{{ /list_article_topics }}</small>
                    <h3><a href="{{ url options="article" }}">{{ $gimme->article->name }}</a></h3>
                    <span class="date">{{ $gimme->article->publish_date|camp_date_format:"%M %d, %Y" }} /  {{ #by# }} {{ list_article_authors }}{{ if $gimme->author->user->defined }}<a href="{{ $view->url(['username' => $gimme->author->user->uname], 'user') }}">{{ /if }}{{ $gimme->author->name }}{{ if $gimme->author->user->defined }}</a>{{ /if }} ({{ $gimme->author->type|lower }}){{ if !$gimme->current_list->at_end }}, {{ /if }}{{ /list_article_authors }}</span>
                    <p>{{ include file="_tpl/_edit-article.tpl" }}{{ if $gimme->article->type_name == "debate" }}{{ $gimme->article->teaser }}{{ else }}{{ $gimme->article->deck }}{{ /if }}</p>
                    <span class="more"><a href="{{ url options="article" }}">+  {{ #readMore# }}</a> or <a href="{{ url options="article" }}#comments">{{ #addComment# }} ({{ $gimme->article->comment_count }})</a></span>
                </article>
{{ /if }}
                
{{ if $gimme->current_list->index == 2 || ($gimme->current_list->at_end && $gimme->current_list->index lte 2) }}            
            </section><!-- / 2 article grid -->
{{ /if }}

{{ if $gimme->current_list->index == 3 }}             
            <div class="title">
              <h2>MORE <span>STORIES</span></h2>
            </div>
            
            <section class="grid-3">
{{ /if }}    
{{ if $gimme->current_list->index gte 3 }}        
              <article>
                    {{ include file="_tpl/img/img_onethird.tpl" }}
                    <small><a href="{{ url options="section" }}">{{ $gimme->section->name }}</a> {{ list_article_topics }}{{ if $gimme->current_list->at_beginning }}<em>/ {{ /if }}<a href="{{ url options="template topic.tpl" }}">{{ $gimme->topic->name }}</a>{{ if $gimme->current_list->at_end }}</em>{{ else }}, {{ /if }}{{ /list_article_topics }}</small>
                    <h4><a href="{{ url options="article" }}">{{ $gimme->article->name }}</a></h4>
                    <span class="date">{{ $gimme->article->publish_date|camp_date_format:"%M %d, %Y" }} /  {{ #by# }} {{ list_article_authors }}{{ if $gimme->author->user->defined }}<a href="{{ $view->url(['username' => $gimme->author->user->uname], 'user') }}">{{ /if }}{{ $gimme->author->name }}{{ if $gimme->author->user->defined }}</a>{{ /if }} ({{ $gimme->author->type|lower }}){{ if !$gimme->current_list->at_end }}, {{ /if }}{{ /list_article_authors }}</span>
                    <p>{{ include file="_tpl/_edit-article.tpl" }}{{ $gimme->article->deck }}</p>
                    <span class="more"><a href="{{ url options="article" }}">+  {{ #readMore# }}</a> or <a href="{{ url options="article" }}#comments">{{ #addComment# }} ({{ $gimme->article->comment_count }})</a></span>
                </article>
{{ /if }}                
{{ if $gimme->current_list->at_end && !($gimme->current_list->index == 2) }}              
            </section><!-- / 3 articles grid -->  
{{ /if }}           
{{ if $gimme->current_list->at_end }}

{{* PAGINATION *}}
{{ $pages=ceil($gimme->current_list->count/8) }}
{{ $curpage=intval($gimme->url->get_parameter($gimme->current_list_id())) }}
{{ if $pages gt 1 }}
<div class="paging-holder">
    {{ if $gimme->current_list->has_previous_elements }}<a href="{{ uripath options="section" }}?{{ urlparameters options="previous_items" }}" class="prev"><span>+ {{ #previous# }}</span> {{ #page# }}</span></a>{{ /if }}
    <span class="paging">
    {{ for $i=0 to $pages - 1 }}
        {{ $curlistid=$i*8 }}
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
            
{{ /list_articles }}            