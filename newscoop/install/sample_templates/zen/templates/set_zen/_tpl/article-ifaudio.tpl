{{ if $gimme->article->has_attachments }}

{{ assign var="counter" value=0 }}

{{ list_article_attachments }}
{{ if ($gimme->attachment->extension == mp3) || ($gimme->attachment->extension == oga) }}

{{ if $gimme->browser->ua_type != "mobile" }}{{* build desktop player *}}

  {{ assign var="counter" value=$counter+1 }}
  {{ if $counter == 1 }}
    <!-- flowplayer scripts should be loaded from your servers NOT from static.flowplayer.org  --> 
    <script type="text/javascript" src="{{ url static_file='_tpl/player/flowplayer-3.2.6.min.js' }}"></script> 
  {{ /if }}
  <div class="audio-entity">
  {{ if $gimme->attachment->extension == oga }} {{* HTML5 player for OGG *}}
    <audio controls>
      <source src="{{ uri options="articleattachment" }}" type="{{ $gimme->attachment->mime_type }}">
    </audio>
  {{ else }} {{* Flash player for MP3 *}}
    <!-- setup player container  -->
    <div id="audio" style="display:block;width:100%;height:30px;"
      href="{{ uri options="articleattachment" }}"></div>
    
    <!-- this script block will install Flowplayer inside previous A tag --> 
    <script language="JavaScript"> 
      flowplayer("audio", "{{ url static_file='_tpl/player/player.swf' }}", {
      // fullscreen button not needed here
      plugins: {
        controls: {
          fullscreen: false,
          height: 30,
          autoHide: false
        }
      },

      clip: {
        autoPlay: false,

        // optional: when playback starts close the first audio playback
        onBeforeBegin: function() {
          $f("player").close();
        }
      }

    });
    </script>
  {{ /if }} {{* end Flash player for MP3 *}}
      <div class="caption audio-attachment-description">{{ $gimme->attachment->description }}</div> 
    </div><!-- /.audio-entity-->
{{ else }}{{* end player for desktop, start mobile *}}
    <div class="audio-entity"> 
        <h4>AUDIO: {{ $gimme->attachment->description }}</h4>
        <a href="{{ uri options="articleattachment" }}">Download .{{ $gimme->attachment->extension }} file</a>
    </div><!-- /.audio-entity-->
{{ /if }}
    
{{ /if }}   

{{ /list_article_attachments }}

{{ /if }}