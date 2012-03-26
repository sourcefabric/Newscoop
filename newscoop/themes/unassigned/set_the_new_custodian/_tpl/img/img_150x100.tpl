{{ image rendition="sectionthumb" }}
                <figure>
                    <a class="fourcol last" href="{{ url options="article" }}">
                        <img src="{{ $image->src }}" width="{{ $image->width }}" height="{{ $image->height }}" alt="{{ $image->caption }} (photo: {{ $image->photographer }})" />
                    </a>
                    <figcaption>
                      <p>{{ $image->caption }}</p>
                    </figcaption>
                </figure>
{{ /image }}                
