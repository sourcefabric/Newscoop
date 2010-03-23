<div width=100% class="masthead"><a href="/"><img src="/templates/fastnews/masthead.png" width=531 height=150 alt="FASTNEWS" border=0></a></div>

<div class="edition">
	<div class="issue"><nobr>{{ if $campsite->issue->is_current }}Current issue &ndash; {{ else }}Archived &ndash; {{ /if }}{{ $campsite->issue->name }}&nbsp;(#{{ $campsite->issue->number }})</nobr></div>
	<div class="date">{{ $smarty.now|camp_date_format:"%W, %M %e %Y" }}</div>
</div>

<table class="navart" width=100%><tr>