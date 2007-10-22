{{ include file="html_header.tpl" }}

<form action="index.php" method="post" name="install_form">
<tr>
  <td valign="top">
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
        onclick="submitForm( install_form, 'finish' );" /></div>
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
            <div class="subtitle">Site Title:</div>
          </td>
        </tr>
        <tr>
          <td width="40%" valign="top">
            <div class="help">
              If any of these items are highlighted in red.
            </div>
          </td>
          <td width="60%" valign="top">
            <div class="message">{{ $message }}</div>
            <div class="form_field">
              Site Title:<br />
              <input class="inputbox" type="text" size="42" maxlength="40"
              id="mc_sitename" name="mc_sitename" value="{{ $mc.sitename }}" />
            </div>
          </td>
        </tr>
        </table>
        <div class="table_spacer"> </div>
        <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td colspan="2">
            <div class="subtitle">Administrator Data:</div>
          </td>
        </tr>
        <tr>
          <td width="40%" valign="top">
            <div class="help">
              These settings are recommended for PHP in order to ensure full
              compatibility with Campsite.
            </div>
          </td>
          <td width="60%" valign="top">
            <div class="form_field">
              Administrator Password:<br />
              <input class="inputbox" type="password" size="42" maxlength="40"
              id="mc_adminpsswd" name="mc_adminpsswd" value="" /><br />
            </div>
            <div class="form_field">
              Confirm Password:<br />
              <input class="inputbox" type="password" size="42" maxlength="40"
              id="mc_admincpsswd" name="mc_admincpsswd" value="" /><br />
            </div>
            <div class="form_field">
              Administrator E-Mail:<br />
              <input class="inputbox" type="text" size="42" maxlength="40"
              id="mc_adminemail" name="mc_adminemail" value="{{ $mc.adminemail }}" /><br />
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
          {{ if $s.order < 4 }}
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
