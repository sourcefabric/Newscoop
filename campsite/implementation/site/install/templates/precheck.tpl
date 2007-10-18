{{ include file="html_header.tpl" }}

<form action="index.php" method="post" name="install_form">
<tr>
  <td valign="top">
    <table class="header" cellspacing="0" cellpadding="0">
    <tr>
      <td width="50%">
        <div class="title">Pre-installation Check</div>
      </td>
      <td width="50%">
        <div class="navigate"><input
        class="nav_button" type="button" value="Re-check"
	onclick="submitForm( install_form, 'precheck' );" /> &nbsp;
        <input
        class="nav_button" type="button" value="Next &#155;"
        onclick="submitForm( install_form, 'license' );" /></div>
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
            <div class="subtitle">System Requirements:</div>
          </td>
        </tr>
        <tr>
          <td width="40%">
            <div class="help">
              If any of these items are highlighted in red then please take
              actions to correct them. Failure to do so could lead to your
              Campsite installation not functioning correctly.
            </div>
          </td>
          <td width="60%">

          </td>
        </tr>
        </table>
        <div class="table_spacer"> </div>
        <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td colspan="2">
            <div class="subtitle">Recommended PHP Settings:</div>
          </td>
        </tr>
        <tr>
          <td width="40%">
            <div class="help">
              These settings are recommended for PHP in order to ensure full
              compatibility with Campsite. However, Campsite will still operate
              if your settings do not quite match the recommended.
            </div>
          </td>
          <td width="60%">

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
          <li>{{ $s.title }}
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
