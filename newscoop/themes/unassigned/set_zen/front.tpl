{{ include file="_tpl/_html-head.tpl" }}

<body>

<div class="container">

{{ include file="_tpl/top.tpl" }}


  <div class="row" id="feature">
{{ list_articles length="1" ignore_issue="true" ignore_section="true" order="bypublishdate desc" constraints="onfrontpage is on" }}
          <div class="fourcol" id="image">
            <a title="{{ $gimme->article->name }}" href="{{ uri options="article" }}"><img src="{{ uri options="image 1 width 350" }}" alt="{{ $gimme->article->image->description }}" class="thumbnail"></a>
            <div class="caption">{{ $gimme->article->image->description }}</div>  
          </div>
          <div class="fourcol" id="post">
            <h2><a href="{{ uri options="article" }}" rel="bookmark" title="{{ $gimme->article->name }}">{{ $gimme->article->name }}</a></h2>
            <p>{{ include file="_tpl/article-icons.tpl" }}{{ $gimme->article->deck }}</p> 
{{ include file="_tpl/link-readmore.tpl" }}
{{ /list_articles }} 
          </div>
          <div class="fourcol last">
{{ include file="_tpl/front-three-categories.tpl" }}
          </div>
  </div><!--.row-->

{{ include file="_tpl/front-topfeatures.tpl" }}

  <div class="row">
    <div class="fourcol">
{{ include file="_tpl/block-about.tpl" }}
    </div>
    <div class="fourcol">
{{ include file="_tpl/block-mostread.tpl" }}
    </div>
    <div class="fourcol last">
{{ include file="_tpl/block-recentcomments.tpl" }}
    </div>
  </div>

  <div class="row" id="furtherarticles">
{{ include file="_tpl/front-also-categories.tpl" }}
  </div><!--.row-->

            
{{* this banner is commented out ...
  <div class="row">
    <div class="twelvecol">
{{ include file="_tpl/_banner728x90.tpl" }}
    </div>
  </div>
... comment out ends here *}}
                
    
{{ include file="_tpl/footer.tpl" }}
  
</div>

</body></html>