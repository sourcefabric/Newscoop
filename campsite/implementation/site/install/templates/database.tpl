{{ include file="html_header.tpl" }}

<form action="index.php" method="post" name="install_form" autocomplete="off">
<tr>
  <td valign="top">
    <table class="header" cellspacing="0" cellpadding="0">
    <tr>
      <td width="50%">
        <div class="title">Database Settings</div>
      </td>
      <td width="50%">
        <div class="navigate"><input
        class="nav_button" type="button" value="&#139; Previous"
        onclick="submitForm( install_form, 'license' );" /> &nbsp;
        <input
        class="nav_button" type="button" value="Next &#155;"
        onclick="submitForm( install_form, 'mainconfig' );" /></div>
      </td>
    </tr>
    </table>
    <div class="table_spacer"> </div>
    <table class="inside" cellspacing="0" cellpadding="0">
    <tr>
      <td>
        <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td colspan="2">
            <div class="subtitle">Connection Parameters:</div>
          </td>
        </tr>
        <tr>
          <td width="40%" valign="top">
            <div class="help">
              Need to change this and talk about database settings.
            </div>
          </td>
          <td width="60%" valign="top">
            <div class="message">{{ $message }}</div>
            <div class="form_field">
              Server Name/Address:<br />
              <input class="inputbox" type="text" size="42" maxlength="40"
              id="db_hostname" name="db_hostname" value="{{ $db.hostname }}" /><br />
            </div>
            <div class="form_field">
              Server Port: (<em>Optional</em>)<br />
              <input class="inputbox" type="text" size="42" maxlength="40"
              id="db_hostport" name="db_hostport" value="{{ $db.hostport }}" /><br />
            </div>
            <div class="form_field">
              User Name:<br />
              <input class="inputbox" type="text" size="42" maxlength="40"
              id="db_username" name="db_username" value="{{ $db.username }}" /><br />
            </div>
            <div class="form_field">
              User Password:<br />
              <input class="inputbox" type="password" size="42" maxlength="40"
              id="db_userpass" name="db_userpass" value="{{ $db.userpass }}" /><br />
            </div>
            <div class="form_field">
              Database Name:<br />
              <input class="inputbox" type="text" size="42" maxlength="40"
              id="db_database" name="db_database" value="{{ $db.database }}" /><br />
            </div>
          </td>
        </tr>
        </table>
      </td>
    </tr>
    </table>
  </td>
  <td width="200" valign="top">
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
          {{ if $s.order < 3 }}
            <li class="stepdone">{{ $s.title }}</span>
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
<input type="hidden" name="step" value="" />
</form>
</table>

{{ include file="html_footer.tpl" }}
