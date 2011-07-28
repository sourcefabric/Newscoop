{{ include file="_tpl/_html-head.tpl" }}

<body>

<div class="container">

{{ include file="_tpl/top.tpl" }}
  <div class="row">
    <div class="eightcol" id="content">

    <h2 class="post-title">Create and Edit Your Profile</h2>

    {{ if !$gimme->edit_user_action->defined && !$gimme->edit_subscription_action->defined }}
{{ include file="_tpl/user-form.tpl" }}
    {{ /if }}

    {{ if $gimme->edit_user_action->defined && $gimme->edit_user_action->is_error }}
    <div class="message messageregister messageerror">
      Something went wrong when creating your account: {{ $gimme->edit_user_action->error_message }}
    </div>
{{ include file="_tpl/user-form.tpl" }}
    {{ /if }}

    {{ if $gimme->edit_user_action->defined && $gimme->edit_user_action->ok }}
    {{ if $gimme->edit_user_action->type == "add" }}
    <div class="message messageregister messagesuccess">
      Your profile has been created. Please check your e-mail for further information.
    </div>
    {{else }}
    <div class="message messageregister messagesuccess">
      Your profile has been created / edited
    </div>
    {{ /if }}
    {{ /if }}                

    </div><!--eightcol-->
    <div class="fourcol last">

{{ include file="_tpl/_banner300x250.tpl" }}

{{ include file="_tpl/sidebar-pages.tpl" }}

{{ include file="_tpl/sidebar-blogroll.tpl" }}
    </div><!--fourcol-->
  </div><!--row-->

  <div class="row" id="furtherarticles">
{{ include file="_tpl/article-mostread.tpl" }}
  </div><!--.row-->
    
{{ include file="_tpl/footer.tpl" }}
  
</div>

</body>
</html>