{{ image rendition="square" }}
                <figure class="threecol">
                    <a href="{{ url options="article" }}">
                        <img src="{{ $image->src }}" width="{{ $image->width }}" height="{{ $image->height }}" alt="{{ $image->caption }} (photo: {{ $image->photographer }})" />
                    </a>
                </figure>
{{ /image }}