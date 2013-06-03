{{ image rendition="onethird" }}
<img src="{{ $image->src }}" width="{{ $image->width }}" height="{{ $image->height }}" rel="resizable" style="max-width: 100%" alt="{{ $image->caption }} ({{ #photo# }} {{ $image->photographer }})" />
{{ /image }} 
