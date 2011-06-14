{{ include file="_tpl/_html-head.tpl" }}

<body class="single single-post custom gecko">
<div id="wrap">

{{ include file="_tpl/top.tpl" }}

    <div id="content" class="wrap"> 
    
    <div class="col-left">
      <div id="main">

                <div class="post wrap">

                    <h2 class="post-title">Register/Modify user data</h2>
                    
            {{ if !$gimme->edit_user_action->defined && !$gimme->edit_subscription_action->defined }}
              {{ include file="_tpl/user-form.tpl" }}
            {{ /if }}

            {{ if $gimme->edit_user_action->defined && $gimme->edit_user_action->is_error }}
              <h5>Error registering an account: {{ $gimme->edit_user_action->error_message }}</h5>
              {{ include file="_tpl/user-form.tpl" }}
            {{ /if }}
            
            {{ if $gimme->edit_user_action->defined && $gimme->edit_user_action->ok }}
              {{ if $gimme->edit_user_action->type == "add" }}
              <h5>User account was added successfully. Soon you will receive a report about access to information.</h5>
              {{else }}
              <h5>User data successfully added/modified</h5>
              {{ /if }}
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