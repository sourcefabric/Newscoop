{{ image rendition="morenewsthumb" }}
<img {{ if $where!='aside' }}class="article-image pull-left"{{/if}} src="{{ $image->src }}" width="{{ $image->width }}" height="{{ $image->height }}" alt="{{ $image->photographer}}: {{ $image->caption }}" />
{{ /image }}
