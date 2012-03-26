{{ image rendition="articlebig" }}
              <figure class="clearall">
                  <img src="{{ $image->src }}" width="{{ $image->width }}" height="{{ $image->height }}" class="tencol last" rel="resizable" style="max-width: 100%" alt="{{ $image->caption }} (photo: {{ $image->photographer }})" />
                  <figcaption class="clearall">
                      <em>{{ $image->caption }}</em>
                  </figcaption>
              </figure>
{{ /image }} 
