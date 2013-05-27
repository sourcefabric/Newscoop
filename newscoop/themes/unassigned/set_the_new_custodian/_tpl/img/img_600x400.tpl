{{ image rendition="articlebig" }}
              <figure class="clearall">
                  <img src="{{ $image->src }}" width="{{ $image->width }}" height="{{ $image->height }}" class="tencol last" rel="resizable" style="max-width: 100%" alt="{{ $image->caption }} {{ if $image->photographer|trim }}({{ #photo# }} {{ $image->photographer }}){{ /if }}" />
                  <figcaption class="clearall">
                      <em>{{ $image->caption }} {{ if $image->photographer|trim }}({{ #photo# }} {{ $image->photographer }}){{ /if }}</em>
                  </figcaption>
              </figure>
{{ /image }} 
