{{ config_load file="{{ $gimme->language->english_name }}.conf" }}

{{ include file="_tpl/_html-head.tpl" }}

	<div id="wrapper">

{{ include file="_tpl/header.tpl" }}

		<div id="content">

{{ assign var="userindex" value=1 }}

            <div class="title page-title">
            	<h2>{{ #registerTitle# }}</h2>
            </div>

            <section class="grid-6 extended-small">

<div class="form-content horizontal-form">
{{ $form }}

<script type="text/javascript">
$('#email').change(function() {
    $.post('{{ $view->url(['controller' => 'register', 'action' => 'check-email'], 'default') }}?format=json', {
        'email': $(this).val()
    }, function (data) {
        if (data.status) {
            $('#email').css('color', 'green');
        } else {
            $('#email').css('color', 'red');
        }
    }, 'json');
}).keyup(function() {
    $(this).change();
});
</script>


            </section><!-- / 6 articles grid -->
        
        </div><!-- / Content -->
        
{{ include file="_tpl/footer.tpl" }}
    
    </div><!-- / Wrapper -->
	
{{ include file="_tpl/_html-foot.tpl" }}

</body>
</html>
