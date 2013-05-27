{{ config_load file="{{ $gimme->language->english_name }}.conf" }}

{{ include file="_tpl/_html-head.tpl" }}

	<div id="wrapper">

{{ include file="_tpl/header.tpl" }}

		<div id="content">

{{ assign var="userindex" value=1 }}

            <div class="title page-title">
            	<h2>{{ #userAccount# }}</h2>
            </div>

            <section class="grid-6 extended-small">

{{ assign var="userindex" value=1 }}

    <h5 class="checkHeading">{{ #weSentEmail# }}</h5>
    <p>{{ #pleaseCheckInbox# }}</p>

            </section><!-- / 6 articles grid -->
        
        </div><!-- / Content -->
        
{{ include file="_tpl/footer.tpl" }}
    
    </div><!-- / Wrapper -->
	
{{ include file="_tpl/_html-foot.tpl" }}

</body>
</html>
