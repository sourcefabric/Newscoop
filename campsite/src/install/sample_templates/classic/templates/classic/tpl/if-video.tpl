{{ if $campsite->article->has_attachments }}

<style>
  .video-entity {
   width: 520px;
   margin: 10px 0;  
   padding: 10px 0; 
   clear: both; 
   border-top: 1px solid #999;
   border-bottom: 1px solid #999;  
  }  
 
.video-entity .video-attachment-description {
  font-style: italic;
  margin-top: 10px; 
  clear: both; 
}  
</style>

{{ assign var="counter" value=0 }}

{{ list_article_attachments }}
{{ if ($campsite->attachment->extension == flv) || ($campsite->attachment->extension == mpg) || ($campsite->attachment->extension == avi) }}
{{ assign var="counter" value=$counter+1 }}

{{ if $counter == 1 }}
    <div class="video-entity"> 
     <h4>{{ if $campsite->language->name == "English" }}Watch video attachments{{ else }}Ver archivos adjuntos de vídeo{{ /if }}</h4> 
     <script type="text/javascript" src="/templates/classic/tpl/player/flowplayer-3.2.4.min.js"></script>
{{ /if }}

{{* <video controls width="520" height="330">
      <source src="http://{{ $campsite->publication->site }}{{ uri options="articleattachment" }}" type="{{ $campsite->attachment->mime_type }}">
    </video>    *}}
    <a  
       href="http://{{ $campsite->publication->site }}{{ uri options="articleattachment" }}"  
       style="display:block;width:520px;height:330px"  
       id="video-holder"> 
    </a>

    <script>
      flowplayer("video-holder", "/templates/classic/tpl/player/flowplayer-3.2.4.swf");
    </script> 

       <div class="video-attachment-description">{{ $campsite->attachment->description }}</div> 
    
{{ /if }}   
 
{{ if $campsite->current_article_attachments_list->at_end }}  
{{ if $counter gt 0 }}     
    </div><!-- /.audio-entity-->
{{ /if }}
{{ /if }}
{{ /list_article_attachments }}

{{ /if }}