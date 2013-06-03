{{ include file="_tpl/sidebar-loginbox.tpl" }} 

{{* Only if user has the right to read the article, aside elements will be shown. The same for article content. *}}
{{ if $gimme->article->content_accessible }}

{{* This part builds the article gallery. There can also be more than one gallery *}} 
{{ foreach $gimme->article->slideshows as $slideshow }}

          <div id="gallery" class="clearfix">
              <h3>{{ #articleGallery# }}</h3>
              <h4>{{ $slideshow->headline }}</h4>
{{ assign var="counter" value=0 }}              
{{ foreach $slideshow->items as $item }}      
{{ assign var="counter" value=$counter+1 }}
                <a href="http://{{ $gimme->publication->site }}/{{ $item->image->original }}" rel="gallery" class="threecol gallery_thumbnail{{ if $counter%4 == 0 }} last{{ /if }}" title="{{ $item->caption }}" /><img src="{{ $item->image->src }}" width="{{ $item->image->width }}" height="{{ $item->image->height }}" alt="{{ $item->caption }}" style="max-width: 100%" rel="resizable" /></a>                         
{{ /foreach }}
            </div><!-- /#gallery -->

{{ /foreach }}
        
{{* this creates article map with markers for selected POIs *}}        
{{ if $gimme->article->has_map }}         
            <figure id="map-box">
                <h3>{{ #map# }}</h3>
                {{ map show_locations_list="false" show_reset_link="Show initial Map" width="350" height="300" }}
            </figure>  
{{ /if }}

{{* here we work with article attachments. .oga and .ogv/.ogg files is automatically shown with player in html5 enabled browsers (for video we are including videojs.com's HTML5 player which also plays mp4 and webm formats), all other cases just build the link for download *}}           
{{ if $gimme->article->has_attachments }} 
{{ list_article_attachments }}
{{ if $gimme->attachment->extension == oga }}           

            <div class="audio-attachment">
              <h3>{{ #listen# }}</h3>
                <audio src="{{ uri options="articleattachment" }}" width="100%" controls>
              <a href="{{ uri options="articleattachment" }}">{{ #downloadAudioFile# }}</a>
              </audio>
            </div><!-- /#audio-attachment -->
            
{{ elseif $gimme->attachment->extension == ogv || $gimme->attachment->extension == ogg || $gimme->attachment->extension == mp4 || $gimme->attachment->extension == webm }}             

            <div class="video-attachment"><!-- read http://diveintohtml5.org/video.html -->
              <h3>{{ #watch# }}</h3>
              <video id="video_{{ $gimme->current_list->index }}" class="video-js vjs-default-skin" controls
                preload="auto" width="100%"
                data-setup="{}">
              <source src="{{ uri options="articleattachment" }}" type='{{ $gimme->attachment->mime_type }}'>
              <a href="{{ uri options="articleattachment" }}">{{ #download# }} .{{ $gimme->attachment->extension }} {{ #file# }}</a>
             </video>

      </div><!-- /#video-attachment --> 
      
{{ else }}

      <div class="attachment">
          <h3>{{ #download# }}</h3>
          <p>{{ #fileOfType# }} {{ $gimme->attachment->mime_type }}</p>
          <a href="{{ uri options="articleattachment" }}">{{ $gimme->attachment->file_name }} ({{ $gimme->attachment->size_kb }}kb)</a>
          <p><em>{{ $gimme->attachment->description }}</em></p>
      </div><!-- /.attachment -->
{{ /if }}      
{{ /list_article_attachments }}      
{{ /if }}                 

{{ /if }}{{* end of $gimme->article->content_accessible *}}

{{* here we include debate voting tool, if article type is 'debate' *}}
{{ if $gimme->article->type_name == "debate" }}
{{ include file="_tpl/debate-voting.tpl" }}

{{ else }}

{{* here we show short bio of article authors for article of non-debate type *}}
{{ list_article_authors }} 
{{ if $gimme->current_list->at_beginning }}            
            <div id="author-box">
              <h3>{{ #aboutAuthor# }}</h3>
{{ /if }}              
                <article class="clearfix">
                	<figure class="threecol">
                    	<img rel="resizable" style="max-width:100%;" alt="{{ $gimme->author->name }}" src="{{ $gimme->author->picture->imageurl }}" />
                	</figure>
                    <div class="ninecol last">
                	<h4>{{ if $gimme->author->user->defined }}<a href="{{ $view->url(['username' => $gimme->author->user->uname], 'user') }}">{{ /if }}{{ $gimme->author->name }}{{ if $gimme->author->user->defined }}</a>{{ /if }}</h4>
                  
                	<p>{{ $gimme->author->biography->text|strip_tags:false|truncate:200 }}</p>
                    </div>
                </article>

{{ if $gimme->current_list->at_end }}                             
            </div><!-- /#author-box -->
{{ /if }}
{{ /list_article_authors }}            

{{ /if }}
                
{{* related content *}}                
            <div id="related">
            
{{ list_related_articles }}
{{ if $gimme->current_list->at_beginning }}
                <h3>{{ #relatedArticles# }}</h3>
        			 <ul>
{{ /if }}        			 
                    <li><a href="{{ uri options="article" }}">{{ $gimme->article->name }}</a></li>
{{ if $gimme->current_list->at_end }}     
                </ul>
{{ /if }}                    
{{ /list_related_articles }}                                               
            
            
                <h3>{{ #moreInThisSection# }}</h3>
        <ul>
{{ assign var="curart" value=$gimme->article->number }}        
{{ list_articles length="5" ignore_issue="true" order="bypublishdate desc" constraints="number not $curart" }}
                    <li><a href="{{ uri options="article" }}">{{ $gimme->article->name }}</a></li>
{{ /list_articles }}                    
                </ul>                
            </div><!-- /#related -->