          <h2 class="arh">Articles by {{ $gimme->article->author->name }}</h2>

{{ list_articles length="5" ignore_issue="true" ignore_section="true" order="bypublishdate desc" constraints="author is __current type is news" }}

                <div class="post wrap">

                    <h2 class="post-title"><a href="{{ uri options="article" }}" rel="bookmark" title="{{ $gimme->article->name }}">{{ $gimme->article->name }}</a></h2>
                    <p class="post-details">Posted on {{ $gimme->article->publish_date|camp_date_format:"%e. %M, %Y" }} in <a href="" title="View all posts in {{ $gimme->section->name }}" rel="category tag">{{ $gimme->section->name }}</a></p>
                    <div class="category-image-block"><a title="{{ $gimme->article->name }}" href="{{ uri options="article" }}"><img src="{{ if $gimme->article->has_image(2) }}{{ uri options="image 2 width 134"}}{{ else }}{{ uri options="image 1 width 134"}}{{ /if }}" alt="{{ $gimme->article->image->description }}" class="woo-image thumbnail"></a></div>                    
                    <p>{{ $gimme->article->deck }}</p>

                </div>
                
                <div class="hr"></div>
                     
{{ if $gimme->current_list->at_end }}                                                 
                                                    
                <div class="more_entries">
                    <div class="alignleft">{{ if $gimme->current_list->has_previous_elements }}<a href="{{ uripath options="template the_journal/author.tpl" }}?{{ urlparameters options="previous_items" }} ">&laquo; Newer Entries</a>{{ /if }}</div>
                    <div class="alignright">{{ if $gimme->current_list->has_next_elements }}<a href="{{ uripath options="template the_journal/author.tpl" }}?{{ urlparameters options="next_items" }}">Older Entries &raquo;</a>{{ /if }}</div>
                    <br class="fix" />
                </div>    
{{ /if }}     
{{ /list_articles }}                    