<div class="logintop">

{{ if $gimme->url->get_parameter('logout') }}
    <META HTTP-EQUIV="Set-Cookie" CONTENT="LoginUserId=; path=/">
    <META HTTP-EQUIV="Set-Cookie" CONTENT="LoginUserKey=; path=/">
    {{ $gimme->url->set_parameter('f_blog_id', $gimme->blog->identifier) }}
    {{ $gimme->url->set_parameter('f_blogentry_id', $gimme->blogentry->identifier) }}
    {{ $gimme->url->set_parameter('tpl', $gimme->default_template->identifier) }}
    {{ $gimme->url->set_parameter('tpid', $gimme->default_topic->identifier) }}
    {{ $gimme->url->reset_parameter('logout') }}
    <META HTTP-EQUIV="Refresh" content="0;url={{ uri }}">
{{ /if }}

{{ if !$gimme->user->logged_in }}
    {{ local }}
        {{ unset_section }}
        <div id="register">
        <a href="{{ uri options="template classic/register.tpl" }}">{{ if $gimme->language->name == "English" }}Register{{ else }}Registrarse{{ /if }}</a> |
        </div>
    {{ /local }}
{{ /if }}

         
{{ if $gimme->user->logged_in }} 
 
    {{ unset_section }}
    <div id="user"><a href="{{ uri options="template classic/register.tpl" }}">{{ $gimme->user->name }}</a></div>
    {{ set_default_section }}
    {{ set_default_article }}

    {{ $gimme->url->set_parameter('f_blog_id', $gimme->blog->identifier) }}
    {{ $gimme->url->set_parameter('f_blogentry_id', $gimme->blogentry->identifier) }}
    {{ $gimme->url->set_parameter('tpl', $gimme->default_template->identifier) }}
    {{ $gimme->url->set_parameter('tpid', $gimme->default_topic->identifier) }}
    {{ $gimme->url->set_parameter('logout', 1) }}
    
    <div id="logout"><a href="{{ uri }}">{{ if $gimme->language->name == "English" }}Logout{{ else }}Desconectarse{{ /if }}</a></div>
    
    {{ $gimme->url->reset_parameter('f_blog_id') }}
    {{ $gimme->url->reset_parameter('f_blogentry_id') }}
    {{ $gimme->url->reset_parameter('tpl') }}
    {{ $gimme->url->reset_parameter('tpid') }}
    {{ $gimme->url->reset_parameter('logout') }}

{{ else }}
 
    {{ $gimme->url->set_parameter('f_blog_id', $gimme->blog->identifier) }}
    {{ $gimme->url->set_parameter('f_blogentry_id', $gimme->blogentry->identifier) }}
    {{ $gimme->url->set_parameter('tpl', $gimme->default_template->identifier) }}
    {{ $gimme->url->set_parameter('tpid', $gimme->default_topic->identifier) }}
    
    <div id="singin"> {{ if $gimme->language->name == "English" }}Sign in{{ else }}Entra{{ /if }}: </div>
    {{ login_form submit_button="Send" }}
          {{ camp_edit object="login" attribute="uname" }}
          {{ camp_edit object="login" attribute="password" }}
    {{ /login_form }}

    {{ if $gimme->login_action->is_error }}
        <div class="loginerror"><div class="loginerrorinner">
        {{ $gimme->login_action->error_message }}
        </div></div>
    {{ /if }}
    
    {{ $gimme->url->reset_parameter('f_blog_id') }}
    {{ $gimme->url->reset_parameter('f_blogentry_id') }}
    {{ $gimme->url->reset_parameter('tpl') }}
    {{ $gimme->url->reset_parameter('tpid') }}
    
{{ /if }}

<div class="clear"></div>
</div><!-- .logintop -->


