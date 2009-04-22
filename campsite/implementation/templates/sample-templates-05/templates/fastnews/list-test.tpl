<html>

{{ unset_section }}
{{ list_articles }}
	<p>{{ $campsite->article->number }}. {{ $campsite->article->name }} ({{ $campsite->article->creation_date }})
	{{ if $campsite->image->has_image2 }}
		, image 2: {{ $campsite->image2->description }}, {{ $campsite->image2->date }} <img src="/get_img.php?{{ urlparameters options="image 2" }}">
	{{ /if }}
{{ /list_articles }}

</html>
