          <h2 class="arh">Section: {{ $gimme->section->name }} <small>({{ $gimme->issue->name }})</small></h2>

{{ list_articles }}

                <div class="post wrap">

                    <h2 class="post-title"><a href="{{ uri options="article" }}" rel="bookmark" title="{{ $gimme->article->name }}">{{ $gimme->article->name }}</a></h2>
                    <p class="post-details">Posted on {{ $gimme->article->publish_date|camp_date_format:"%e. %M, %Y" }} by  {{*<a href="{{ uri options="template author.tpl" }}" title="Posts by {{ $gimme->article->author->name }}">*}}{{ $gimme->article->author->name }}</a> in <a href="" title="View all posts in {{ $gimme->section->name }}" rel="category tag">{{ $gimme->section->name }}</a></p>
                    <div class="category-image-block"><a title="{{ $gimme->article->name }}" href="{{ uri options="article" }}"><img src="{{ if $gimme->article->has_image(2) }}{{ uri options="image 2 width 134"}}{{ else }}{{ uri options="image 1 width 134"}}{{ /if }}" alt="{{ $gimme->article->image->description }}" class="woo-image thumbnail"></a></div>                    
                    <p>{{ $gimme->article->deck }}</p>

                </div>
                
                <div class="hr"></div>
                                                    
{{ /list_articles }}

                <div class="more_entries">
                    <div class="alignleft"></div>
                    <div class="alignright"></div>
                    <br class="fix" />
                     
                </div>  