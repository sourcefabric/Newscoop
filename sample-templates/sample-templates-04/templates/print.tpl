<h4>{{ $campsite->article->deck }}</h4>
<h1>{{ $campsite->article->name }}</h1>
<h5>{{ $campsite->article->byline }}</h5>
<p>{{ $campsite->article->intro }}</p>
<p>{{ if $campsite->article->has_image(2) }}
<div style="float:left;margin-right:10px;"><img src="/get_img.php?{{ urlparameters options="image 2" }}"></div>
{{ /if }}{{ $campsite->article->full_text }}</p>
