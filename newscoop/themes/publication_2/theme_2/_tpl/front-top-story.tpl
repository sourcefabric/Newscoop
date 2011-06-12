{{ local }}
{{ list_articles length="1" ignore_section="true" order="bypublishdate desc" constraints="onfrontpage is on" }}            

<div id="featured_photo" class="threecol_one">
                <a title="{{ $gimme->article->name }}" href="{{ uri options="article" }}"><img src="{{ uri options="image 1 width 293" }}" alt="{{ $gimme->article->image->description }}" class="woo-image thumbnail"></a>
                <div>{{ $gimme->article->image->description }}</div>  
            </div>
            
            <div class="threecol_two">            
                <div id="featured_post">
                    <h2><a href="{{ uri options="article" }}" rel="bookmark" title="{{ $gimme->article->name }}">{{ if ! $gimme->article->content_accessible }}* {{ /if }}{{ $gimme->article->name }}</a></h2>
                    <p>{{ $gimme->article->deck }}</p>      
                </div>
                
{{ /list_articles }}
{{ /local }}                
                
{{ include file="_tpl/front-tabs.tpl" }}

            </div>