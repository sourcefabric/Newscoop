{{ config_load file="{{ $gimme->language->english_name }}.conf" }}

{{ include file="_tpl/_html-head.tpl" }}

	<div id="wrapper">

{{ include file="_tpl/header.tpl" }}

		<div id="content">
        
{{ include file="_tpl/search-cont.tpl" }}

{{ if $incl-all-sec == 1 }}            
{{ include file="_tpl/all-sections.tpl" }} 
{{ /if }}
        
        </div><!-- / Content -->
        
{{ include file="_tpl/footer.tpl" }}
    
    </div><!-- / Wrapper -->
	
{{ include file="_tpl/_html-foot.tpl" }}

</body>
</html>
