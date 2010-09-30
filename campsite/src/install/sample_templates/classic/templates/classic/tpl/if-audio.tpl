{{ if $campsite->article->has_attachments }}

<style>
  .audio-entity {
   width: 250px;
   margin: 10px 0;  
   padding: 10px 0; 
   clear: both; 
   border-top: 1px solid #999;
   border-bottom: 1px solid #999;  
  }  
 
.audio-entity .audio-attachment-description {
  font-style: italic;
  margin-top: 10px; 
  clear: both; 
}  
</style>

{{ assign var="counter" value=0 }}

{{ list_article_attachments }}
{{ if ($campsite->attachment->extension == mp3) || ($campsite->attachment->extension == ogg) }}
{{ assign var="counter" value=$counter+1 }}

{{ if $counter == 1 }}
    <div class="audio-entity"> 
     <h4>{{ if $campsite->language->name == "English" }}Listen to audio attachments{{ else }}Escucha a los accesorios de audio{{ /if }}</h4> 
     <script type="text/javascript" src="/templates/classic/tpl/player/flowplayer-3.2.4.min.js"></script>
{{ /if }}

{{*
  <audio controls>
    <source src="http://{{ $campsite->publication->site }}{{ uri options="articleattachment" }}" type="{{ $campsite->attachment->mime_type }}">
  </audio>
*}}

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>
<div id="player-holder"></div>

<script type="text/javascript">
  var options = {};
  options.mediaPath = "http://{{ $campsite->publication->site }}{{ uri options="articleattachment" }}";
  
  var params = {};
  params.allowScriptAccess = "always";
  
  swfobject.embedSWF("/templates/classic/tpl/player/player.swf", "player-holder", "250", "21", "9.0.0",false, options, {}, {});
</script>


       <div class="audio-attachment-description">{{ $campsite->attachment->description }}</div> 
    
{{ /if }}    

{{ if $campsite->current_article_attachments_list->at_end }}  
{{ if $counter gt 0 }}     
    </div><!-- /.audio-entity-->
{{ /if }}
{{ /if }}

{{ /list_article_attachments }}

{{ /if }}