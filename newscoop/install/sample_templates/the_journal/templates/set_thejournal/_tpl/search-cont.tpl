      <div id="main">
          <h2 class="arh">Search results</h2>
                                                                                    
{{ list_search_results length="5" order="bypublishdate desc" constraints="type is news" }}

                <div class="post wrap">
                    <h2 class="post-title"><a href="{{ uri options="article" }}" rel="bookmark" title="{{ $gimme->article->name }}">{{ if ! $gimme->article->content_accessible }}* {{ /if }}{{ $gimme->article->name }}</a></h2>
                    <p class="post-details">Published on {{ $gimme->article->publish_date|camp_date_format:"%e %M %Y" }} by  {{*<a href="" title="Posts by {{ $gimme->article->author->name }}">*}}{{ $gimme->article->author->name }}{{*</a>*}} in <a href="{{ uri options="section" }}">{{ $gimme->section->name }}</a></p> 
                    <div class="category-image-block"><a title="{{ $gimme->article->name }}" href="{{ uri options="article" }}"><img src="{{ if $gimme->article->has_image(2) }}{{ uri options="image 2 width 134"}}{{ else }}{{ uri options="image 1 width 134"}}{{ /if }}" alt="{{ $gimme->article->image->description }}" class="woo-image thumbnail"></a></div>                    
                    <p>{{ $gimme->article->deck }}</p>
                </div><!-- /.post -->
                
                <div class="hr"></div>
                                
{{ if $gimme->current_list->at_end }}                                                 
                                                    
                <div class="more_entries">
                    <div class="alignleft">{{ if $gimme->current_list->has_previous_elements }}<a href="{{ uripath options="template search.tpl" }}?{{ urlparameters options="previous_items" }} ">&laquo; Newer Entries</a>{{ /if }}</div>
                    <div class="alignright">{{ if $gimme->current_list->has_next_elements }}<a href="{{ uripath options="template search.tpl" }}?{{ urlparameters options="next_items" }}">Older Entries &raquo;</a>{{ /if }}</div>
                    <br class="fix" />
                     
                </div>    
{{ /if }}                
{{ /list_search_results }}                   
                
{{ if $gimme->prev_list_empty }}
      <div class="postinformation">No results found</div>
{{ /if }}                
                
            </div><!-- main ends -->