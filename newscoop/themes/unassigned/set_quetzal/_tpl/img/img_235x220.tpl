{{ image rendition="multimedia"}}
{{ if $where=='slideshow'}}
<a href="{{ uri options="article" }}" class="pull-left sub-item photo">
    <img src="{{ $image->src }}" alt="uno">
    <div class="photo-caption">
        <i class="icon-camera icon-white"></i>
    </div>
</a>
{{else}}
<a href="{{ uri options="article" }}#video-cont-label" class="pull-left sub-item video">
  <img style="background: url({{ $image->src }});background-position: center top;" src="{{url static_file='_img/player.png'}}" alt="uno">
  <div class="photo-caption">
    {{ if $where=="audio" }}
    <i class="icon-headphones icon-white"></i>
    {{ else }}
    <i class="icon-film icon-white"></i>
    {{ /if }}
  </div>
</a>
{{/if}}
{{ /image }}
