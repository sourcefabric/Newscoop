{{ include file="_tpl/_html-head.tpl" }}
<body> 
  <div data-role="page">
    <div data-role="header" data-theme="a" data-position="inline">
      <h1>Change password</h1>
    </div>
    <div data-role="content">

{{ user_form submit_button="submit" template="jqm_register.tpl" }}
<label for="contact-password">Password:</label>
{{ camp_edit object="user" attribute="password" html_code="class=\"tablefield widerone\" id=\"contact-password\"" }}
<label for="contact-passwordagain">Password (again):</label>
{{ camp_edit object="user" attribute="passwordagain" html_code="class=\"tablefield widerone\" id=\"contact-passwordagain\"" }}
{{ /user_form }}  

    </div><!-- /content -->
  </div><!-- /body -->
</body>
</html>
