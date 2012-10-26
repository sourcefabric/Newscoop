{{ include file="classic/tpl/header.tpl" }}

<body id="register">
<div id="container">
<div id="wrapbg">
<div id="wrapper">

{{ include file="classic/tpl/headernav.tpl" }}

<div class="colmask rightmenu">
    <div class="colleft">
        <div class="col1wrap">
            <div class="col1">

<!-- Column 1 start -->

{{ if !$gimme->edit_user_action->defined && !$gimme->edit_subscription_action->defined }}
  {{ include file="classic/tpl/user-form.tpl" }}
{{ /if }}

{{ if $gimme->edit_user_action->defined && $gimme->edit_user_action->is_error }}
<div class="error"><div class="errorinner">
{{ if $gimme->language->name == "English" }}There was an error submitting the account creation form:{{ else }}Hubo un error al enviar el formulario de creación de la cuenta:{{ /if }}
  {{ $gimme->edit_user_action->error_message }}
</div></div>
  {{ include file="classic/tpl/user-form.tpl" }}
{{ /if }}

{{ if $gimme->edit_user_action->defined && $gimme->edit_user_action->ok }}
  {{ if $gimme->language->name == "English" }}Your profile updated sucessfully.{{ else }}Su perfil actualizado correctamente.{{ /if }}
{{ /if }}


<!-- Column 1 end -->

            </div>
        </div>
        <div class="col2">

<!-- Column 2 start -->

{{ include file="classic/tpl/search-box.tpl" }}

<!-- Banner -->
{{ include file="classic/tpl/banner/bannerrightcol.tpl" }}

<!-- Column 2 end -->

        </div>
    </div>
</div>

{{ include file="classic/tpl/footer.tpl" }}

</div><!-- id="wrapbg"-->
</div><!-- id="wrapper"-->
</div><!-- id="container"-->
</body>
</html>