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
  
{{ if $campsite->article->has_image(1) }}
<!-- Big banner image -->
  <div class="teaserimg_big">
  <a href="{{ uri options="template article.tpl" }}"><img src="{{ uri options="image 1 width 350" }}"/></a>
  </div><!-- .teaserimg_big -->
{{ /if }}

  <div class="teasercontent content">
  <h2 class="title title_big"><a href="{{ uri options="article template article.tpl" }}">{{ $campsite->article->name }}</a></h2>
  <p class="text">{{ $campsite->article->Lead_and_SMS }}</p>
  <ul class="links">
  <li><a href="{{ uri options="article template article.tpl" }}">{{ if $campsite->language->name == "English" }}Read more{{ else }}Leer más{{ /if }}</a>
  {{ include file="classic/tpl/comments-link.tpl" }}
  </ul>
  </div><!-- .teasercontent content -->
</div><!-- .teaserframeinner -->
</div><!-- .teaserframe -->