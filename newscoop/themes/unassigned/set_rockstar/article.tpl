{{ config_load file="{{ $gimme->language->english_name }}.conf" }}

{{ include file="_tpl/_html-head.tpl" }}

	<div id="wrapper">

{{ include file="_tpl/header.tpl" }}

		<div id="content" class="clearfix">
            
            <section class="main entry">
            
{{ include file="_tpl/article-cont.tpl" }}
                
{{ include file="_tpl/article-author-info.tpl" }}
                
{{ include file="_tpl/article-comments.tpl" }}
            
            </section><!-- / Entry -->
            
{{ include file="_tpl/article-aside.tpl" }}
            
            <div class="divider"></div>
            
{{ include file="_tpl/all-sections.tpl" }}
        
        </div><!-- / Content -->
        
{{ include file="_tpl/footer.tpl" }}
    
    </div><!-- / Wrapper -->
	
{{ include file="_tpl/_html-foot.tpl" }}

</body>
</html>