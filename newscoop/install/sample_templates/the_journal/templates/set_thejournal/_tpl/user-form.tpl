            {{ user_form template="subscription.tpl" submit_button="submit" }}
            <p style="margin: 15px 0">Please fill in the following form in order to create the subscription account.</p>
            <table id="contact" cellspacing="0" cellpadding="0">
              <tr>
                  <td><label for="contact-name">Full name:</label></td>
                  <td>{{ camp_edit object="user" attribute="name" html_code="class=\"tablefield widerone\" id=\"contact-name\"" }}</td>
              </tr>
              <tr>
                  <td><label for="contact-email">E-mail:</label></td>
                  <td>{{ camp_edit object="user" attribute="email" html_code="class=\"tablefield widerone\" id=\"contact-email\"" }}</td>
                </tr>  
              <tr>
              <tr>
                  <td><label for="contact-uname">Username:</label></td>
                  <td>{{ camp_edit object="user" attribute="uname" html_code="class=\"tablefield widerone\" id=\"contact-uname\"" }}</td>
                </tr>  
              <tr>  
{{ if ! $gimme->user->logged_in }}                           
              <tr>
                  <td><label for="contact-passowrd">Password:</label></td>
                  <td>{{ camp_edit object="user" attribute="password" html_code="class=\"tablefield widerone\" id=\"contact-password\"" }}</td>
              </tr>                                                                                  
              <tr>
                  <td><label for="contact-passowrdagain">Password (again):</label></td>
                  <td>{{ camp_edit object="user" attribute="passwordagain" html_code="class=\"tablefield widerone\" id=\"contact-passwordagain\"" }}</td>
              </tr>                 
{{ /if }}              
              <tr>
                  <td><label for="contact-city">City, Country:</label></td>
                  <td>{{ camp_edit object="user" attribute="city" html_code="class=\"tablefield widerone\" id=\"contact-city\"" }}</td>
              </tr>                          
              <tr>
                  <td><label for="contact-phone">Phone:</label></td>
                  <td>{{ camp_edit object="user" attribute="phone" html_code="class=\"tablefield widerone\" id=\"contact-phone\"" }}</td>
              </tr> 
              <tr>
                  <td><label for="second_phone">Phone (cell):</label></td>
                  <td>{{ camp_edit object="user" attribute="second_phone" html_code="class=\"tablefield widerone\" id=\"second_phone\"" }}</td>
              </tr>       

              <input type="hidden" name="SubsType" value="paid" />                                
              
            </table>
            <div id="submitformdiv">
                {{ /user_form }}        
            </div> 
            
{{ if $gimme->user->logged_in }}<p style="margin: 15px 0"> To change your password, go <a href="{{ uri options="template user-chgpass.tpl" }}">here</a></p> 
{{ /if }}                   
                       