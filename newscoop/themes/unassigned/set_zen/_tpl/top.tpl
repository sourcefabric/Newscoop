<div class="row" id="metanavigation">
  <div class="eightcol register">
{{ include file="_tpl/top-login.tpl" }}
  </div>
  <div class="twocol  rss">
    <p><a href="http://{{ $gimme->publication->site }}/?tpl=1341">RSS Feed</a></p>
  </div>
  <div class="twocol date last">
    <p>{{$smarty.now|camp_date_format:"%M %e, %Y"}}</p>
  </div>
</div><!--.row--><!--#metanavigation-->

<div class="row" id="header">
  <div class="fourcol logo">
    <p><a href="http://{{ $gimme->publication->site }}" title="{{ $gimme->publication->name }}"><img src="{{ url static_file='_img/logo.png' }}" alt="{{ $gimme->publication->name }}"></a></p>
  </div>
  {{ list_articles length="3" ignore_issue="true" ignore_section="true" order="bypublishdate desc" constraints="type is news" }}
  <div class="twocol mobilehide">
    <div class="section"><a href="{{ uri options="section" }}" title="View all posts in {{ $gimme->section->name }}" rel="category tag">{{ $gimme->section->name }}</a></div>
      <h4><a title="{{ $gimme->article->name }}" href="{{ uri options="article" }}" rel="bookmark">{{ include file="_tpl/article-icons.tpl" }}{{ $gimme->article->name }}</a></h4>
  </div>
    {{ /list_articles }}
  <div class="twocol last archive mobilehide">
    <div class="section"><a href="{{ local }}{{ unset_section }}{{ uri options="template archive.tpl" }}{{ /local }}">Archive</a></div>
      <h4>Find more articles in our <a href="{{ local }}{{ unset_section }}{{ uri options="template archive.tpl" }}{{ /local }}">archive</a>.</h4>
  </div>
</div><!--.row--><!--#header-->

{{ include file="_tpl/top-nav.tpl" }}  

