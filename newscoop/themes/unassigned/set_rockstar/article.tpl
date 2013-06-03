{{ config_load file="{{ $gimme->language->english_name }}.conf" }}

{{ include file="_tpl/_html-head.tpl" }}

	<div id="wrapper">

{{ include file="_tpl/header.tpl" }}

		<div id="content" class="clearfix">
            
            <section class="main entry">
            
{{ if $gimme->article->type_name == "debate" }}

{{ include file="_tpl/article-debate.tpl" }}

{{ else }}

{{ include file="_tpl/article-cont.tpl" }}

{{ /if }}

{{ if $gimme->article->type_name !== "page" }}

{{ if $gimme->article->type_name !== "debate" }}                
{{ include file="_tpl/article-author-info.tpl" }}
{{ /if }}
                
{{ include file="_tpl/article-rating.tpl" }}

{{ include file="_tpl/article-comments.tpl" }}
            
{{ /if }}            
            
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