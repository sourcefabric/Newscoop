<div class="logintop">
{{ if ! $gimme->user->logged_in }}                                
    {{ login_form submit_button="login" }}     
        <p class="fields"><a href="{{ uri options="template register.tpl" }}">Register</a> | {{ if $gimme->login_action->is_error }}<span style="color: red">{{ $gimme->login_action->error_message }}!</span>{{ else }}Sign in:{{ /if }} 
        <label for="uname"></label> {{ camp_edit object="login" attribute="uname" html_code="value=\"username\"" }}
        <label for="uname"></label> {{camp_edit object="login" attribute="password" html_code="value=\"password\"" }}
    {{ /login_form }}</p>
{{ else }}<form><p class="fields">Welcome, <a href="{{ uri options="template register.tpl" }}">{{ $gimme->user->name }}</a> | <a href="?logout=true">logout</a></p></form>
{{ /if }}
</div><!-- /#login-top -->