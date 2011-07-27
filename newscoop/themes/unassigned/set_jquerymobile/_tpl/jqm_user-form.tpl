 
{{ user_form template="jqm_register.tpl" submit_button="Submit" }}
{{* 
  if you want to work with subscriptions, replace the above line with the below line, 
  it will bring up the subscription form 
  {{ user_form template="jqm_subscription.tpl" submit_button="Submit" }}
*}}
    <div data-role="fieldcontain">
      Please fill in the following form in order to create the subscription account.
{{ if $gimme->user->logged_in }}
  <p>Or <a href="{{ uri options="template jqm_user-chgpass.tpl" }}">change your password</a>.</p> 
{{ /if }}  
    </div>
    <div data-role="fieldcontain">
      <label for="contact-name">Full name:</label>
      {{ camp_edit object="user" attribute="name" html_code=" id=\"contact-name\"" }}
    </div>
    <div data-role="fieldcontain">
      <label for="contact-email">E-mail:</label>
      {{ camp_edit object="user" attribute="email" html_code=" id=\"contact-email\"" }}
    </div>
    <div data-role="fieldcontain">
      <label for="contact-uname">Username:</label>
      {{ camp_edit object="user" attribute="uname" html_code=" id=\"contact-uname\"" }}
    </div>
{{ if ! $gimme->user->logged_in }} 
    <div data-role="fieldcontain">
      <label for="contact-password">Password:</label>
      {{ camp_edit object="user" attribute="password" html_code=" id=\"contact-password\"" }}
    </div>
    <div data-role="fieldcontain">
      <label for="contact-passwordagain">Password (again):</label>
      {{ camp_edit object="user" attribute="passwordagain" html_code=" id=\"contact-passwordagain\"" }}
    </div>
{{ /if }}
    <div data-role="fieldcontain">
      <label for="contact-city">City, Country:</label>
      {{ camp_edit object="user" attribute="city" html_code=" id=\"contact-city\"" }}
    </div>
    <div data-role="fieldcontain">
      <label for="contact-phone">Phone:</label>
      {{ camp_edit object="user" attribute="phone" html_code=" id=\"contact-phone\"" }}
    </div>
    <div data-role="fieldcontain">
      <label for="second_phone">Phone (cell):</label>
      {{ camp_edit object="user" attribute="second_phone" html_code=" id=\"second_phone\"" }} 
    </div>
      <input type="hidden" name="SubsType" value="paid" />  
      <a href="docs-dialogs.html" data-role="button" data-rel="back">Cancel</a> 
{{ /user_form }} 

                       