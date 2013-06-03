{{ image rendition="sectionthumb" }}
                <figure{{ if $where == "author" }} class="fourcol"{{ /if }}>
                    <a{{ if $where == "section" }} class="fourcol"{{ /if }} href="{{ url options="article" }}">
                        <img src="{{ $image->src }}" width="{{ $image->width }}" height="{{ $image->height }}" rel="resizable" style="max-width: 100%" alt="{{ $image->caption }} {{ if $image->photographer|trim }}({{ #photo# }} {{ $image->photographer }}){{ /if }}" />
                    </a>
                    {{ if $where == "section" }}
                    <figcaption>
                      <p>{{ $image->caption }} {{ if $image->photographer|trim }}({{ #photo# }} {{ $image->photographer }}){{ /if }}</p>
                    </figcaption>
                    {{ /if }}
                </figure>
{{ /image }}              
