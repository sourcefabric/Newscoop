{{ include file="_tpl/_html-head.tpl" }}

<body>

<div class="container">

{{ include file="_tpl/top.tpl" }}
  <div class="row">
    <div class="eightcol" id="content">

<h2 class="post-title">Change Your Password</h2>

{{ user_form submit_button="submit" template="register.tpl" }}

    <div class="formitem">
      <label for="contact-passowrd">Password:</label>
      {{ camp_edit object="user" attribute="password" html_code="class=\"tablefield widerone\" id=\"contact-password\"" }}
    </div>
    <div class="formitem">
      <label for="contact-passowrdagain">Password (again):</label>
      {{ camp_edit object="user" attribute="passwordagain" html_code="class=\"tablefield widerone\" id=\"contact-passwordagain\"" }}
    </div>

{{ /user_form }}             

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