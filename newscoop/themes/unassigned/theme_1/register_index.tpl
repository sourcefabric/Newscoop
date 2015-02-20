{{extends file="layout.tpl"}}

{{block content}}
<div class="article_content bloger content_text white-box">
  <div class="clearfix">
             <h3 class="normal_header">{{'Register'|translate}}</h3>



            <div class="register_form">

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
</div>
</div>

{{/block}}



