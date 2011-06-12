{{ include file="set_thejournal/_tpl/_html-head.tpl" }}

<body class="single single-post custom gecko">
<div id="wrap">

{{ include file="set_thejournal/_tpl/top.tpl" }}

    <div id="content" class="wrap">
    
{{ if $gimme->article->type_name == "news" }}    
    
    <div class="col-left">
      <div id="main">

                <div class="post wrap">

{{ include file="set_thejournal/_tpl/article-cont.tpl" }}
          
          <div id="comments">
            
{{ include file="set_thejournal/_tpl/article-comments.tpl" }}

          </div>
                </div>

            </div><!-- main ends -->
        </div><!-- .col-left ends -->

<div id="sidebar" class="col-right">

    <div class="sidebar-top">
    
 {{ if $gimme->article->has_map }}     
{{ include file="set_thejournal/_tpl/article-map.tpl" }}
{{ else }}
{{ include file="set_thejournal/_tpl/_banner300x250.tpl" }}
{{ /if }}

{{ include file="set_thejournal/_tpl/dynamic-map.tpl" }}

    </div>
         
{{ include file="set_thejournal/_tpl/sidebar-related.tpl" }}
        
{{ include file="set_thejournal/_tpl/sidebar-pages.tpl" }}
    
{{ include file="set_thejournal/_tpl/sidebar-blogroll.tpl" }}

</div>

{{ else }}

{{ include file="set_thejournal/_tpl/article-fullwidth.tpl" }}

{{ /if }}

    </div><!-- Content Ends -->
    
{{ include file="set_thejournal/_tpl/footer.tpl" }}
  
</div>

</body>
</html>
