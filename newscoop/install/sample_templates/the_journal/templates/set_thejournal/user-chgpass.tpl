{{ include file="_tpl/_html-head.tpl" }}

<body class="single single-post custom gecko">
<div id="wrap">

{{ include file="_tpl/top.tpl" }}

    <div id="content" class="wrap"> 
    
    <div class="col-left">
      <div id="main">

                <div class="post wrap">

           <h2>Change password</h2>
           <br />
            {{ user_form submit_button="submit" template="register.tpl" }}
            <table id="contact" cellspacing="0" cellpadding="0">
              <tr>
                  <td><label for="contact-passowrd">Password:</label></td>
                  <td>{{ camp_edit object="user" attribute="password" html_code="class=\"tablefield widerone\" id=\"contact-password\"" }}</td>
              </tr>                                                                                  
              <tr>
                  <td><label for="contact-passowrdagain">Password (again):</label></td>
                  <td>{{ camp_edit object="user" attribute="passwordagain" html_code="class=\"tablefield widerone\" id=\"contact-passwordagain\"" }}</td>
              </tr> 
            </table>
            <div style="margin: 15px 0;">
                {{ /user_form }}        
            </div>             
                    
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