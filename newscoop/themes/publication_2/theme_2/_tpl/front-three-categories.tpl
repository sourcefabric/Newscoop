<div id="categories-module"> 

{{ local }}
{{ list_articles length="3" ignore_section="true" constraints="onsection is on" }}

    <div class="category-box">
        <p class="category"><a href="{{ uri options="section" }}" title="View all posts in {{ $gimme->section->name }}" rel="category tag">{{ $gimme->section->name }}</a></p>    
        <div class="category-image-block"><a title="{{ $gimme->article->name }}" href="{{ uri options="article" }}"><img src="{{ if $gimme->article->has_image(2) }}{{ uri options="image 2 width 134"}}{{ else }}{{ uri options="image 1 width 134"}}{{ /if }}" alt="{{ $gimme->article->image->description }}" class="woo-image thumbnail"></a></div>
        <h3><a href="{{ uri options="article" }}">{{ if ! $gimme->article->content_accessible }}* {{ /if }}{{ $gimme->article->name }}</a></h3>
        <div class="fix"></div>
    </div>
              
{{ /list_articles }}
{{ /local }}              
                                                               
    <p>Go to the <a href="{{ uri options="template archive.tpl" }}">Archives</a> to see more entries</p>  
                                            
</div>