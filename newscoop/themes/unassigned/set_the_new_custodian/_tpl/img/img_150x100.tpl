{{ image rendition="sectionthumb" }}
                <figure>
                    <a class="fourcol last" href="{{ url options="article" }}">
                        <img src="{{ $image->src }}" width="{{ $image->width }}" height="{{ $image->height }}" alt="{{ $image->caption }} {{ if $image->photographer|trim }}({{ #photo# }} {{ $image->photographer }}){{ /if }}" />
                    </a>
                    <figcaption>
                      <p>{{ $image->caption }}</p>
                    </figcaption>
                </figure>
{{ /image }}                
