{{extends file="layout.tpl"}}

{{block title}}{{ #registerConfirm# }}{{/block}}

{{block content}}

{{ assign var="userindex" value=1 }}

<h3>{{ #pleaseFill# }}</h3>
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
{{/block}}
