            <aside>

{{* This part builds the article gallery. There can also be more than one gallery *}} 
{{ foreach $gimme->article->slideshows as $slideshow }}

          <div class="gallery aside-box">
              <h2>{{ #articleGallery# }}</h2>
              <h5>{{ $slideshow->headline }}</h5>
{{ assign var="counter" value=0 }}              
{{ foreach $slideshow->items as $item }}      
{{ assign var="counter" value=$counter+1 }}
                <a href="http://{{ $gimme->publication->site }}/{{ $item->image->original }}" rel="gallery" class="threecol gallery_thumbnail{{ if $counter%3 == 0 }} last{{ /if }}" title="{{ $item->caption }}" /><img src="{{ $item->image->src }}" width="90" height="90" alt="{{ $item->caption }}" style="max-width: 100%" rel="resizable" /></a>                         
{{ /foreach }}
            </div><!-- /#gallery -->

{{ /foreach }}

{{* this creates article map with markers for selected POIs *}}        
{{ if $gimme->article->has_map }}           
            	<h2>{{ #storyPlaces# }}</h2>
                <div class="aside-box">
                	{{ map show_locations_list="false" show_big_map="false" show_reset_link="false" width="290" height="195" }}
                </div>
{{ /if }}                

{{* here we work with article attachments. .oga and .ogv/.ogg files are automatically shown with player in html5 enabled browsers (for video we are including videojs.com's HTML5 player which also plays mp4 and webm formats), all other cases just build the link for download *}}           
{{ if $gimme->article->has_attachments }} 
{{ list_article_attachments }}
{{ if $gimme->attachment->extension == oga }}           

            <div class="audio-attachment aside-box">
              <h2>{{ #listenAudio# }}</h2>
                <audio src="{{ uri options="articleattachment" }}" width="290" controls>
              <a href="{{ uri options="articleattachment" }}">{{ #downloadAudioFile# }}</a>
              </audio>
            </div><!-- /.audio-attachment -->
            
{{ elseif $gimme->attachment->extension == ogv || $gimme->attachment->extension == ogg || $gimme->attachment->extension == mp4 || $gimme->attachment->extension == webm }}             

            <div class="video-attachment aside-box"><!-- read http://diveintohtml5.org/video.html -->
              <h2>{{ #watchVideo# }}</h2>
              <video id="video_{{ $gimme->current_list->index }}" class="video-js vjs-default-skin" controls
                preload="auto" width="100%"
                data-setup="{}">
              <source src="{{ uri options="articleattachment" }}" type='{{ $gimme->attachment->mime_type }}'>
              <a href="{{ uri options="articleattachment" }}">{{ #download# }} .{{ $gimme->attachment->extension }} {{ #file# }}</a>
             </video>

      </div><!-- /#video-attachment --> 
      
{{ else }}

      <div class="attachment aside-box">
          <h2>{{ #downloadFile# }}</h2>
          <p>{{ #fileOfType# }} {{ $gimme->attachment->mime_type }}</p>
          <a href="{{ uri options="articleattachment" }}">{{ $gimme->attachment->file_name }} ({{ $gimme->attachment->size_kb }}kb)</a>
          <p><em>{{ $gimme->attachment->description }}</em></p>
      </div><!-- /.attachment -->
{{ /if }}      
{{ /list_article_attachments }}      
{{ /if }}  

{{* here we include debate voting tool, if article type is 'debate' *}}
{{ if $gimme->article->type_name == "debate" }}
{{ include file="_tpl/debate-voting.tpl" }}
{{ /if }}               
                
                <h2>{{ #alsoIn# }} <span>{{ $gimme->section->name }}</span></h2>
                <div class="aside-box">
                    <ul class="article-list">
{{ assign var="curart" value=$gimme->article->number }}        
{{ list_articles length="5" ignore_issue="true" order="bypublishdate desc" constraints="number not $curart" }}
                    <li><h4><a href="{{ url options="article" }}">{{ $gimme->article->name }}</a></h4></li>
{{ /list_articles }}
                    </ul>
                </div>

{{ list_related_articles }}
{{ if $gimme->current_list->at_beginning }}
                <h2>{{ #relatedStories# }}</h2>
                <section class="grid-3">
{{ /if }}        			 
                    <article>
                        {{ include file="_tpl/img/img_onethird.tpl" }}
                        <small><a href="{{ url options="section" }}">{{ $gimme->section->name }}</a> {{ list_article_topics }}{{ if $gimme->current_list->at_beginning }}<em>/ {{ /if }}<a href="{{ url options="template topic.tpl" }}">{{ $gimme->topic->name }}</a>{{ if $gimme->current_list->at_end }}</em>{{ else }}, {{ /if }}{{ /list_article_topics }}</small>
                        <h4><a href="{{ url options="article" }}">{{ $gimme->article->name }}</a></h4>
                        <span class="time">{{ include file="_tpl/relative_date.tpl" date=$gimme->article->publish_date }} / <a href="{{ url options="article" }}#comments">{{ $gimme->article->comment_count }} {{ #comments# }}</a></span>
                    </article>
{{ if $gimme->current_list->at_end }}     
                </section>
{{ /if }}                    
{{ /list_related_articles }}
				            
            </aside><!-- / Aside -->