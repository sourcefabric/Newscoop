{{ if $campsite->article->defined }}
<div class="topics">
<p class="opt"><b>Related articles on</b></p>
<ul>
{{ local }}

{{ set_language name="english" }}

{{ if $campsite->topic->name == "law:en" }}
	<li><a href="http://del.icio.us/tag/law">law</a></li>
{{ /if }}

{{ if $campsite->topic->name == "media:en" }}
	<li><a href="http://del.icio.us/tag/media">media</a></li>
{{ /if }}

{{ if $campsite->topic->name == "environment:en" }}
	<li><a href="http://del.icio.us/tag/environment">environment</a></li>
{{ /if }}

{{ if $campsite->topic->name == "politics:en" }}
	<li><a href="http://del.icio.us/tag/politics">politics</a></li>
{{ /if }}

{{ /local }}
</ul>
</div>
{{ /if }}