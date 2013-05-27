{{ config_load file="{{ $gimme->language->english_name }}.conf" }}

{{ include file="_tpl/_html-head.tpl" }}

<body id="sectionpage">

  <div id="container">
          
{{ include file="_tpl/header.tpl" }}
    
    <div class="row clearfix" role="main">
  
      <div id="maincol" class="eightcol clearfix issue-archive">
        
{{ include file="_tpl/archive-cont.tpl" }}                        

        </div><!-- /#maincol -->
        
    <div id="sidebar" class="fourcol last">

{{ include file="_tpl/sidebar-loginbox.tpl" }}

{{ include file="_tpl/sidebar-most.tpl" }} 
            
{{ include file="_tpl/sidebar-community-feed.tpl" }}     
            
{{ include file="_tpl/_banner-sidebar.tpl" }} 
            
        </div><!-- /#sidebar -->
        
    </div>
    
{{ include file="_tpl/footer.tpl" }}

  </div> <!-- /#container -->
  
{{ include file="_tpl/_html-foot.tpl" }}