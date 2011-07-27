{{ user_form template="subscription.tpl" submit_button="submit" }}

  <div class="message messageinformation messagesubscription">
    Please fill in the following form in order to create the subscription account.
{{ if $gimme->user->logged_in }}
<p>Go here to <a href="{{ uri options="template user-chgpass.tpl" }}">change your password</a></p>
{{ /if }} 
  </div>

  <div class="formitem">
    <label for="contact-name">Full name:</label>
    {{ camp_edit object="user" attribute="name" html_code="class=\"tablefield widerone\" id=\"contact-name\"" }}
  </div>
  <div class="formitem">
    <label for="contact-email">E-mail:</label>
    {{ camp_edit object="user" attribute="email" html_code="class=\"tablefield widerone\" id=\"contact-email\"" }}
  </div>
  <div class="formitem">
    <label for="contact-uname">Username:</label>
    {{ camp_edit object="user" attribute="uname" html_code="class=\"tablefield widerone\" id=\"contact-uname\"" }}
  </div>

  {{ if ! $gimme->user->logged_in }}                           
    <div class="formitem">
      <label for="contact-passowrd">Password:</label>
      {{ camp_edit object="user" attribute="password" html_code="class=\"tablefield widerone\" id=\"contact-password\"" }}
    </div>
    <div class="formitem">
      <label for="contact-passowrdagain">Password (again):</label>
      {{ camp_edit object="user" attribute="passwordagain" html_code="class=\"tablefield widerone\" id=\"contact-passwordagain\"" }}
    </div>
  {{ /if }}              

  <div class="formitem">
    <label for="contact-city">City, Country:</label>
    {{ camp_edit object="user" attribute="city" html_code="class=\"tablefield widerone\" id=\"contact-city\"" }}
  </div>
  <div class="formitem">
    <label for="contact-phone">Phone:</label>
    {{ camp_edit object="user" attribute="phone" html_code="class=\"tablefield widerone\" id=\"contact-phone\"" }}
  </div>
  <div class="formitem">
    <label for="second_phone">Phone (cell):</label>
    {{ camp_edit object="user" attribute="second_phone" html_code="class=\"tablefield widerone\" id=\"second_phone\"" }}
  </div>

  <input type="hidden" name="SubsType" value="paid" />                                

{{ /user_form }}        
            
                  
                       