<figure class="clearall">
  <div data-picture data-alt="{{ image rendition="sectionthumb" }}{{ $image->caption }} (photo: {{ $image->photographer }}){{ /image }}">
		{{ image rendition="sectionthumb" }}
			<div data-src="{{ $image->src }}"></div>
		{{ /image }}
		{{ image rendition="topfront" }}
			<div data-src="{{ $image->src }}" data-media="(min-width: 500px)"></div>
		{{ /image }}
		{{ image rendition="articlebig" }}
			<div data-src="{{ $image->src }}" data-media="(min-width: 600px)"></div>
		{{ /image }} 
		<noscript>
		{{ image rendition="sectionthumb" }}
			<img src="{{ $image->src }}" alt="{{ $image->caption }} (photo: {{ $image->photographer }})">
		{{ /image }}
		</noscript>
  </div>
  <figcaption class="clearall">
    {{ $image->caption }} {{ if $image->photographer|trim }}({{ #photo# }} {{ $image->photographer }}){{ /if }}
  </figcaption>
</figure>