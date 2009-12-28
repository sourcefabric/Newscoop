{{ include file="html_header.tpl" }}
<script type="text/javascript" src="include/js/fValidate/fValidate.config.js"></script>
<script type="text/javascript" src="include/js/fValidate/fValidate.core.js"></script>
<script type="text/javascript" src="include/js/fValidate/fValidate.lang-enUS.js"></script>
<script type="text/javascript" src="include/js/fValidate/fValidate.validators.js"></script>
<form action="index.php" method="post" name="install_form" autocomplete="off">
<tr>
  <td width="100%" valign="top">
    <table class="header" cellspacing="0" cellpadding="0">
    <tr>
      <td width="50%">
        <div class="title">General Configuration</div>
      </td>
      <td width="50%">
        <div class="navigate"><input
          class="nav_button" type="button" value="&#139; Previous"
          onclick="submitForm( install_form, 'database' );" /> &nbsp;
          <input
          class="nav_button" type="button" value="Next &#155;"
          onclick="if (validateForm(install_form, 0, 1, 0, 1, 8) == true) {
                   submitForm(install_form, 'loaddemo'); }"/>
        </div>
      </td>
    </tr>
    </table>
    <div class="table_spacer"> </div>
    <table class="inside" cellspacing="0" cellpadding="0">
    <tr>
      <td>
        <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td colspan="3">
            <div class="subtitle">Site Title:</div>
          </td>
        </tr>
        <tr>
          <td width="35%" valign="top">
            <div class="help">
              Give your site a title. Be short and descriptive.
            </div>
          </td>
          <td width="5%">&nbsp;</td>
          <td width="60%" valign="top">
            <div class="message">{{ $message }}</div>
            <div class="form_field">
              <label for="Site_Title">Site Title</label>:<br />
              <input class="inputbox" type="text" size="42" maxlength="40"
              id="Site_Title" name="Site_Title" value="{{ $mc.sitetitle }}"
              alt="blank"
              emsg="You must complete the 'Site Title' field" />
            </div>
          </td>
        </tr>
        </table>
        <div class="table_spacer"> </div>
        <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td colspan="3">
            <div class="subtitle">Administrator Data:</div>
          </td>
        </tr>
        <tr>
          <td width="35%" valign="top">
            <div class="help">
              <p><em>admin</em> is the system administrator user created
              automatically by <em>Campsite</em>. Please, set a strong and
              secure password for it at this step, confirm that password
              and input a valid e-mail address.</p>

              <p>REMEMBER: After installation, you will be able to log into
              the administration interface by typing <em>admin</em> as
              username and the password you set in this step.</p>
            </div>
          </td>
          <td width="5%">&nbsp;</td>
          <td width="60%" valign="top">
            <div class="form_field">
              <label for="Admin_Password">Administrator Password</label>:<br />
              <input class="inputbox" type="password" size="42" maxlength="40"
              id="Admin_Password" name="Admin_Password" value=""
              alt="blank"
              emsg="You must complete the 'Administrator Password' field" />
            </div>
            <div class="form_field">
              <label for="Confirm_Password">Confirm Password</label>:<br />
              <input class="inputbox" type="password" size="42" maxlength="40"
              id="Confirm_Password" name="Confirm_Password" value=""
              alt="equalto|Admin_Password" />
            </div>
            <div class="form_field">
              <label for="Admin_Email">Administrator E-Mail</label>:<br />
              <input class="inputbox" type="text" size="42" maxlength="40"
              id="Admin_Email" name="Admin_Email" value="{{ $mc.adminemail }}"
              alt="blank"
              emsg="You must complete the 'Administrator E-Mail' field" />
            </div>
          </td>
        </tr>
        </table>
      </td>
    </tr>
    </table>
  </td>
  <td valign="top">
    <table class="right_header" cellspacing="0" cellpadding="0">
    <tr>
      <td>
        <div class="title">Progress...</div>
      </td>
    </tr>
    </table>
    <div class="table_spacer"> </div>
    <table class="right" cellspacing="0" cellpadding="0">
    <tr>
      <td>
        <ul id="steps_list">
        {{ foreach from=$step_titles key="step" item="s" }}
          {{ if $s.order < 4 }}
            <li class="stepdone">{{ $s.title }}
          {{ else }}
            <li>{{ $s.title }}
          {{ /if }}
          {{ if $s.title eq $current_step_title }}
            &nbsp; <img src="img/checked.png" />
          {{ /if }}
          </li>
        {{ /foreach }}
        </ul>
      </td>
    </tr>
    </table>
    <div class="table_spacer"> </div>
    <div align="center">
      <img src="img/installation-progress.png" />
    </div>
  </td>
</tr>
</table>
<input type="hidden" name="step" value="" />
</form>

{{ include file="html_footer.tpl" }}
