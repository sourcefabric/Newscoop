{{ config_load file="{{ $gimme->language->english_name }}.conf" }}

{{ include file="_tpl/_html-head.tpl" }}

	<div id="wrapper">

{{ include file="_tpl/header.tpl" }}

		<div id="content">

{{ assign var="userindex" value=1 }}

<script>
function afterRegistration() {
    location.reload();
}
</script>

            <div class="title page-title">
            	<h2>{{ #welcome# }} <span>{{ $user->name }}</span></h2>
            </div>

            <section class="grid-6 extended-small">
<div class="form-content">
<figure class="user-image threecol">
    <img src="{{ include file="_tpl/user-image.tpl" user=$user width=156 height=156 }}" style="max-width: 100%" rel="resizable" />
</figure>

{{ $form }}
</div>

            </section><!-- / 6 articles grid -->
        
        </div><!-- / Content -->
        
{{ include file="_tpl/footer.tpl" }}
    
    </div><!-- / Wrapper -->
	
{{ include file="_tpl/_html-foot.tpl" }}

</body>
</html>
