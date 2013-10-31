{{ image rendition="articlesquare" }}
{{ if $where=='mobile'}}
<figure class="aside-figure pull-right hidden-desktop">
{{ else }}
<figure class="aside-figure pull-right visible-desktop">
{{ /if }}
    <img src="{{ $image->src }}" width="{{ $image->width }}" height="{{ $image->height }}" alt="{{ $image->photographer }}: {{ $image->caption }}" />
        <figcaption>{{ $image->caption}}</figcaption>
</figure>
{{ /image }}
