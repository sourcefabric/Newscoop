{{ config_load file="{{ $gimme->language->english_name }}.conf" }}

{{ include file="_tpl/_html-head.tpl" }}

	<div id="wrapper">

{{ include file="_tpl/header.tpl" }}

		<div id="content">

            <div class="title page-title">
            	<h2>{{ #confirmSent# }}</h2>
            </div>

            <section class="grid-6 extended-small">

<div class="alert alert-info">
    <p>{{ #followEmailSteps# }}</p>
    <p>{{ #thanksForRegistering# }}</p>
</div>

            </section><!-- / 6 articles grid -->
        
        </div><!-- / Content -->
        
{{ include file="_tpl/footer.tpl" }}
    
    </div><!-- / Wrapper -->
	
{{ include file="_tpl/_html-foot.tpl" }}

</body>
</html>
