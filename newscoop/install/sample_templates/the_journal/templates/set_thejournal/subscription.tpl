{{ include file="_tpl/_html-head.tpl" }}

<body class="single single-post custom gecko">
<div id="wrap">

{{ include file="_tpl/top.tpl" }}

    <div id="content" class="wrap"> 
    
    <div class="col-left">
      <div id="main">

                <div class="post wrap">

           <h2>Add new subscription</h2>

            {{* debug user add/edit/subscribe 
            <h5>Edit_user_action:</h5>
            <p>{{ if $gimme->edit_user_action->defined }}defined{{ else }}not defined{{/if}}</p>
            <p>{{ if $gimme->edit_user_action->is_error }}is_error, code: {{ $gimme->edit_user_action->error_code }}, message: {{ $gimme->edit_user_action->error_message }}{{ else }}not error{{/if}}</p>
            <p>{{ if $gimme->edit_user_action->ok }}ok{{ else }}not ok{{/if}}</p>
            <p>type: {{ $gimme->edit_user_action->type }}</p>

            <h5>Edit_subscription_action:</h5>
            <p>{{ if $gimme->edit_subscription_action->defined }}defined{{ else }}not defined{{/if}}</p>
            <p>{{ if $gimme->edit_subscription_action->is_error }}is_error, code: {{ $gimme->edit_subscription_action->error_code }}, message: {{ $gimme->edit_subscription_action->error_message }}{{ else }}not error{{/if}}</p>            
            <p>{{ if $gimme->edit_subscription_action->ok }}ok{{ else }}not ok{{/if}}</p>
            <p>{{ if $gimme->edit_subscription_action->is_trial }}is_trial{{ else }}not trial{{/if}}</p>
            <p>{{ if $gimme->edit_subscription_action->is_paid }}is_paid{{ else }}not paid{{/if}}</p>
            *}}

{{* no user form submitted, no subscription form submitted: display the user form *}}
{{ if !$gimme->edit_user_action->defined
      && !$gimme->edit_subscription_action->defined }}
          {{ include file="_tpl/user-form.tpl" }}
{{ /if }}

{{* user form submitted with errors: display the error and the user form *}}
{{ if $gimme->edit_user_action->defined
      && $gimme->edit_user_action->is_error }}
          <h5 style="margin-bottom: 20px">There was an error submitting the account creation form:
          {{ $gimme->edit_user_action->error_message }}</h5>
          {{ include file="_tpl/user-form.tpl" }}
{{ /if }}

{{* user form submitted ok: display the subscription form *}}
{{ if $gimme->edit_user_action->defined
      && $gimme->edit_user_action->ok }}
          {{ include file="_tpl/subscription-form.tpl" }}
{{ /if }}

{{* subscription form submitted with errors: display the error and the submit form *}}
{{ if $gimme->edit_subscription_action->defined
      && $gimme->edit_subscription_action->is_error }}
          <p style="margin: 15px 0">There was an error submitting the subscription form:
          {{ $gimme->edit_subscription_action->error_message }}</p>
          {{ include file="_tpl/subscription-form.tpl" }}
{{ /if }}

{{* subscription form submitted ok: display success message *}}
{{ if $gimme->edit_subscription_action->defined
      && $gimme->edit_subscription_action->ok }}
          <p style="margin: 15px 0">Your subscription was created successfully.</p>
{{ /if }}            
                    
           <div class="fix"></div>
          
                </div>

            </div><!-- main ends -->
        </div><!-- .col-left ends -->

<div id="sidebar" class="col-right">

    <div class="sidebar-top">
    
{{ include file="_tpl/_banner300x250.tpl" }}

    </div>
        
{{ include file="_tpl/sidebar-pages.tpl" }}
    
{{ include file="_tpl/sidebar-blogroll.tpl" }}
  
</div>

    </div><!-- Content Ends -->
    
{{ include file="_tpl/footer.tpl" }}
  
</div>

</body>
</html>