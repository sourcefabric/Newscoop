{{ include file="_tpl/_html-head.tpl" }}

<body>

<div class="container">

{{ include file="_tpl/top.tpl" }}
  <div class="row">
    <div class="eightcol" id="content">
{{* debug user add/edit/subscribe 
            <h5>Edit_user_action:</h5>
            <p>Edit user action defined: {{ if $gimme->edit_user_action->defined }}defined{{ else }}not defined{{/if}}</p>
            <p>Edit user action error: {{ if $gimme->edit_user_action->is_error }}is_error, code: {{ $gimme->edit_user_action->error_code }}, message: {{ $gimme->edit_user_action->error_message }}{{ else }}not error{{/if}}</p>
            <p>Edit user action ok: {{ if $gimme->edit_user_action->ok }}ok{{ else }}not ok{{/if}}</p>
            <p>edit user action type: {{ $gimme->edit_user_action->type }}</p>

            <h5>Edit_subscription_action:</h5>
            <p>Subs action defined: {{ if $gimme->edit_subscription_action->defined }}defined{{ else }}not defined{{/if}}</p>
            <p>Subs action error: {{ if $gimme->edit_subscription_action->is_error }}is_error, code: {{ $gimme->edit_subscription_action->error_code }}, message: {{ $gimme->edit_subscription_action->error_message }}{{ else }}not error{{/if}}</p>            
            <p>Subs action ok: {{ if $gimme->edit_subscription_action->ok }}ok{{ else }}not ok{{/if}}</p>
            <p>Subs action {{ if $gimme->edit_subscription_action->is_trial }}is_trial{{ else }}not trial{{/if}}</p>
            <p>subs action {{ if $gimme->edit_subscription_action->is_paid }}is_paid{{ else }}not paid{{/if}}</p>

*}}
{{* no user form submitted, no subscription form submitted: display the user form *}}
{{ if !$gimme->edit_user_action->defined
      && !$gimme->edit_subscription_action->defined }}
    <h2 class="post-title">Create and Edit Your Profile</h2>
          {{ include file="_tpl/user-form.tpl" }}
{{ /if }}

{{* user form submitted with errors: display the error and the user form *}}
{{ if $gimme->edit_user_action->defined
      && $gimme->edit_user_action->is_error }}
    <h2 class="post-title">Create and Edit Your Profile</h2>
          
  <div class="message messageerror messagesubscription">There was an error submitting the account creation form:
          {{ $gimme->edit_user_action->error_message }}</div>
          {{ include file="_tpl/user-form.tpl" }}
{{ /if }}

{{* user form submitted ok: display the subscription form *}}
{{ if $gimme->edit_user_action->defined
      && $gimme->edit_user_action->ok }}
           <h2 class="post-title">Registration and Subscription</h2>
          {{ include file="_tpl/subscription-form.tpl" }}
{{ /if }}

{{* subscription form submitted with errors: display the error and the submit form *}}
{{ if $gimme->edit_subscription_action->defined
      && $gimme->edit_subscription_action->is_error }}
           <h2 class="post-title">Registration and Subscription</h2>
          <div class="message messageerror messagesubscription">There was an error submitting the subscription form:
          {{ $gimme->edit_subscription_action->error_message }}</div>
          {{ include file="_tpl/subscription-form.tpl" }}
{{ /if }}

{{* subscription form submitted ok: display success message *}}
{{ if $gimme->edit_subscription_action->defined
      && $gimme->edit_subscription_action->ok }}
          <div class="message messageinformation messagesubscription">Your subscription was created successfully.</div>
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