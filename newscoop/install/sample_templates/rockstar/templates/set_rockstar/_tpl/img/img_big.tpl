{{ image rendition="big" }}
    <img src="{{ $image->src }}" width="{{ $image->width }}" height="{{ $image->height }}" rel="resizable" style="max-width: 100%" alt="{{ $image->caption }} (photo: {{ $image->photographer }})" />
    {{ if $where == "article" }}<p><em>{{ $image->caption }}</em>{{ if $image->photographer }} / (photo: {{ $image->photographer }}){{ /if }}</p>{{ /if }}
{{ /image }} 
