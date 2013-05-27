{{ config_load file="{{ $gimme->language->english_name }}.conf" }}

{{ include file="_tpl/_html-head.tpl" }}

	<div id="wrapper">

{{ include file="_tpl/header.tpl" }}

		<div id="content">
        
{{ include file="_tpl/section-cont.tpl" }}
            
{{ include file="_tpl/section-two-blocks.tpl" }} 
            
{{ include file="_tpl/all-sections.tpl" }} 
        
        </div><!-- / Content -->
        
{{ include file="_tpl/footer.tpl" }}
    
    </div><!-- / Wrapper -->
	
{{ include file="_tpl/_html-foot.tpl" }}

</body>
</html>