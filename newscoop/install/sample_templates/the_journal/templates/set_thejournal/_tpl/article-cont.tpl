{{ include file="_tpl/article-author-popup.tpl" }}

                    <h2 class="post-title">{{ $gimme->article->name }}</h2>
                    <p class="post-details">Published on {{ $gimme->article->publish_date|camp_date_format:"%e %M %Y" }} in <a href="{{ uri options="section" }}" title="View all posts in {{ $gimme->section->name }}" rel="category tag">{{ $gimme->section->name }}</a><br />
                    By: {{ list_article_authors }}<a href="#TB_inline?height=350&width=350&inlineId=hidden{{ $gimme->current_list->index }}Content&modal=true" class="thickbox" title="{{ $gimme->author->name }} Bio">{{ $gimme->author->name }}</a> ({{ $gimme->author->type|lower }}){{ if !$gimme->current_list->at_end }}, {{ /if }}{{ /list_article_authors }}
<br />
                    Location(s): {{ list_article_locations }}{{ if
$gimme->location->enabled }}{{ $gimme->location->name }}{{ if $gimme->current_list->at_end }}{{ else }}, {{ /if }}{{ /if }}{{ /list_article_locations }}</p>
{{ if $gimme->article->content_accessible }}
                    {{ if $gimme->article->has_image(1) }}<img src="{{ url options="image 1 width 280" }}" alt="{{ $gimme->article->image1->description }}" class="woo-image thumbnail" />{{ elseif $gimme->article->has_image(2)}}<img src="{{ url options="image 2 width 280" }}" alt="{{ $gimme->article->image2->description }}" class="woo-image thumbnail" />{{ /if }}
                    <div class="full_text">{{ $gimme->article->full_text }}</div>
{{ else }}
                    <p style="margin: 30px 0; font-style: italic; text-align: center">This article is accessible only to registered and logged in users!</p>
{{ /if }}                    
           <div class="fix"></div>  

           {{*<p class="tags">Tags: <a href="" rel="tag">blockquote</a>, <a href="" rel="tag">done</a>, <a href="" rel="tag">edit</a>, <a href="" rel="tag">elements</a>, <a href="" rel="tag">featured</a>, <a href="" rel="tag">h1</a>, <a href="" rel="tag">h2</a>, <a href="" rel="tag">h3</a>, <a href="" rel="tag">h4</a>, <a href="" rel="tag">h5</a>, <a href="" rel="tag">h6</a>, <a href="" rel="tag">lists</a>, <a href="" rel="tag">test</a></p>*}}