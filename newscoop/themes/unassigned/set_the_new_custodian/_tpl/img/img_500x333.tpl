{{ image rendition="topfront" }}
              <figure class="eightcol">
                  <a href="{{ url options="article" }}">
							<img src="{{ $image->src }}" width="{{ $image->width }}" height="{{ $image->height }}" rel="resizable" style="max-width: 100%" alt="{{ $image->caption }} {{ if $image->photographer|trim }}({{ #photo# }} {{ $image->photographer }}){{ /if }}" />
						</a>
                  <figcaption>
                      <em>{{ $image->caption }} {{ if $image->photographer|trim }}({{ #photo# }} {{ $image->photographer }}){{ /if }}</em>
                  </figcaption>
              </figure>
{{ /image }}              
              
              