{{ include file="set_thejournal/_tpl/_html-head.tpl" }}

<body class="single single-post custom gecko">
<div id="wrap">

{{ include file="set_thejournal/_tpl/top.tpl" }}

    <div id="content" class="wrap"> 
    
    <div class="col-left">
      <div id="main">

                <div class="post wrap">

           <h2>Change password</h2>
           <br />
            {{ user_form submit_button="submit" template="set_thejournal/register.tpl" }}
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
    
{{ include file="set_thejournal/_tpl/_banner300x250.tpl" }}

    </div>
        
{{ include file="set_thejournal/_tpl/sidebar-pages.tpl" }}
    
{{ include file="set_thejournal/_tpl/sidebar-blogroll.tpl" }}
  
</div>

    </div><!-- Content Ends -->
    
{{ include file="set_thejournal/_tpl/footer.tpl" }}
  
</div>

</body>
</html>