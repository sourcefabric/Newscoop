{{ capture name="_img" assign="_img" }}
{{ image rendition="slider"  }}
{{ $image->src }}
{{ /image }}
{{ /capture }}
{{ if trim($_img) }}
{{ $_img }}
{{ else }}
{{ url static_file="_img/slider-default.jpg" }}
{{ /if }}
