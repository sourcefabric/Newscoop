{{ image rendition="big" }}
	<figure>
    <img src="{{ $image->src }}" width="{{ $image->width }}" height="{{ $image->height }}" rel="resizable" style="max-width: 100%" alt="{{ $image->caption }} ({{ #photo# }} {{ $image->photographer }})" />
    {{ if $where == "article" }}<figcaption><em>{{ $image->caption }}</em>{{ if $image->photographer }} / ({{ #photo# }} {{ $image->photographer }}){{ /if }}</figcaption>{{ /if }}
    </figure>
{{ /image }} 
