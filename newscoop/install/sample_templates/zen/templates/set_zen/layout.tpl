{{ include file="_tpl/_html-head.tpl" }}

<body>

<div class="container">

{{ include file="_tpl/top.tpl" }}
  <div class="row">
    <div class="eightcol" id="contentlist">

    {{block content}}{{/block}}

    </div><!--eightcol-->
    <div class="fourcol last">

{{ include file="_tpl/_banner300x250.tpl" }}

{{ include file="_tpl/sidebar-pages.tpl" }}

{{ include file="_tpl/sidebar-blogroll.tpl" }}
    </div><!--fourcol-->
  </div><!--row-->

  <div class="row" id="furtherarticles">
{{ include file="_tpl/article-mostread.tpl" }}
  </div><!--.row-->
    
{{ include file="_tpl/footer.tpl" }}
  
</div>

</body>
</html>
