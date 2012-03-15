{{ include file="_tpl/_html-head.tpl" }}

<body id="articlepage">

  <div id="container">
          
{{ include file="_tpl/header.tpl" }}
    
    <div class="row clearfix" role="main">
  
      <div id="maincol" class="eightcol clearfix">

{{ include file="_tpl/article-cont.tpl" }}

{{ include file="_tpl/article-comments.tpl" }}
                              
      </div><!-- /#maincol -->

      <aside class="fourcol last">
        
{{ include file="_tpl/article-aside.tpl" }}          

        </aside>
        
    </div>
    
{{ include file="_tpl/footer.tpl" }}

  </div> <!-- /#container -->

{{ include file="_tpl/_html-foot.tpl" }}