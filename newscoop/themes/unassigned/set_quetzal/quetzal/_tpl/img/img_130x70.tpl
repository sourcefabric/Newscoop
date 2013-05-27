{{ image rendition="issuethumb" }}
<figure class="pull-left article-image">
    <img src="{{ $image->src }}" width="{{ $image->width }}" height="{{ $image->height }}" alt="{{ $image->photographer}}: {{ $image->caption }}" />
</figure>
{{ /image }}
