{{ config_load file="{{ $gimme->language->english_name }}.conf" }}

{{ include file="_tpl/_html-head.tpl" }}

	<div id="wrapper">

{{ include file="_tpl/header.tpl" }}

		<div id="content">

{{ assign var="userindex" value=1 }}

            <div class="title page-title">
            	<h2>{{ #pleaseFillData# }}</h2>
            </div>

            <section class="grid-6 extended-small">

<div class="form-content">
<fieldset class="background-block">
{{ $form }}


<script type="text/javascript">
$('#first_name, #last_name').keyup(function() {
    $.post('{{ $view->url(['controller' => 'register', 'action' => 'generate-username'], 'default') }}?format=json', {
        'first_name': $('#first_name').val(),
        'last_name': $('#last_name').val()
    }, function (data) {
        $('#username').val(data.username).css('color', 'green');
    }, 'json');
});

$('#username').change(function() {
    $.post('{{ $view->url(['controller' => 'register', 'action' => 'check-username'], 'default') }}?format=json', {
        'username': $(this).val()
    }, function (data) {
        if (data.status) {
            $('#username').css('color', 'green');
        } else {
            $('#username').css('color', 'red');
        }
    }, 'json');
}).keyup(function() {
    $(this).change();
});
</script>
</fieldset>
</div>
            </section><!-- / 6 articles grid -->
        
        </div><!-- / Content -->
        
{{ include file="_tpl/footer.tpl" }}
    
    </div><!-- / Wrapper -->
	
{{ include file="_tpl/_html-foot.tpl" }}

</body>
</html>
