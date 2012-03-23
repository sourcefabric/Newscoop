{{ image rendition="topfront" }}
              <figure class="eightcol">
                  <a href="{{ url options="article" }}">
							<img src="{{ $image->src }}" width="{{ $image->width }}" height="{{ $image->height }}" rel="resizable" style="max-width: 100%" alt="{{ $image->caption }} (photo: {{ $image->photographer }})" />
						</a>
                  <figcaption>
                      <em>{{ $image->caption }}</em>
                  </figcaption>
              </figure>
{{ /image }}              
              
              