{{ config_load file="{{ $gimme->language->english_name }}.conf" }}

{{ include file="_tpl/_html-head.tpl" }}

<body id="articlepage" class="{{ if $gimme->article->type_name == "debate" }} debatte-single{{ /if }}">

  <div id="container">
          
{{ include file="_tpl/header.tpl" }}
    
    <div class="row clearfix" role="main">
  
      <div id="maincol" class="eightcol clearfix">

{{ if $gimme->article->type_name == "debate" }}

{{ include file="_tpl/article-debate.tpl" }}

{{ else }}

{{ include file="_tpl/article-cont.tpl" }}

{{ /if }}

{{ if $gimme->article->type_name !== "page" }}

{{ include file="_tpl/article-rating.tpl" }}

{{ include file="_tpl/article-comments.tpl" }}

{{ /if }}                              
                              
      </div><!-- /#maincol -->

      <aside class="fourcol last">
        
{{ include file="_tpl/article-aside.tpl" }}          

        </aside>
        
    </div>
    
{{ include file="_tpl/footer.tpl" }}

  </div> <!-- /#container -->

{{ include file="_tpl/_html-foot.tpl" }}