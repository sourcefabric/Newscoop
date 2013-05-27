{{ image rendition="square" }}
                <figure class="threecol">
                    <a href="{{ url options="article" }}">
                        <img src="{{ $image->src }}" width="{{ $image->width }}" height="{{ $image->height }}" alt="{{ $image->caption }} {{ if $image->photographer|trim }}({{ #photo# }} {{ $image->photographer }}){{ /if }}" />
                    </a>
                </figure>
{{ /image }}