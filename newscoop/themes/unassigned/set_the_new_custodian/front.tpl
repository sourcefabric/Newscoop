{{ config_load file="{{ $gimme->language->english_name }}.conf" }}

{{ include file="_tpl/_html-head.tpl" }}

<body>

  <div id="container">
      
{{ include file="_tpl/header.tpl" }}
    
    <div class="row clearfix" role="main">    
    
      <div id="maincol" class="eightcol clearfix">
        
{{ include file="_tpl/front-topstory.tpl" }}
            
{{ include file="_tpl/front-morestories.tpl" }}

{{ include file="_tpl/front-highlights.tpl" }}
            
      </div><!-- /#maincol -->
        
      <div id="sidebar" class="fourcol last">

{{ include file="_tpl/sidebar-loginbox.tpl" }}

{{ include file="_tpl/sidebar-most.tpl" }} 
            
{{ include file="_tpl/sidebar-community-feed.tpl" }}      

{{ include file="_tpl/sidebar-poll.tpl" }}
            
{{* include file="_tpl/_banner-sidebar.tpl" *}}   
            
        </div><!-- /#sidebar -->
        
{{ include file="_tpl/front-dynamicmap.tpl" }}        
    
{{ include file="_tpl/front-morenews.tpl" }}
    
    </div>
    
{{ include file="_tpl/footer.tpl" }}

  </div> <!-- /#container -->

{{ include file="_tpl/_html-foot.tpl" }}