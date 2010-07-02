<div class="teaserframe teaserframe-{{ $campsite->section->number }}">
<div class="teaserframeinner">
	<div class="teaserhead">
	<div class="teaserheadinner">
	<div class="teaserheadsection">
		<a href="{{ uri options="section template section.tpl" }}">{{ $campsite->section->name }}</a> |
	</div><!-- class="teaserheadsection" -->
		{{ $campsite->article->publish_date|camp_date_format:'%M %D, %Y %h:%i:%s' }}
		{{ include file="classic/tpl/topic-list.tpl" }}
	</div><!-- .teaserheadinner -->
	</div><!-- .teaserhead -->
{{ if $campsite->article->has_image(202) }}
<!-- Big banner image -->
	<div class="teaserimg_big">
	<a href="{{ uri options="template article.tpl" }}"><img src="/get_img.php?{{ urlparameters options="image 202" }}"/></a>
	</div><!-- .teaserimg_big -->
{{ /if }}
{{ if $campsite->article->has_image(203) }}
<!-- Big square image -->
	<div class="teaserimg_med">
	<a href="{{ uri options="template article.tpl" }}"><img src="/get_img.php?{{ urlparameters options="image 203" }}"/></a>
	</div><!-- .teaserimg_med -->
{{ /if }}
	<div class="teasercontent content">
	<h2 class="title title_big"><a href="{{ uri options="article template article.tpl" }}">{{ $campsite->article->name }}</a></h2>
	<p class="text">{{ $campsite->article->Lead_and_SMS }}</p>
	<ul class="links">
	<li><a href="{{ uri options="article template article.tpl" }}">Read more<!--Read more--></a>
	{{ include file="classic/tpl/comments-link.tpl" }}
	</ul>
	</div><!-- .teasercontent content -->
</div><!-- .teaserframeinner -->
</div><!-- .teaserframe -->