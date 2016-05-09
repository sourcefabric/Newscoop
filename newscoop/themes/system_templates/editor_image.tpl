{{ dynamic }}
{{ if isset($imageDetails['align'] ) && $imageDetails['align'] }} <div align="center"> {{ /if }}
	<div class="cs_img {{ if isset($imageDetails['align'] ) && $imageDetails['align'] }}cs_fl_{{ $imageDetails['align'] }}{{ /if }}" {{ if isset($imageDetails['percentage'] ) && $imageDetails['percentage'] }}style="width:{{ $imageDetails['percentage'] }};"{{ /if }} {{ if isset($imageDetails['percentage'] ) && !$imageDetails['percentage'] && $imageDetails['width']}}style="width:{{ $imageDetails['width'] }}px;"{{ /if }}>
		{{ if strlen($imgZoomLink) > 0 }} <p><a href="{{ $imgZoomLink }}" class="photoViewer" title="{{ $imageDetails['sub'] }}"> {{ else }}<p> {{ /if }}
			<img src="{{ $uri->uri }}" {{ if isset($imageDetails['alt']) }}alt="{{ $imageDetails['alt'] }}"{{ /if }} {{ if isset($imageDetails['sub']) }}title="{{ $imageDetails['sub'] }}" {{ /if }} border="0"/>
		{{ if strlen($imgZoomLink) > 0 }} </a></p> {{ else }}</p> {{ /if }}
		{{ if isset($imageDetails['sub']) }}
            {{ if $MediaRichTextCaptions == 'Y' }}
                <div class="cs_img_caption">{{$imageDetails['sub']}}</div>
            {{ else }}
                <p class="cs_img_caption">{{$imageDetails['sub']}}</p>
            {{ /if }}
        {{ /if }}
	</div>
{{ if isset($imageDetails['align'] ) && $imageDetails['align'] }}</div>{{ /if }}
{{ /dynamic }}
