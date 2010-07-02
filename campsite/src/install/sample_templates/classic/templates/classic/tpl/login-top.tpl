<div class="logintop">

{{ if $campsite->url->get_parameter('logout') }}
    <META HTTP-EQUIV="Set-Cookie" CONTENT="LoginUserId=; path=/">
    <META HTTP-EQUIV="Set-Cookie" CONTENT="LoginUserKey=; path=/">
    {{ $campsite->url->set_parameter('f_blog_id', $campsite->blog->identifier) }}
    {{ $campsite->url->set_parameter('f_blogentry_id', $campsite->blogentry->identifier) }}
    {{ $campsite->url->set_parameter('tpl', $campsite->default_template->identifier) }}
    {{ $campsite->url->set_parameter('tpid', $campsite->default_topic->identifier) }}
    {{ $campsite->url->reset_parameter('logout') }}
    <META HTTP-EQUIV="Refresh" content="0;url={{ uri }}">
{{ /if }}

{{ if !$campsite->user->logged_in }}
    {{ local }}
        {{ unset_section }}
        <div id="register">
        <a href="{{ uri options="template classic/register.tpl" }}">Register</a> |
        </div>
    {{ /local }}
{{ /if }}

         
{{ if $campsite->user->logged_in }} 
 
    {{ unset_section }}
    <div id="user"><a href="{{ uri options="template classic/register.tpl" }}">{{ $campsite->user->name }}</a></div>
    {{ set_default_section }}
    {{ set_default_article }}

    {{ $campsite->url->set_parameter('f_blog_id', $campsite->blog->identifier) }}
    {{ $campsite->url->set_parameter('f_blogentry_id', $campsite->blogentry->identifier) }}
    {{ $campsite->url->set_parameter('tpl', $campsite->default_template->identifier) }}
    {{ $campsite->url->set_parameter('tpid', $campsite->default_topic->identifier) }}
    {{ $campsite->url->set_parameter('logout', 1) }}
    
    <div id="logout"><a href="{{ uri }}">Logout</a></div>
    
    {{ $campsite->url->reset_parameter('f_blog_id') }}
    {{ $campsite->url->reset_parameter('f_blogentry_id') }}
    {{ $campsite->url->reset_parameter('tpl') }}
    {{ $campsite->url->reset_parameter('tpid') }}
    {{ $campsite->url->reset_parameter('logout') }}

{{ else }}
 
    {{ $campsite->url->set_parameter('f_blog_id', $campsite->blog->identifier) }}
    {{ $campsite->url->set_parameter('f_blogentry_id', $campsite->blogentry->identifier) }}
    {{ $campsite->url->set_parameter('tpl', $campsite->default_template->identifier) }}
    {{ $campsite->url->set_parameter('tpid', $campsite->default_topic->identifier) }}
    
    <div id="singin"> Sign in: </div>
    {{ login_form submit_button="Send" }}
          {{ camp_edit object="login" attribute="uname" }}
          {{ camp_edit object="login" attribute="password" }}
    {{ /login_form }}

    {{ if $campsite->login_action->is_error }}
        <div class="loginerror"><div class="loginerrorinner">
        {{ $campsite->login_action->error_message }}
        </div></div>
    {{ /if }}
    
    {{ $campsite->url->reset_parameter('f_blog_id') }}
    {{ $campsite->url->reset_parameter('f_blogentry_id') }}
    {{ $campsite->url->reset_parameter('tpl') }}
    {{ $campsite->url->reset_parameter('tpid') }}
    
{{ /if }}

<div class="clear"></div>
</div><!-- .logintop -->


