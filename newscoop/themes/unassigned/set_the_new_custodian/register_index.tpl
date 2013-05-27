{{extends file="layout.tpl"}}

{{block content}}

{{ assign var="userindex" value=1 }}

<h3>{{ #register# }}</h3>
<div class="register-block">
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
</div>
{{/block}}
