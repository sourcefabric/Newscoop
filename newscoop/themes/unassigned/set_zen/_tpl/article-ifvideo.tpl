{{***************************************************************

this template delivers videos for desktop and mobile devices.
it follows these assumptions:

- video files have one of the extensions:
  .mp4
  .ogv (OGG Theora)
  .flv

delivery to mobile:
- do not show flash videos
- list all other formats with download links

delivery to desktop:
- show all flash videos
- if flash and *other* formats are attached, show only flash (assumption: *other* = mobile)

**************************************************************}}

{{ if $gimme->article->has_attachments }}

{{ assign var="flashcounter" value=0 }}{{* used to include JS for Flash Player only once *}}

{{ list_article_attachments }}
{{ if ($gimme->attachment->extension == flv) || ($gimme->attachment->extension == m4v) || ($gimme->attachment->extension == ogv) || ($gimme->attachment->extension == mp4) }}

{{ if ($gimme->attachment->extension == flv) && ($gimme->browser->ua_type != "mobile") }}
<div class="video-entity"> 
  {{ assign var="flashcounter" value=$flashcounter+1 }}{{* flash video found = count up *}}
  {{* START FLASH PLAYER ************************************************************}}
  {{ if $flashcounter == 1 }}
    <!-- flowplayer scripts should be loaded from your servers NOT from static.flowplayer.org  --> 
    <script type="text/javascript" src="{{ url static_file='_tpl/player/flowplayer-3.2.6.min.js' }}"></script> 
  {{ /if }} 

  <!-- this A tag is where your Flowplayer will be placed. it can be anywhere -->
  <a  
    href="{{ uri options="articleattachment" }}"
    style="display:block;width:708px;height:399px"  
    id="player-{{ $gimme->current_list->index }}"> 
  </a> 
  
  <!-- this script block will install Flowplayer inside previous A tag --> 
  <script language="JavaScript"> 
    flowplayer("player-{{ $gimme->current_list->index }}", "{{ url static_file='_tpl/player/player.swf' }}");
  </script>

        <div class="caption video-attachment-description">{{ $gimme->attachment->description }}</div>    
</div><!-- /.video-entity-->  
{{ /if }}{{* end FLASH player *}}

{{ if ($gimme->attachment->extension == ogv) || ($gimme->attachment->extension == m4v) || ($gimme->attachment->extension == mp4) }}
  {{ if ($gimme->browser->ua_type == "mobile")}}
    <div class="video-entity"> 
        <h4>VIDEO: {{ $gimme->attachment->description }}</h4>
        <a href="{{ uri options="articleattachment" }}">Download .{{ $gimme->attachment->extension }} file</a>
    </div><!-- /.video-entity-->
  {{ else }}
    {{ if $flashcounter == 0 }}{{* if we already delivered flash to desktop, do not show alternative formats *}}
      <div class="video-entity"> 
        <video src="{{ uri options="articleattachment" }}" controls width="100%">
          <a href="{{ uri options="articleattachment" }}">Download .{{ $gimme->attachment->extension }} file</a>
        </video>
        <div class="caption video-attachment-description">
          {{ $gimme->attachment->description }}<br />
          <a href="{{ uri options="articleattachment" }}">Download .{{ $gimme->attachment->extension }} file</a>
        </div> 
      </div><!-- /.video-entity-->
    {{ /if }}
  {{ /if }}
{{ /if }}{{* end HTML5 player for OGG / MP4 *}}


{{ /if }}{{* end check IF videos *}}

{{ /list_article_attachments }}

{{ /if }}